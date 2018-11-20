<?php
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use app\widgets\ActiveForm;

$form = ActiveForm::begin([
	'options' => [
		'enctype' => 'multipart/form-data',
		'class' => 'form-horizontal'
	],
]);

$tabsItems = [
	[
		'label' => 'Пользователь админки',
		'content' => $this->render('_form', ['model' => $model, 'form' => $form, 'profile'=>$profile]),
		'active' => true
	],
	[
		'label' => 'Почтовые уведомления',
		'content' => $this->render('_events', ['model' => $model, 'form' => $form]),
	]
];

echo Tabs::widget([
    'items' => $tabsItems,
]);


?>  

    <?=\app\widgets\Buttons::formSubmit($model);?>	

<?php ActiveForm::end(); ?>