<?php 
$this->title = 'Просмотр меню: ' . ' ' . $model->id;

echo yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',	
        'title',
    ],
]) ?>

<?=\app\widgets\Buttons::actions($model->id);?>