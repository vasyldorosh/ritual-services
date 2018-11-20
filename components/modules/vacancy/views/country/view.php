<?php 
$this->title = 'Просмотр страны: ' . ' ' . $model->id;

echo yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',	
        'title',
        'alias',
        [                     
            'attribute' => 'is_active',
            'value' => \app\helpers\Grid::checkboxValue($model->is_active),
        ],	
		'rank',
		'seo_title',
		'seo_description',
    ],
]) ?>

<?=\app\widgets\Buttons::actions($model->id);?>