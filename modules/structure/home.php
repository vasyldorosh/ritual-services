<?php 

return [
	[	
		'title' => 'Страницы',
		'model' => '\app\modules\structure\models\Page',
		'action'=> 'structure.page',
	],
	[	
		'title' => 'Домены',
		'model' => '\app\modules\structure\models\Domain',
		'action'=> 'structure.domain',
	],
	[	
		'title' => 'Архив страниц',
		'model' => '\app\modules\structure\models\Archive',
		'action'=> 'structure.archive',
	],
];