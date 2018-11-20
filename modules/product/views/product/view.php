<?php 
$this->title = 'Просмотр товара: ' . ' ' . $model->id;

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
            'attribute' => 'category_id',
            'value' => isset($model->category) ? $model->category->title : '',
        ],		
        'title',
        'alias',
        [                     
            'attribute' => 'is_active',
            'value' => \app\helpers\Grid::checkboxValue($model->is_active),
        ],	
		'price',
		'rank',
		'seo_title',
		'seo_description',
		'content:html',
    ],
]) ?>

<?=\app\widgets\Buttons::actions($model->id);?>