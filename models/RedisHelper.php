<?php

namespace app\models;

use yii\redis\Cache;

/**
 * Class User
 * @package app\models
 */
class RedisHelper extends Cache
{

    public function setUpdateTs()
    {
        return $this->setValue('global:classroom:updateTs', time(), 0);
    }
}
