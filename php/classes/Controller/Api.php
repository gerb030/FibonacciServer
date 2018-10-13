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

            $requestUser = null;
            if ($request->getProperty('user') != null) {
                $userId = self::cleanString($request->getProperty('user'));
                $requestUser = $userMapper->find(array('username' => $userId));
            }
            
            $response = array();
            switch($request->getProperty('method')) {
                case 'new':
                    break;
                case 'join':
                    if ($requestUser == null) {
                        throw new Exception("User not set");
                    }
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
                    // Create a new Pokerround User to add to the poker round
                    $pokerroundUser = new Domain_PokerroundUser();
                    $pokerroundUser->setUserId($requestUser->getId());
                    $pokerroundUser->setUserName($requestUser->getUsername());
                    $pokerroundUser->setPokerroundId($pokerround->getId());
                    $pokerroundUser->setVoted(0);
                    // Save it     
                    $pokerroundUserMapper->save($pokerroundUser);
                    // Add it        
                    $pokerround->addUser($pokerroundUser);       
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

    function cleanString($input) {
        return preg_replace("/[^A-Za-z0-9_-]+/", "", $input);
    }
}
