<?php 

return [
   'title' => 'Структура сайта',
   'items' => [
        'structure' => [
            'title' => 'Структура страниц',
            'actions' => [
                'structure.structure.index'	=> 'Просмотр',
            ],        
        ],
        'archive' => [
            'title' => 'Архив страниц',
            'actions' => [
                'structure.archive.index' 	=> 'Список',
                'structure.archive.create' 	=> 'Добавление в архив',
                'structure.archive.restore' => 'Восстановление с архива',
                'structure.archive.delete' 	=> 'Удаление',
            ],        
        ],
        'page' => [
            'title' => 'Страницы',
            'actions' => [
                'structure.page.index'	=> 'Список',
                'structure.page.create'	=> 'Создание',
                'structure.page.update'	=> 'Редактирование',
                'structure.page.delete'	=> 'Удаление',    
                'structure.page.view'	=> 'Просмотр',    
                'structure.page.move'	=> 'Перемещение',    
            ],        
        ],
        'block' => [
            'title' => 'Блоки страниц',
            'actions' => [
                'structure.block.index'	=> 'Просмотр',
                'structure.block.update'=> 'Создание / редактирование',
                'structure.block.delete'=> 'Удаление',    
            ],        
        ],
        'domain' => [
            'title' => 'Домены',
            'actions' => [
                'structure.domain.index'	=> 'Список',
                'structure.domain.create'	=> 'Создание',
                'structure.domain.update'	=> 'Редактирование',
                'structure.domain.delete'	=> 'Удаление',  
                'structure.domain.activate'	=> 'Активация',    
                'structure.domain.deactivate'=> 'Деактивация',
				'structure.domain.view'		=> 'Просмотр', 
            ],        
        ],
   ]
];