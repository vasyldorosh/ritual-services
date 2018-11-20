<?php 

return [
	[	
		'title' => 'Шаблоны',
		'model' => '\app\modules\event\models\Event',
		'action'=> 'event.event',
	],
	[	
		'title' => 'Почтовая очередь',
		'model' => '\app\modules\event\models\EventSpool',
		'action'=> 'event.spool',
	],
];
?>