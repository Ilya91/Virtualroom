<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use app\models\User;
use yii\widgets\Pjax;
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
            $members = $model->getUsersAsArray();

            /*var_dump(Yii::$app->session['id']);
            var_dump($members);*/

            $users = $model->getUserInSetById(Yii::$app->session['id']);
            var_dump($redis->keys('global:classroom:users*'));
        ?>
    </p>
        <div class="col-sm-12 col-md-8 col-md-offset-2">
            <div class="list-group">
                <a href="#" class="list-group-item disabled">
                    Class Members
                </a>
                <?php foreach ( $members as $member) :?>
                    <a href="#" class="list-group-item" data-key="<?= $member->id;?>"><?= $member->name;?> <i class="glyphicon <?= $member->handState ? "glyphicon-hand-up" : "";?> pull-right"></i></a>
                <?php endforeach;?>
            </div>




            <h3>Notifications:</h3>

            <p id="notify">&nbsp;</p>

            <h3>Operations:</h3>

            <form class="form-horizontal">
                <div class="control-group">
                    <label class="control-label" for="channels">Active channel:</label>
                    <div class="controls">
                        <select class="channels"></select>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="unsub"></label>
                    <div class="controls">
                        <button class="btn btn-danger" type="button" id="unsub">Unsubscribe</button>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="sub">Subscribe:</label>
                    <div class="controls">
                        <input type="text" id="sub" placeholder="">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="pub">Publish (websocket):</label>
                    <div class="controls">
                        <input type="text" id="pub" placeholder="">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="redispub">Publish (server Redis):</label>
                    <div class="controls">
                        <input type="text" id="redispub" placeholder="">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="response"><strong>Response:</strong></label>
                    <div class="controls">
                        <textarea id="response" rows="10"></textarea>
                    </div>
                </div>
            </form>

        </div>
</div>
