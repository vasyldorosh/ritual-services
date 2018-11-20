<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\widgets\LoginForm */

$this->title = Yii::$app->name.Yii::t('modules/admin/main', ' - Напоминание пароля');

$fieldOptions = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-envelope form-control-feedback'></span>"
];
?>

<div class="auth-box">
	<h1 style="width: 500px;">Напоминание пароля</h1>	
		
        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

        <?= $form
            ->field($model, 'email', $fieldOptions)
            ->textInput(['placeholder' => $model->getAttributeLabel('email')]) ?>

        
		<button class="btn btn-inverse login" type="submit">Отправить</button>
        
		<?php ActiveForm::end(); ?>

</div>
