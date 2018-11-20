<?php

return [
    'languages' => [
		'ua' => 'українська', 
	],
    'otherLanguages' => [
	],
	'events' => [
		1 => [
			'title' => 'Напоминание пароля (ссылка для генерации пароля)',
			'vars' => 'username, email, link',
		],	
		2 => [
			'title' => 'Напоминание пароля (отправка пароля)',
			'vars' => 'username, email, password',
		],	
	],
    'templates' => [
        1 => [
			'alias' => 'home',
            'title' => 'Шаблон главной',
		],
        2 => [
			'alias' => 'style',
            'title' => 'Шаблон стилевой',
		],
	],
	'urlAliases' => [
		[
			'key' => '/^catalog\/([^\/]*)\/([^\/]*)\/?$/',
			'attributes' => [
				'vars' => 'category, alias',
				'mode' => 'dinamic',
				'type' => URL_TRANSFORMER_DYNAMIC,
				'path' => 'catalog/category/view',
			 ],
		],			
		[
			'key' => '/^catalog\/([^\/]*)\/?$/',
			'attributes' => [
				'vars' => 'alias',
				'mode' => 'dinamic',
				'type' => URL_TRANSFORMER_DYNAMIC,
				'path' => 'catalog/category',
			 ],
		],					
	],
	
	'page404' => '404',
	'colors' => require(__DIR__ . '/color.php'),
];
