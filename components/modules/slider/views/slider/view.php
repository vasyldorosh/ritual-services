<?php 
$this->title = 'Просмотр слайдера: ' . ' ' . $model->id;

echo yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',	
        'title',
        [                     
            'attribute' => 'is_active',
            'value' => \app\helpers\Grid::checkboxValue($model->is_active),
        ],	
		[                     
            'attribute' => 'publish_time',
            'value' => \app\helpers\Grid::datetimeValue($model->publish_time),
        ],		
    ],
]) ?>

<?=\app\widgets\Buttons::actions($model->id);?>