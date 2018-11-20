<?php

use yii\helpers\Html;
use app\components\AccessControl;
use yii\helpers\Url;
use app\helpers\Page as PageHelper;

$this->title = 'Домены';
?>

<?=\app\widgets\Buttons::create();?>

<?php \yii\widgets\Pjax::begin(['id' => 'pjax-grid', 'timeout' => false, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]); ?>
  
<?php echo app\components\GridView::widget([
		'id' => 'event-grid',
		'dataProvider' => $dataProvider,
		'actions' => [
			'activate' => [
				'label' => 'Активировать',
				'class' => 'btn-info',
				'url' => Url::toRoute(['multipleActivate']),
			],
			'deactivate' => [
				'label' => 'Деактивировать',
				'class' => 'btn-warning',
				'url' => Url::toRoute(['multipleDeactivate']),
			],
			'delete' => [
				'label' => 'Удалить',
				'class' => 'btn-danger',
				'url' => Url::toRoute(['multipleDelete']),
			]
		],
		'exports' => [
			'excel' => [
				'label' => 'Excel',
				'class' => 'btn-info',
				'url' => Url::toRoute(['excel']),
			],
			'csv' => [
				'label' => 'Csv',
				'class' => 'btn-info',
				'url' => Url::toRoute(['csv']),
			],
		],
		'filterModel' => $searchModel,
        'layout' => '{items}{actions}{exports}{pager}{summary}',
        'columns' => [
			[
				'class' => 'yii\grid\CheckboxColumn', 
				'checkboxOptions'=>[
					'class'=>'js-grid-checkbox',
				]
			],
            'id',
			[
				'attribute'=> 'logo',
				'value'	=> function ($model, $index, $widget) {
					return $model->getImageUrl('logo');
				},
				'format'=> 'image',
				'filter'=> false,
			],			
			[
				'attribute'=> 'logo_hover',
				'value'	=> function ($model, $index, $widget) {
					return $model->getImageUrl('logo_hover');
				},
				'format'=> 'image',
				'filter'=> false,
			],			
			'alias',
			[
				'attribute' => 'template_id',
				'value' => function ($model, $index, $widget) {
					return PageHelper::getTemplateTitle($model->template_id);
				},
				'filter' => Html::activeDropDownList($searchModel, 'template_id', PageHelper::getTemplatesList(), ['class' => 'form-control', 'prompt' => '- выбрать -']),
			],
			[
				'attribute' => 'lang',
				'value' => function ($model, $index, $widget) {
					return $model->getLangTitle();
				},
				'filter' => Html::activeDropDownList($searchModel, 'lang', Yii::$app->params['languages'], ['class' => 'form-control', 'prompt' => '- выбрать -']),
			],
		    app\helpers\Grid::checkboxValueColumn($searchModel, 'is_active'),
		    app\helpers\Grid::checkboxValueColumn($searchModel, 'is_root'),
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{view}{update}{delete}{log}',
                'options' => ['width' => '50px']
            ]
        ],
    ]);
?>	
    
<?php \yii\widgets\Pjax::end(); ?>