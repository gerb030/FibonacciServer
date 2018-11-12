<?php

class Controller_Api extends Controller_Abstract
{
    private $_userMapper;
    private $_pokerroundMapper;
    private $_pokerroundUserMapper;

    /**
     * @param Request $request
     * @param Config $config
     */
    public function __construct(Request $request, Config $config)
    {
        $this->setRequest($request);
        $this->setConfig($config);
    }

     public function handleRequest()
     {
        header('Content-type: application/json; charset=utf-8');
        try {
            $pdo = DbFactory::build();
            $this->_userMapper = new Mapper_User($pdo);
            $this->_pokerroundMapper = new Mapper_Pokerround($pdo);
            $this->_pokerroundUserMapper = new Mapper_PokerroundUser($pdo);

            $request = $this->getRequest();
            $languageCode = $request->getProperty('languageCode');

            // TODO: authenticate user
            $requestUser = null;
            if ($request->getProperty('user') != null) {
                $userId = self::cleanString($request->getProperty('user'));
                $requestUser = $this->_userMapper->find(array('username' => $userId));
                if ($requestUser == null) {
                    throw new Exception("User not found");
                }
            }
            
            $response = array();
            switch($request->getProperty('method')) {
                case 'create_user':
                    $userName = self::cleanString($request->getProperty('username'));
                    $email = self::cleanString($request->getProperty('email'));
                    $user = new Domain_User();
                    $user->setUsername($userName);
                    $user->setEmailAddress($email);
                    // check if user or email already exists
                    $user = $this->_userMapper->find(array('user' => $userName, 'emailaddress' => $email));
                    var_export($user);
                    die();

                    $user = $this->_userMapper->save($user);
                    return $user;
                                
                case 'new':
                    $pokerround = new Domain_Pokerround();
                    $pokerround->setOwnerusername($requestUser->getUsername());
                    $sessionKey = $this->_createSessionKey();
                    $pokerround->setSession($sessionKey);
                    $pokerround->setPokerroundUsers(array());
                    $this->_pokerroundMapper->save($pokerround);
                    $pokerround = $this->_addNewPokerroundUserAndPersist($pokerround, $requestUser);
                    $response['response'] = $pokerround->toArray();
                    break;
                case 'join':
                    $pokerround = $this->_fetchPokerround($request->getProperty('session'));
                    // User already joined?
                    $pokerroundUsers = $pokerround->getPokerroundUsers();
                    foreach($pokerroundUsers as $pokerroundUser) {
                        if ($pokerroundUser->getUserId() == $requestUser->getId()) {
                            throw new Exception_Http("You are already in the session", 409);                            
                        }
                    }
                    $pokerround = $this->_addNewPokerroundUserAndPersist($pokerround, $requestUser);
                    $response['response'] = $pokerround->toArray();
                    break;
                case 'vote':
                    $pokerround = $this->_fetchPokerroundForParticipant($request->getProperty('session'), $requestUser);
                    if ($pokerround->getClosed()) {
                        throw new Exception_Http("This round does not accept votes at this time", 500);
                    }
                    if ($request->getProperty('vote') != null) {
                        $vote = self::cleanString($request->getProperty('vote'));
                    }
                    if (!in_array($vote, array("C", "?", "0", "1", "2", "3", "5", "8", "13", "20"), true)) {
                        throw new Exception_Http("Not a valid vote value", 500);
                    }
                    // User already joined?
                    $found = false;
                    $allHaveVoted = true;
                    $pokerroundUsers = $pokerround->getPokerroundUsers();
                    foreach($pokerroundUsers as $pokerroundUser) {
                        if ($pokerroundUser->getUserId() == $requestUser->getId()) {
                            $found = true;
                            $pokerroundUser->setVoted($vote);
                            $this->_pokerroundUserMapper->save($pokerroundUser);
                        } else {
                            if ($pokerroundUser->getVoted() == null) {
                                $allHaveVoted = false;
                            }
                        }
                    }
                    // here's a security issue - users can enter a valid session key and learn if a session exists
                    if (!$found) {
                        throw new Exception_Http("General error occurred", 500);
                    }
                    if ($allHaveVoted) {
                        $pokerround = $this->_closePokerround($pokerround);
                    }
                    $response['response'] = $pokerround->toArray();
                    break;
                case 'close':
                    $session = $request->getProperty('session');
                    $pokerround = $this->_fetchPokerroundForOwner($session, $requestUser);
                    if ($pokerround->getClosed()) {
                        throw new Exception_Http("Already closed", 500);
                    }
                    $pokerround = $this->_closePokerround($pokerround);
                    $response['response'] = $pokerround->toArray();
                    break;
                case 'poll':
                    $pokerround = $this->_fetchPokerroundForParticipant($request->getProperty('session'), $requestUser);
                    $response['response'] = $pokerround->toArray();
                    break;
                case 'reset':
                    $session = $request->getProperty('session');
                    $pokerround = $this->_pokerroundMapper->find(array('session' => $session));
                    if ($pokerround->getOwnerusername() != $requestUser->getUsername()) {
                        throw new Exception_Http("You are not the owner of this round", 401);
                    }
                    $pokerround = $this->_resetPokerround($pokerround);
                    break;
                case 'kick':
                    // TODO: kick
                    break;
            }
            $json = json_encode($response);
            print $json;
        } catch (Exception $e) {
            Logger::error($e);
            // render final page
            throw $e;
        }

    }


