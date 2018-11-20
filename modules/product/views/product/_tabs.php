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

$tab = r()->get('tab', 'main');

$tabsItems = [[
	'label' => 'Товар',
    'content' => $this->render('_form', ['model' => $model, 'form' => $form]),
    'active' => $tab=='main'
]];

foreach (Yii::$app->params['otherLanguages'] as $lang=>$langTitle) {
	$tabsItems[] = [
		'label' => $lang,
		'content' => $this->render('_form_lang', ['model' => $model, 'form' => $form, 'lang'=>$lang]),
	];	
}

$tabsItems[] = [
    'label' => 'Фото',
    'content' => $this->render('_photos', ['model' => $model, 'form' => $form]),
];	

echo Tabs::widget([
    'items' => $tabsItems,
]);

?>  

<?=\app\widgets\Buttons::formSubmit($model);?>	
	
<?php ActiveForm::end(); ?>