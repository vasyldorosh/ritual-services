<?php 
$this->title = 'Просмотр домена: ' . ' ' . $model->id;

echo yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'alias',
        [                     
            'attribute' => 'is_active',
            'value' => \app\helpers\Grid::checkboxValue($model->is_active),
        ],		
        [                     
            'attribute' => 'is_root',
            'value' => \app\helpers\Grid::checkboxValue($model->is_root),
        ],
		[                     
			'attribute' => 'lang',
			'value' => $model->getLangTitle(),
		],
		[
			'attribute'=>'logo',
			'value'=>$model->getImageUrl('logo'),
			'format' => ['image',['width'=>'80','height'=>'50']],
		],		
		[
			'attribute'=>'logo_hover',
			'value'=>$model->getImageUrl('logo_hover'),
			'format' => ['image',['width'=>'80','height'=>'50']],
		],	
		'logo_width',
		'logo_height',
		'btn_title',
		[                     
            'attribute' => 'phone_type',
            'value' => $model->getPhoneTypeTitle(),
        ],
		'phone',		
    ],
]) ?>

<?=\app\widgets\Buttons::actions($model->id);?>