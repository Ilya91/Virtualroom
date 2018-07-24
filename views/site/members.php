<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use app\models\User;
use app\assets\MemberAsset;

MemberAsset::register($this);

$this->title = 'Members';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">

    <div class="row">
        <div class="col-lg-12 col-md-12 col-xs-12">
                <?= Html::a('Raise hand up/down',
                    [
                        '/site/raise'
                    ],
                    [
                        'id'    => 'raise_hand',
                        'class' => 'btn btn-success'
                    ]) ?>
        </div>
    </div>
    <p>
        <?php
            $redis = Yii::$app->redis;
            $members = $model->getAllUsers();

            $user = new User();
            $users = $user->getUserById(Yii::$app->session['id']);
        ?>
    </p>
        <div class="col-sm-12 col-md-8 col-md-offset-2">
            <div class="list-group">
                <a href="#" class="list-group-item disabled">
                    Class Members
                </a>
                <div class="list-members">
                    <?php foreach ( $members as $member) :?>
                        <a href="#" class="list-group-item" data-key="<?= $member->id;?>"><?= $member->name;?> <i class="glyphicon <?= $member->handState ? "glyphicon-hand-up" : "";?> pull-right"></i></a>
                    <?php endforeach;?>
                </div>
            </div>
        </div>
</div>
