<?php 

return [
   'title' => 'События',
   'items' => [
        'event' => [
            'title' => 'Шаблоны',
            'actions' => [
                'event.event.index'		=> 'Список',
                'event.event.create'	=> 'Создание',
                'event.event.update'	=> 'Редактирование',
                'event.event.delete'	=> 'Удаление',    
                'event.event.view'		=> 'Просмотр',    
                'event.event.activate'	=> 'Активация',    
                'event.event.deactivate'=> 'Деактивация',    
            ],        
        ],
        'spool' => [
            'title' => 'Почтовая очередь',
            'actions' => [
                'event.spool.index'	=> 'Список',
                'event.spool.delete'=> 'Удаление',    
            ],
        ]
   ]
];