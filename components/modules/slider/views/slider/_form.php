<?php 
use app\widgets\DateTimePicker;
?>			
			
			<?= $form->field($model, 'is_active')->checkbox() ?>
	        
            <?= $form->field($model, 'title'); ?>
			
			<?= $form->field($model, 'publish_time')->widget(DateTimePicker::className(), ['options'=>['readonly'=>false]]) ?>
		