<?php 

return [
   'title' => 'Слайдер',
   'items' => [
        'slider' => [
            'title' => 'Слайдер',
            'actions' => [
                'slider.slider.index'		=> 'Список',
                'slider.slider.create'		=> 'Создание',
                'slider.slider.update'		=> 'Редактирование',
                'slider.slider.delete'		=> 'Удаление',    
                'slider.slider.view'		=> 'Просмотр',    
                'slider.slider.activate'	=> 'Активация',    
                'slider.slider.deactivate'	=> 'Деактивация',    
            ], 
			'items' => [
				'slide' => [
					'title' => 'Слайды',
					'actions' => [
						'slider.slide.index'			=> 'Список',
						'slider.slide.create'		=> 'Создание',
						'slider.slide.update'		=> 'Редактирование',
						'slider.slide.delete'		=> 'Удаление',    
						'slider.slide.view'			=> 'Просмотр',    
						'slider.slide.activate'		=> 'Активация',    
						'slider.slide.deactivate'	=> 'Деактивация',    
					], 				
				]
			],
        ],
   ]
];