<?php 

return [
   'title' => 'Вакансии',
   'items' => [
        'vacancy' => [
            'title' => 'Вакансии',
            'actions' => [
                'vacancy.vacancy.index'		=> 'Список',
                'vacancy.vacancy.create'	=> 'Создание',
                'vacancy.vacancy.update'	=> 'Редактирование',
                'vacancy.vacancy.delete'	=> 'Удаление',    
                'vacancy.vacancy.view'		=> 'Просмотр',    
                'vacancy.vacancy.activate'	=> 'Активация',    
                'vacancy.vacancy.deactivate'=> 'Деактивация',    
            ], 
			'items' => [
				'country' => [
					'title' => 'Страны',
					'actions' => [
						'vacancy.country.index'		=> 'Список',
						'vacancy.country.create'	=> 'Создание',
						'vacancy.country.update'	=> 'Редактирование',
						'vacancy.country.delete'	=> 'Удаление',    
						'vacancy.country.view'		=> 'Просмотр',    
						'vacancy.country.activate'	=> 'Активация',    
						'vacancy.country.deactivate'=> 'Деактивация',    
					], 				
				]
			],
        ],
   ]
];