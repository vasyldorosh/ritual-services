<?php
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use app\widgets\ActiveForm;
use app\modules\structure\models\Page;

$model = new Page;

$form = ActiveForm::begin([
	'enableClientScript' => false,
	'options' => [
		'enctype' => 'multipart/form-data',
		'class' => 'form-horizontal',
		'id' => 'page-form',
	],
]);

$tabsItems = [[
	'label' => 'Страница',
    'content' => $this->render('_form', ['model' => $model, 'form' => $form]),
    'active' => true
]];

foreach (Yii::$app->params['otherLanguages'] as $lang=>$langTitle) {
	$tabsItems[] = [
		'label' => $lang,
		'content' => $this->render('_form_lang', ['model' => $model, 'form' => $form, 'lang'=>$lang]),
	];	
}

echo Tabs::widget([
    'items' => $tabsItems,
]);


?>  

<?php ActiveForm::end(); ?>