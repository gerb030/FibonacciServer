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

            $response = array();
            switch($request->getProperty('method')) {
                case 'new':
                    break;
                case 'join':
                    $session = $request->getProperty('session');
                    $pokerround = $pokerroundMapper->find(array('session' => $session));
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
            echo json_encode(array("error" => $e));
        }

    }
}
