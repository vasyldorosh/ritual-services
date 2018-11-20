<?php 
use app\widgets\DateTimePicker;
?>			
			
			<?= $form->field($model, 'is_active')->checkbox() ?>
	        
            <?= $form->field($model, 'title'); ?>
			
            <?= $form->field($model, 'alias'); ?>
			
            <?= $form->field($model, 'rank'); ?>
            
			<?= $form->field($model, 'seo_title'); ?>
			
            <?= $form->field($model, 'seo_description'); ?>
			
			