    /**
     * Close a pokerround - users are then unable to vote
     */
    private function _closePokerround(Domain_Pokerround $pokerround) {
        $pokerround->setClosed(true);
        $this->_pokerroundMapper->save($pokerround);
        return $pokerround;
    }

    /**
     * Reset a pokerround - users are able to vote again
     */
    private function _resetPokerround(Domain_Pokerround $pokerround) {
        $pokerround->setClosed(false);
        $this->_pokerroundMapper->save($pokerround);
        return $pokerround;
    }

    /**
     * Fetch the poker round for the owner user
     */
    private function _fetchPokerroundForOwner($session, Domain_User $requestUser) {
        $pokerround = $this->_pokerroundMapper->find(array('session' => $session, 'owneruser' => $requestUser->getUsername()));
        if ($pokerround == null || (gettype($pokerround) == 'array' && count($pokerround) == 0)) {
            throw new Exception_Http("Poker round with this session id not found", 404);
        }
        return $pokerround;
    }

    /**
     * Fetch the poker round for a participating user
     */
    private function _fetchPokerroundForParticipant($session, Domain_User $requestUser) {
        $pokerround = $this->_fetchPokerround($session);
        $pokerroundUsers = $pokerround->getPokerroundUsers();
        foreach($pokerroundUsers as $pokerroundUser) {
            if ($pokerroundUser->getUserId() == $requestUser->getId()) {
                $found = true;
            }
        }
        // here's a security issue - users can enter a valid session key and learn if a session exists
        if (!$found) {
            throw new Exception_Http("General error occurred", 500);
        }
        return $pokerround;
    }

    /**
     * Fetch a poker round based on only the session key
     */
    private function _fetchPokerround($session) {
        $pokerround = $this->_pokerroundMapper->find(array('session' => $session));
        if ($pokerround == null || (gettype($pokerround) == 'array' && count($pokerround) == 0)) {
            throw new Exception_Http("Poker round with this session id not found", 404);
        }
        return $pokerround;
    }


    private function _createSessionKey() {
        $newKey = $this->_generateKey();
        $pokerround = $this->_pokerroundMapper->find(array('session' => $newKey));
        while ( !(is_array($pokerround) && count($pokerround) == 0) ) {
            $newKey = $this->_generateKey();
            $pokerround = $this->_pokerroundMapper->find(array('session' => $newKey));
        }
        return $newKey;
    }

    private function _generateKey() {
        $newKey = random_int(0, 99999);
        if (strlen($newKey) < 5) {
            $newKey = str_pad($newKey, 6-strlen($newKey), "0", STR_PAD_LEFT);
        }
        return $newKey;
    }

    private function _addNewPokerroundUserAndPersist(Domain_Pokerround $pokerround, Domain_User $requestUser) {
        // Create a new Pokerround User to add to the poker round
        $pokerroundUser = new Domain_PokerroundUser();
        $pokerroundUser->setUserId($requestUser->getId());
        $pokerroundUser->setUserName($requestUser->getUsername());
        $pokerroundUser->setPokerroundId($pokerround->getId());
        $pokerroundUser->setVoted(null);
        // Save it     
        $this->_pokerroundUserMapper->save($pokerroundUser);
        // Add it        
        $pokerround->addUser($pokerroundUser);       
        return $pokerround;
    }

    function cleanString($input) {
        if (ord($input) == 63) { return $input; }
        return preg_replace("/[^A-Za-z0-9_-]+/", "", $input);
    }
}
