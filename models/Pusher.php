<?php

namespace app\models;

use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;
use SplObjectStorage;
use Yii;
use yii\helpers\Json;


class Pusher implements WampServerInterface {
    /**
     * A lookup of all the topics clients have subscribed to
     */
    public $subscribedTopics = ['__keyspace@0__:global:classroom:*'];
    protected $redis;
    protected $clients;

    public function __construct() {
        $this->clients = new SplObjectStorage;
    }
    public function init($client) {
        $this->redis = $client;
        echo "Connected to Redis, now listening for incoming messages...\n";
    }


    public function onSubscribe(ConnectionInterface $conn, $topic) {
        echo "Pusher: onSubscribe\n";
        echo "Pusher: topic: $topic {$topic->count()}\n";

        $this->redis->psubscribe('__keyspace@0__:global:classroom:*', function ($event) use ($topic){
            if (in_array("set", $event)){
                $model = new User();
                if (in_array('__keyspace@0__:global:classroom:updateTs', $event)){
                    $members = $model->getAllUsers();
                    $response = [
                        'type' => 'class_config_changed',
                        'members' => $members
                    ];
                    $response = Json::encode($response);
                    $topic->broadcast($response);

                }else {
                    foreach ($event as $item) {
                        if (strlen($item) > 40){
                            $student = $model->getUserByKey('global:classroom:users:' . substr($item, strlen('__keyspace@0__:global:classroom:users:')));
                            $response = [
                                'type' => 'student_state_changed',
                                'student' => $student
                            ];
                            $response = Json::encode($response);
                            $topic->broadcast($response);
                        }
                    }
                }
            }
        });
    }
    /**
     * @param string
     */
    public function pubsub($event, $pubsub) {
        echo "Pusher: pubsub\n";
        echo "Pusher: kind: $event->kind channel: $event->channel payload: $event->payload\n";
        if (!array_key_exists($event->channel, $this->subscribedTopics)) {
            //echo "Pusher: no subscribers, no broadcast\n";
            return;
        }
        $topic = $this->subscribedTopics[$event->channel];
        echo "Pusher: $event->channel: $event->payload {$topic->count()}\n";

        // quit if we get the message from redis
        if (strtolower(trim($event->payload)) === 'quit') {
            echo "Pusher: quitting...\n";
            $pubsub->quit();
        }
    }
    public function onUnSubscribe(ConnectionInterface $conn, $topic) {
        echo "Pusher: onUnSubscribe\n";
        echo "Pusher: topic: $topic {$topic->count()}\n";
    }
    public function onOpen(ConnectionInterface $conn) {
        echo "Pusher: onOpen\n";
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }
    public function onClose(ConnectionInterface $conn) {
        echo "Pusher: onClose\n";
    }
    public function onCall(ConnectionInterface $conn, $id, $topic, array $params) {
        // In this application if clients send data it's because the user hacked around in console
        echo "Pusher: onCall\n";
        $conn->callError($id, $topic, 'You are not allowed to make calls')->close();
    }
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) {
        // In this application if clients send data it's because the user hacked around in console
        echo "Pusher: onPublish\n";
        $topic->broadcast("$event");
    }
    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Pusher: onError\n";
    }
}