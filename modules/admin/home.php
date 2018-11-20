<?php 

return [
	[	
		'title' => 'Админы',
		'model' => '\app\modules\admin\models\Admin',
		'action'=> 'admin.admin',
	],
	[	
		'title' => 'Группы',
		'model' => '\app\modules\admin\models\Group',
		'action'=> 'admin.group',
	],
];
?>