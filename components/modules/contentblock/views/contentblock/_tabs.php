<?php
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use app\widgets\ActiveForm;
use app\components\imageAdaptive\Manager;

$form = ActiveForm::begin([
	'options' => [
		'enctype' => 'multipart/form-data',
		'class' => 'form-horizontal'
	],
]);

$tabsItems = [[
	'label' => 'Контентный блок',
    'content' => $this->render('_form', ['model' => $model, 'form' => $form]),
    'active' => true
]];

foreach (Yii::$app->params['otherLanguages'] as $lang=>$langTitle) {
	$tabsItems[] = [
		'label' => $lang,
		'content' => $this->render('_form_lang', ['model' => $model, 'form' => $form, 'lang'=>$lang]),
	];	
}
		foreach (Manager::getAttributesData(\yii\helpers\StringHelper::basename(get_class($model))) as $attribute=>$attrOption) {
			$tabsItems[] = array(
				'label'=>$attrOption['label'],
				'content'=>$this->renderFile(Yii::getAlias('@app').'/components/imageAdaptive/views/imageAttributeManager.php', [ 
					'model'=>$model, 
					'attribute'=>$attribute, 
					'attributeConfig'=>$attrOption,
				]),
			);				
		}

echo Tabs::widget([
    'items' => $tabsItems,
]);


?>  

<?=\app\widgets\Buttons::formSubmit($model);?>	
	
<?php ActiveForm::end(); ?>