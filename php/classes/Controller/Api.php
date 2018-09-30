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
            $playerMapper = new Mapper_Player($pdo);
            $pokerroundMapper = new Mapper_Pokerround($pdo);
            $pokerroundPlayerMapper = new Mapper_PokerroundPlayer($pdo);

            $request = $this->getRequest();

            $user = $request->getProperty('user');
            echo $user;

            $languageCode = $request->getProperty('languageCode');

//            $json = json_encode(array('packages' => $packages, 'promotions' => $promotionsJson));

            print $json;
        } catch (Exception $e) {
            Logger::error($e);
            // render final page
            echo json_encode(array("error" => $e));
        }

    }
}
