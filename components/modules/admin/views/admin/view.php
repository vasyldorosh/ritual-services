<?php 
$this->title = 'Просмотр пользователя админки: ' . ' ' . $model->id;

echo yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        [                     
            'attribute' => 'group_id',
            'value' => isset($model->group) ? $model->group->title : '',
        ],		
        'email',
        'name',
        [                     
            'attribute' => 'is_active',
            'value' => \app\helpers\Grid::checkboxValue($model->is_active),
        ],		
		[                     
            'attribute' => 'register_at',
            'value' => \app\helpers\Grid::datetimeValue($model->register_at),
        ],		
		[                     
            'attribute' => 'auth_at',
            'value' => \app\helpers\Grid::datetimeValue($model->auth_at),
        ],
        'auth_ip',
    ],
]) ?>

<?=\app\widgets\Buttons::actions($model->id);?>