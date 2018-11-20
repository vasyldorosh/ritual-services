<?php

use yii\helpers\Html;
use app\components\AccessControl;
use yii\helpers\Url;

$this->title = 'Очередь';
?>

<?php \yii\widgets\Pjax::begin(['id' => 'pjax-grid', 'timeout' => false, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]); ?>
  
<?php echo app\components\GridView::widget([
		'id' => 'event-spool-grid',
		'dataProvider' => $dataProvider,
		'actions' => [
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
			/*
			[
				'attribute' => 'event_id',
				'value' => function ($model, $index, $widget) {
					return isset($model->event) ? $model->event->subject : '';
				},
				'filter' => app\modules\event\models\Event::getList(),
			],
			*/
            'subject',
			'content:html',
			'email_to',
			[
				'attribute' => 'status',
				'value' => function ($model, $index, $widget) {
					return \app\components\Event::getTitleSpoolStatus($model->status);
				},
				'filter' => \app\components\Event::getListSpoolStatus(),
			],
			[
				'attribute' => 'created_at',
				'format' 	=>  ['date', 'php:Y-m-d H:i:s'],
				'filter'	=> false,
			],
			[
				'attribute' => 'send_at',
				'value' => function ($model, $index, $widget) {
					return !empty($model->send_at)?date('Y-m-d H:i:s', $model->send_at):'';
				},
				'filter'	=> false,
			],
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{view}{delete}',
                'options' => ['width' => '50px']
            ]
        ],
    ]);
?>	
    
<?php \yii\widgets\Pjax::end(); ?>