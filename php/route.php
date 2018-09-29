<?php
/*

API commands

Get all active games for one user
/api/login/[user]
POST: password
POST command

Command: issue a new challenge
/api/start_session/[user_id]
* cookie should be included
return session_id

Command: get one game 
/api/join_session/[session_id]/[user_id]
GET command
* cookie should be included

Command: issue a new challenge
/api/play/[session_id]/[[user_id]]/[value]
* cookie should be included
*/

switch (strtolower($_GET['scope'])) {
    case 'api':
        $params = explode("/", $_GET['parameters']);
        $results = array();
        switch($params[0]) {
            case 'login':
                // TODO
                break;
            case 'start_session':
                $results = array('gameId' => 23424, 'startDate' => '2018-05-16 00:00:00', 'turn' => 'gerb', 'player1' => 'gerb', 'letters' => array('_','_','_', '_', '_'), 'mistakes' => 0);
                break;
            case 'join_session':
                $results = array('gameId' => 23424, 'startDate' => '2018-05-16 00:00:00', 'turn' => 'gerb', 'player1' => 'gerb', 'letters' => array('_','_','_', '_', '_'), 'mistakes' => 0);
                break;
            case 'play':
                array_push($results, array('gameId' => 23784, 'startDate' => '2018-05-16 00:00:00', 'player1' => 'gerb', 'player2' => 'other', 'turn' => 'gerb', 'letters' => array('_','_','_', '_', 'e')));
                array_push($results, array('gameId' => 23445, 'startDate' => '2018-05-16 00:00:00', 'player1' => 'gerb', 'player2' => 'other', 'turn' => 'other', 'letters' => array('_','_','e', 'e', '_', 'e', 'r')));
                $results = array('gameId' => 23424, 'startDate' => '2018-05-16 00:00:00', 'turn' => 'gerb', 'player1' => 'gerb', 'player2' => 'other', 'letters' => array('_','_','_', '_', '_'), 'mistakes' => 0);
                break;
            case 'get_session':
                $results = array('gameId' => 23424, 'startDate' => '2018-05-16 00:00:00', 'turn' => 'gerb', 'player1' => 'gerb', 'player2' => 'other', 'letters' => array('_','_','_', '_', '_'), 'mistakes' => 0);
                break;
            default:
                // TODO
                header("HTTP/1.0 404 Not Found");
                break;
        }
        echo json_encode($results);
        break;
    case 'facebook':
    default:
        header("HTTP/1.0 404 Not Found");
        echo "<hr>GET<br>";
        print_r($_GET);
        echo "<hr>POST<br>";
        print_r($_POST);
        break;
}
