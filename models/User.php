<?php

namespace app\models;

use Yii;
use yii\helpers\Json;

/**
 * Class User
 * @package app\models
 */
class User
{
    public $id;
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

    public function isUserExist($name)
    {
        $users = $this->getAllUsers();

        foreach ($users as $user) {
            if ($user['name'] === $name){
                return true;
            }
        }
        return false;
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

    public function addUser($id, $user)
    {
        $this->redis->set('global:classroom:users:' . $id,  $user);
    }

    public function updateUser($id, $user)
    {
        $this->redis->set('global:classroom:users:' . $id,  Json::encode($user));
    }

    public function getUserById($id)
    {
        if ($id){
            return Json::decode($this->redis->get('global:classroom:users:' . $id));
        }
        return false;
    }

    public function getUserByKey($key)
    {
        if ($key){
            return Json::decode($this->redis->get($key));
        }
        return false;
    }

    public function getUserInSetById($id)
    {
        $members = $this->getAllUsersInSet();

        foreach ($members as $member) {
            if ($id == stristr($member, ':', true)){
                return unserialize(substr(stristr($member, ':'), 1));
            }
        }

        return false;
    }

    public function getAllUsers()
    {
        $keys = $this->redis->keys('global:classroom:users:*');
        $users = [];
        foreach ($keys as $key) {
            $users[] = Json::decode($this->redis->get($key));
        }
        return $users;
    }

    public function deleteUser($id)
    {
        $this->redis->del('global:classroom:users:' . $id);
    }
}
