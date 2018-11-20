<?php 

return [
   'title' => 'Пользователи админки',
   'items' => [
        'admin' => [
            'title' => 'Пользователи',
            'actions' => [
                'admin.admin.index'		=> 'Список',
                'admin.admin.create'	=> 'Создание',
                'admin.admin.update'	=> 'Редактирование',
                'admin.admin.delete'	=> 'Удаление',    
                'admin.admin.view'		=> 'Просмотр',    
                'admin.admin.activate'	=> 'Активация',    
                'admin.admin.deactivate'=> 'Деактивация',    
                'admin.admin.eventLog'  => 'Просмотр лога назначения почтовых событий',       
            ],        
        ],
        'group' => [
            'title' => 'Группы',
            'actions' => [
                'admin.group.index'		=> 'Список',
                'admin.group.create'	=> 'Создание',
                'admin.group.update'	=> 'Редактирование',
                'admin.group.delete'	=> 'Удаление',    
                'admin.group.view'		=> 'Просмотр',    
                'admin.group.activate'	=> 'Активация',    
                'admin.group.deactivate'=> 'Деактивация',      
            ],
        ],
        'log' => [
            'title' => 'Лог',
            'actions' => [
                'admin.log.index'		=> 'Список',
            ],
        ]
   ]
];