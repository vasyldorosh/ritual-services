<?php 
use app\widgets\DateTimePicker;
use app\modules\slider\models\Slider;
use app\widgets\ImagePicker;
?>			
			<?= $form->field($model, 'slider_id')->dropDownList(Slider::getList());?>
			
			<?= $form->field($model, 'is_active')->checkbox() ?>
				
			<?= $form->field($model, 'image')->widget(ImagePicker::className(), ['width'=>140, 'height'=>50]) ?>
					
            <?= $form->field($model, 'title'); ?>
           
			<?= $form->field($model, 'publish_time')->widget(DateTimePicker::className(), ['options'=>['readonly'=>false]]) ?>
			
			<?= $form->field($model, 'url'); ?>
			 
			<?= $form->field($model, 'rank'); ?>
			
			<?= $form->field($model, 'is_button')->checkbox() ?>
			
			<?= $form->field($model, 'button_text'); ?>
			
			<?=$form->field($model, 'description')->textArea() ?>
			
			<?=$form->field($model, 'content')->textArea(['class'=>'ckeditor']) ?>