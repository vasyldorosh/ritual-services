<?php 
$this->title = 'Просмотр контентного блока: ' . ' ' . $model->id;

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
            'attribute' => 'created_at',
            'value' => \app\helpers\Grid::datetimeValue($model->created_at),
        ],		
        'content:html',
    ],
]) ?>

<?=\app\widgets\Buttons::actions($model->id);?>