<?php

use yii\helpers\Html;
use app\components\AccessControl;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;

$this->title = 'Лог';
?>

<?php \yii\widgets\Pjax::begin(['id' => 'pjax-grid', 'timeout' => false, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]); ?>
  
<?php echo app\components\GridView::widget([
		'id' => 'admin-grid',
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
        'layout' => '{items}{actions}{pager}{summary}',
        'columns' => [
			'id',
			[
				'attribute' => 'admin_id',
				'value' => function ($model, $index, $widget) {
					return isset($model->admin) ? $model->admin->name_email : '';
				},
				'filter' => app\modules\admin\models\Admin::getList(),
			],
        	'action',
        	'model_id',
			[
				'attribute' => 'created_at',
				'format' 	=>  ['date', 'php:Y-m-d H:i:s'],
				'options' 	=> ['width' => '200']			
			],
			'ip',
        ],
    ]);
?>	
    
<?php \yii\widgets\Pjax::end(); ?>