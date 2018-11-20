<?php
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use app\widgets\ActiveForm;
use app\modules\menu\models\MenuLink;

$model = new MenuLink;

$form = ActiveForm::begin([
	'enableClientScript' => false,
	'options' => [
		'enctype' => 'multipart/form-data',
		'class' => 'form-horizontal',
		'id' => 'menu-item-form',
	],
]);

$tabsItems = [[
	'label' => 'Пункт меню',
    'content' => $this->render('_item_form', ['model' => $model, 'form' => $form]),
    'active' => true
]];

foreach (Yii::$app->params['otherLanguages'] as $lang=>$langTitle) {
	$tabsItems[] = [
		'label' => $lang,
		'content' => $this->render('_item_form_lang', ['model' => $model, 'form' => $form, 'lang'=>$lang]),
	];	
}

echo Tabs::widget([
    'items' => $tabsItems,
]);


?>  

<?php ActiveForm::end(); ?>