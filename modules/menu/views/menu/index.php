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
			'title',
			[
				'attribute' => 'type',
				'value' => function ($model, $index, $widget) {
					return $model->getTypeTitle();
				},
				'filter' => Html::activeDropDownList($searchModel, 'type', \app\modules\menu\models\Menu::getTypes(), ['class' => 'form-control', 'prompt' => '- выбрать -']),
			],
            app\helpers\Grid::checkboxValueColumn($searchModel, 'is_active'),
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{view}{update}{delete}',
                'options' => ['width' => '50px']
            ]
        ],
    ]);
?>	
    
<?php \yii\widgets\Pjax::end(); ?>