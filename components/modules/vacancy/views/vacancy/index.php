<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\AccessControl;
use app\modules\vacancy\models\Country;

$this->title = 'Вакансии';
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
				'attribute' => 'image',
				'value'     => function ($model, $index, $widget)
					{
					return $model->getImageUrl('image', 140, 50, 'crop');
					},
				'format' => 'image',
				'filter' => false,
			],			
            [
				'attribute' => 'country_id',
				'value' => function ($model, $index, $widget) {
					return isset($model->country) ? $model->country->title : '';
				},
				'filter' => Country::getList(),
			],			
            'title',
            'alias',
            app\helpers\Grid::checkboxValueColumn($searchModel, 'is_active'),
            'work_time',
            'salary',
            'rank',
			app\helpers\Grid::dateValueColumn($searchModel, 'create_time'),
            'seo_description',
            'seo_title',
	        [
                'class' => 'app\components\ActionColumn',
                'template' => '{view}{update}{delete}{log}',
                'options' => ['width' => '50px']
            ]
        ],
    ]);
?>	
    
<?php \yii\widgets\Pjax::end(); ?>