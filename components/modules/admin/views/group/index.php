<?php

use yii\helpers\Html;
use app\components\AccessControl;
use yii\helpers\Url;

$this->title = 'Группы пользователей';
?>

<?=\app\widgets\Buttons::create();?>

<?php \yii\widgets\Pjax::begin(['id' => 'pjax-grid', 'timeout' => false, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]); ?>
  
<?php echo app\components\GridView::widget([
		'id' => 'group-grid',
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
            'title',
            app\helpers\Grid::checkboxValueColumn($searchModel, 'is_active'),
            app\helpers\Grid::checkboxValueColumn($searchModel, 'is_super'),
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{view}{update}{delete}',
                'options' => ['width' => '50px']
            ]
        ],
    ]);
?>	
    
<?php \yii\widgets\Pjax::end(); ?>