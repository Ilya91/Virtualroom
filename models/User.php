<?php

namespace app\models;

use Yii;
use yii\redis\Cache;

/**
 * Class User
 * @package app\models
 */
class User
{
    public $name;
    public $handState = 0;
    public $redis;

    public function __construct()
    {
        $this->redis = Yii::$app->redis;
    }

    public function getAllUsersInSet()
    {
        return $this->redis->smembers('global:classroom:users');
    }

    public function getUsersAsArray()
    {
        $members = $this->getAllUsersInSet();
        $array = [];

        foreach ($members as $member) {
            $array[] = unserialize(substr(stristr($member, ':'), 1));
        }

        return $array;
    }

    public function deleteUserFromSet($id, $obj)
    {
        $this->redis->srem('global:classroom:users', $id . ':' .  serialize($obj));
    }

    public function addUserToSet($id, $obj)
    {
        $this->redis->sadd('global:classroom:users', $id . ':' .  serialize($obj));
    }

    public function addUser($id, $obj)
    {
        $this->redis->sadd('global:classroom:users:' . $id,  serialize($obj));
    }

    public function getUserById($id)
    {
        if ($id){

        }
        return false;
    }
}
