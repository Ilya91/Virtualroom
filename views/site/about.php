<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use app\models\User;

$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
        $obj = (object)['name' => 'Born', 'handState' => 0];
        $redis = Yii::$app->redis;
        //$redis->sadd('global:classroom:users', 'userID:'. rand() . ':' .  serialize($obj));

        //$data = $redis->smembers("global:classroom:users");

        $user = new User();
        $data = $user->getUser('mykey');
        var_dump($data);
        ?>
    </p>
</div>
