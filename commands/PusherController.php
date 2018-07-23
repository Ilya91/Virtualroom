<?php

namespace app\commands;

use app\models\Pusher;
use Ratchet\Server\IoServer;
use yii\console\Controller;
use React\EventLoop\Factory;
use Predis\Async\Client;
use React\Socket\Server;
use Ratchet\WebSocket\WsServer;
use Ratchet\Wamp\WampServer;


class PusherController extends Controller
{

    public function actionIndex()
    {
        $loop   = Factory::create();
        $pusher = new Pusher();
        $client = new Client('tcp://127.0.0.1:6379', $loop);
        $client->connect(array($pusher, 'init'));
// Set up our WebSocket server for clients wanting real-time updates
        $webSock = new Server($loop);
        $webSock->listen(8087, '127.0.0.1');
        $webServer = new IoServer(
            new WsServer(
                new WampServer(
                    $pusher
                )
            ),
            $webSock
        );
        echo "Pusher starting...\n";
        $loop->run();
    }
}
