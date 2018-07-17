<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
<div class="row">
    <div class="absolute-center is-responsive">
        <div class="col-sm-12 col-md-10 col-md-offset-1">
            <?php $form = ActiveForm::begin([
            ]); ?>
            <label for="loginform-name">Your Name</label>
            <?= $form->field($model, 'name')->textInput()->label(false) ?>

            <?= Html::submitButton('Login', ['class' => 'btn btn-primary btn-block', 'name' => 'login-button']) ?>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
</div>