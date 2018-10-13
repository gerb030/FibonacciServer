<?php

class Controller_Api extends Controller_Abstract
{
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
            $userMapper = new Mapper_User($pdo);
            $pokerroundMapper = new Mapper_Pokerround($pdo);
            $pokerroundUserMapper = new Mapper_PokerroundUser($pdo);

            $request = $this->getRequest();
            $languageCode = $request->getProperty('languageCode');
            
            // TODO: authenticate user
            $requestUser = null;
            if ($request->getProperty('user') != null) {
                $userId = self::cleanString($request->getProperty('user'));
                $requestUser = $userMapper->find(array('username' => $userId));
                if ($requestUser == null) {
                    throw new Exception("User not found");
                }
            }
            
            $response = array();
            switch($request->getProperty('method')) {
                case 'new':
                    $pokerround = new Domain_Pokerround();
                    $pokerround->setOwnerusername($requestUser->getUsername());
                    $sessionKey = $this->_createSessionKey($pokerroundMapper);
                    $pokerround->setSession($sessionKey);
                    $pokerround->setPokerroundUsers(array());
                    $pokerroundMapper->save($pokerround);
                    $pokerround = $this->_addNewPokerroundUserAndPersist($pokerround, $requestUser, $pokerroundUserMapper);
                    $response['response'] = $pokerround->toArray();
                    break;
                case 'join':
                    $session = $request->getProperty('session');
                    $pokerround = $pokerroundMapper->find(array('session' => $session));
                    if ($pokerround == null || (gettype($pokerround) == 'array' && count($pokerround) == 0)) {
                        throw new Exception_Http("Poker round with this session id not found", 404);
                    }
                    // User already joined?
                    $pokerroundUsers = $pokerround->getPokerroundUsers();
                    foreach($pokerroundUsers as $pokerroundUser) {
                        if ($pokerroundUser->getUserId() == $requestUser->getId()) {
                            throw new Exception_Http("You are already in the session", 409);                            
                        }
                    }
                    $pokerround = $this->_addNewPokerroundUserAndPersist($pokerround, $requestUser, $pokerroundUserMapper);
                    $response['response'] = $pokerround->toArray();
                    break;
                case 'vote':
                    break;
                case 'poll':
                    break;
                case 'kick':
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


    private function _createSessionKey(Mapper_Pokerround $pokerroundMapper) {
        $newKey = $this->_generateKey();
        $pokerround = $pokerroundMapper->find(array('session' => $newKey));
        while ( !(is_array($pokerround) && count($pokerround) == 0) ) {
            $newKey = $this->_generateKey();
            $pokerround = $pokerroundMapper->find(array('session' => $newKey));
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

    private function _addNewPokerroundUserAndPersist(Domain_Pokerround $pokerround, Domain_User $requestUser, Mapper_PokerroundUser $pokerroundUserMapper) {
        // Create a new Pokerround User to add to the poker round
        $pokerroundUser = new Domain_PokerroundUser();
        $pokerroundUser->setUserId($requestUser->getId());
        $pokerroundUser->setUserName($requestUser->getUsername());
        $pokerroundUser->setPokerroundId($pokerround->getId());
        $pokerroundUser->setVoted(null);
        // Save it     
        $pokerroundUserMapper->save($pokerroundUser);
        // Add it        
        $pokerround->addUser($pokerroundUser);       
        return $pokerround;
    }

    function cleanString($input) {
        return preg_replace("/[^A-Za-z0-9_-]+/", "", $input);
    }
}
