<?php 
$this->title = 'Просмотр вакансии: ' . ' ' . $model->id;

echo yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
		[
			'attribute'=>'image',
			'value'=>$model->getImageUrl('image', 140, 50, 'crop'),
			'format' => ['image',['width'=>'140','height'=>'50']],
		],			
        [                     
            'attribute' => 'country_id',
            'value' => isset($model->country) ? $model->country->title : '',
        ],		
        'title',
        'alias',
        [                     
            'attribute' => 'is_active',
            'value' => \app\helpers\Grid::checkboxValue($model->is_active),
        ],	
		'work_time',
		'salary',
		'rank',
		[                     
            'attribute' => 'create_time',
            'value' => \app\helpers\Grid::datetimeValue($model->create_time),
        ],	
		'seo_title',
		'seo_description',
		'content:html',
    ],
]) ?>

<?=\app\widgets\Buttons::actions($model->id);?>