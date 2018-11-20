<?php

use yii\helpers\Html;
use app\components\AccessControl;
use yii\helpers\Url;

$this->title = $this->context->module->title;
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
		'filterModel' => $searchModel,
        'layout' => '{items}{actions}{pager}{summary}',
        'columns' => [
			[
				'class' => 'yii\grid\CheckboxColumn', 
				'checkboxOptions'=>[
					'class'=>'js-grid-checkbox',
				]
			],
            'id',
			[
				'attribute' => 'event_id',
				'value' => function ($model, $index, $widget) {
					return \app\components\Event::getTitle($model->event_id);
				},
				'filter' => Html::activeDropDownList($searchModel, 'event_id', \app\components\Event::getList(), ['class' => 'form-control', 'prompt' => '- выбрать -']),
			],
            'subject',
            'from_email',
            'from_name',
			[
				'attribute' => 'content_type',
				'value' => function ($model, $index, $widget) {
					return \app\components\Event::getTitleContentType($model->content_type);
				},
				'filter' => Html::activeDropDownList($searchModel, 'content_type', \app\components\Event::getListContentType(), ['class' => 'form-control', 'prompt' => '- выбрать -']),
			],			
            app\helpers\Grid::checkboxValueColumn($searchModel, 'is_active'),
            app\helpers\Grid::checkboxValueColumn($searchModel, 'is_instant'),
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{view}{update}{delete}',
                'options' => ['width' => '50px']
            ]
        ],
    ]);
?>	
    
<?php \yii\widgets\Pjax::end(); ?>