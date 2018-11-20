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
		'label' => 'Редирект',
		'content' => $this->render('_form', ['model' => $model, 'form' => $form]),
		'active' => true
	],
];

echo Tabs::widget([
    'items' => $tabsItems,
]);


?>  

    <?=\app\widgets\Buttons::formSubmit($model);?>	

<?php ActiveForm::end(); ?>