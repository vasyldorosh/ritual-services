<?php 

return [
   'title' => 'Товары',
   'items' => [
        'product' => [
            'title' => 'Товары',
            'actions' => [
                'product.product.index'		=> 'Список',
                'product.product.create'	=> 'Создание',
                'product.product.update'	=> 'Редактирование',
                'product.product.delete'	=> 'Удаление',    
                'product.product.view'		=> 'Просмотр',    
                'product.product.activate'	=> 'Активация',    
                'product.product.deactivate'=> 'Деактивация',    
            ], 
			'items' => [
				'category' => [
					'title' => 'Категории',
					'actions' => [
						'product.category.index'	=> 'Список',
						'product.category.create'	=> 'Создание',
						'product.category.update'	=> 'Редактирование',
						'product.category.delete'	=> 'Удаление',    
						'product.category.view'		=> 'Просмотр',    
						'product.category.activate'	=> 'Активация',    
						'product.category.deactivate'=> 'Деактивация',    
					], 				
				]
			],
        ],
   ]
];