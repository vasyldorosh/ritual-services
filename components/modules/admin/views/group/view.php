<?php 
$this->title = 'Просмотр группы пользователей админки: ' . ' ' . $model->id;

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
            'attribute' => 'is_super',
            'value' => \app\helpers\Grid::checkboxValue($model->is_super),
        ],		
    ],
]) ?>

<?=\app\widgets\Buttons::actions($model->id);?>