<?php

namespace app\commands;

use app\models\Socket;
use yii\console\Controller;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;


class SocketController extends Controller
{

    public function actionIndex()
    {
        $server = IoServer::factory(
                new WsServer(
                    new Socket()
                ),
            8081
        );

        $server->run();
    }
}
