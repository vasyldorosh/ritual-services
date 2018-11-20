<?php 
$this->title = 'Просмотр события: ' . ' ' . $model->id;

echo yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        [                     
            'attribute' => 'event_id',
            'value' => \app\components\Event::getTitle($model->event_id),
        ],		
        'subject',
        'from_email',
        'from_name',
        [                     
            'attribute' => 'is_active',
            'value' => \app\helpers\Grid::checkboxValue($model->is_active),
        ],		
        [                     
            'attribute' => 'is_instant',
            'value' => \app\helpers\Grid::checkboxValue($model->is_instant),
        ],		
        [                     
            'attribute' => 'content_type',
            'value' => \app\components\Event::getTitleContentType($model->content_type),
        ],		
        'content:html',
    ],
]) ?>

<?=\app\widgets\Buttons::actions($model->id);?>