<?php 
$this->title = 'Просмотр редиректа: ' . ' ' . $model->id;

echo yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',	
        'pattern',
        'url',
        [                     
            'attribute' => 'is_active',
            'value' => \app\helpers\Grid::checkboxValue($model->is_active),
        ],		
        [                     
            'attribute' => 'is_lang',
            'value' => \app\helpers\Grid::checkboxValue($model->is_lang),
        ],		
        'rank',
    ],
]) ?>

<?=\app\widgets\Buttons::actions($model->id);?>