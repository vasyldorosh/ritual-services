<?php 
$this->title = 'Просмотр слайда слайдера: ' . ' ' . $model->id;

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
            'attribute' => 'slider_id',
            'value' => isset($model->slider) ? $model->slider->title : '',
        ],		
        'title',
        [                     
            'attribute' => 'is_active',
            'value' => \app\helpers\Grid::checkboxValue($model->is_active),
        ],	
        [                     
            'attribute' => 'is_button',
            'value' => \app\helpers\Grid::checkboxValue($model->is_button),
        ],	
		'button_text',
		'rank',
		'url',
		[                     
            'attribute' => 'publish_time',
            'value' => \app\helpers\Grid::datetimeValue($model->publish_time),
        ],	
		'description',
		'content:html',
    ],
]) ?>

<?=\app\widgets\Buttons::actions($model->id);?>