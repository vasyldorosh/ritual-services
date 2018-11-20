<?php

use yii\helpers\Html;
use app\components\AccessControl;
use yii\helpers\Url;

$this->title = 'Пользователи админки';
?>

<?php \yii\widgets\Pjax::begin(['id' => 'pjax-grid', 'timeout' => false, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]); ?>
  
<?=\app\widgets\Buttons::create()?>  
  
<?php echo app\components\GridView::widget([
		'id' => 'admin-grid',
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
				'attribute' => 'group_id',
				'value' => function ($model, $index, $widget) {
					return isset($model->group) ? $model->group->title : '';
				},
				'filter' => Html::activeDropDownList($searchModel, 'group_id', \app\modules\admin\models\Group::getList(), ['class' => 'form-control', 'prompt' => '- выбрать -']),
			],
            'name',
            'email',
			app\helpers\Grid::dateValueColumn($searchModel, 'register_at'),
			app\helpers\Grid::dateValueColumn($searchModel, 'auth_at'),
            'auth_ip',
            app\helpers\Grid::checkboxValueColumn($searchModel, 'is_active'),
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{login}{view}{update}{delete}',
				'buttons' => [
					'login' => function ($url, $model, $key) {
						if (session()->get("root_id") == $model->id) {
							return '';
						}
						
						return (is_super_admin() && admin()->id != $model->id) ? Html::a('Логин', $url, ['class'=>'_blank']) : '';
					},			
				],
                'options' => ['width' => '50px']
            ]
        ],
    ]);
?>	
    
<?php \yii\widgets\Pjax::end(); ?>