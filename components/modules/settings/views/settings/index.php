<?php
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use app\widgets\ActiveForm;

$this->title = 'Настройки';


$form = ActiveForm::begin([
	'options' => [
		'enctype' => 'multipart/form-data',
		'class' => 'form-horizontal'
	],
]);

	$tabsItems = [
		[
			'label' => 'Порядок модулей',
			'content' => $this->render('_modules', ['values' => $values]),
			'active' => true
		],
		[
			'label' => 'Отправка пошты',
			'content' => $this->render('_email', ['values' => $values]),
		],
		[
			'label' => 'Контент блоки',
			'content' => $this->render('_content_block', ['values' => $values]),
		],
		[
			'label' => 'Меню',
			'content' => $this->render('_menu', ['values' => $values]),
		],
		[
			'label' => 'Карта сайта',
			'content' => $this->render('_sitemap', ['values' => $values]),
		],
		[
			'label' => 'Соц. сети',
			'content' => $this->render('_social', ['values' => $values]),
		],
	];

	echo Tabs::widget([
		'items' => $tabsItems,
	]);


	?>  


	<button type="submit" class="btn btn-success">Сохранить</button>

<?php ActiveForm::end(); ?>