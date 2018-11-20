<?php

use yii\helpers\Html;
use app\components\AccessControl;
use yii\helpers\Url;
use app\modules\structure\models\Archive;

$searchModel = new Archive;
$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

?>

<?php \yii\widgets\Pjax::begin(['id' => 'pjax-grid', 'timeout' => false, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]); ?>
  
<?php echo app\components\GridView::widget([
		'id' => 'event-grid',
		'dataProvider' => $dataProvider,
        'layout' => '{items}',
        'columns' => [
			[
				'attribute' => 'id',
				'enableSorting' => false,
			],		   
			[
				'attribute' => 'created_at',
				'format' 	=>  ['date', 'php:Y-m-d H:i:s'],
				'options' 	=> ['width' => '200'],
				'enableSorting' => false,
			],		   
            [
                'class' => 'app\components\ActionColumn',
                'template' 	=> '{restore}', //{delete}
				'access' 	=> 'structure.archive.',
                'options' 	=> ['width' => '50px'],
				'buttons' => [
						'restore' => function ($url, $model) {
							return Html::a('Востановить', ['/structure/archive/restore', 'id'=>$model->id], [
								'title' => 'Востановить',
								'class' => 'js-archive-restore',
							]);
						},
						'delete' => function ($url, $model) {
							return Html::a('<span class="icon-trash"></span>', ['/structure/archive/delete', 'id'=>$model->id], [
								'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
								'data-method' => 'post',
								'data-pjax' => '0',	
								'title' => 'Удалить',
							]);
						}
					],				
            ]			
        ],
    ]);
?>	
    
<?php \yii\widgets\Pjax::end(); ?>