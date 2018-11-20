<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\widgets\LoginForm */

$this->title = Yii::$app->name.Yii::t('modules/admin/main', ' - Вход в систему');

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-envelope form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>

<div class="auth-box">
	<h1>Авторизация</h1>

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

        <?= $form
            ->field($model, 'email', $fieldOptions1)
            ->label(false)
            ->textInput(['placeholder' => $model->getAttributeLabel('email')]) ?>

        <?= $form
            ->field($model, 'password', $fieldOptions2)
            ->label(false)
            ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>
		
		<div class="auth-controls">
			<input checked="checked" class="remember-me" name="LoginForm[rememberMe]" id="login-form-rememberMe" value="1" type="checkbox">		
			<label class="remember-label" for="login-form-rememberMe">Remember Me</label>		
			<span class="separator">|</span> 
			<a class="forgot-link" href="/?r=admin/auth/remind">Напомнить пароль</a>	
		</div>		
		
		<button class="btn btn-inverse login" type="submit">Войти</button>
		
        <?php ActiveForm::end(); ?>


</div>
