<?php 
$this->title = 'Просмотр очереди: ' . ' ' . $model->id;

echo yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        [                     
            'attribute' => 'event_id',
            'value' => isset($model->event) ? $model->event->subject : '',
        ],		
        'subject',
		'content:html',
        [                     
            'attribute' => 'status',
            'value' => \app\components\Event::getTitleSpoolStatus($model->status),
        ],
		'email_to',
    ],
]) ?>