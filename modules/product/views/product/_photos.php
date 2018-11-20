<?php if (!empty($model->id)) :?>
    <?= \app\widgets\vstab\Manager::widget([
		'modelName' 		=> 'ProductPhoto',
		'fullModelName'		=> '\app\modules\product\models\ProductPhoto',
		'items' 			=> $model->getPhotos(),
		'model_id' 			=> $model->id,
		'labelItem' 		=> 'Фото',
		'labelCreate' 		=> 'Создание фото',
		'labelUpdate' 		=> 'Редактирование фото',
		'modelAttribute' 	=> 'product_id',
		'inputAttributes' 	=> [
            'title' => [],
         ],
		'imageAttributes'	=> [
            'image' => [],
        ],
		'labelAttributes' 	=> (new \app\modules\product\models\ProductPhoto)->attributeLabels(),
	]);?>

<?php else:?>
	<p>Доступно после сохранения.</p>
<?php 
endif?>	