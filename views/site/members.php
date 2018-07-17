<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use app\models\User;

$this->title = 'Members';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <p>
        <?php
            $redis = Yii::$app->redis;
            $members = $model->getUsersAsArray();
            var_dump(Yii::$app->session['id']);
            var_dump(Yii::$app->session['user']);
        ?>
    </p>
        <div class="col-sm-12 col-md-8 col-md-offset-2">
            <div class="list-group">
                <a href="#" class="list-group-item disabled">
                    Class Members
                </a>
                <?php foreach ( $members as $member) :?>
                    <a href="#" class="list-group-item"><?= $member->name;?> <i class="glyphicon <?= $member->handState ? "glyphicon-hand-up" : "";?> pull-right"></i></a>
                <?php endforeach;?>
            </div>
    </div>
</div>
