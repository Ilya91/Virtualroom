<?php

namespace app\models;

use yii\redis\Cache;

/**
 * Class User
 * @package app\models
 */
class User extends Cache
{
    public $name;
    public $handState;

    public function getUser($id)
    {
        return $this->getValue('global:classroom:users:' . $id);
    }

    public function setUser($key, $value)
    {
        return $this->setValue($key, $value, 0);
    }
}
