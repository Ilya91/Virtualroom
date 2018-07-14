<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
        $redis = Yii::$app->redis;
        $redis->sadd("global:classroom:users", "hell");

        $data = $redis->smembers('global:classroom:users');
        var_dump($data);
        ?>
    </p>

    <code><?= __FILE__ ?></code>
</div>
