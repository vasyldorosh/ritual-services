<?php 
use app\modules\vacancy\models\Country;
use app\widgets\ImagePicker;
?>			
			<?= $form->field($model, 'country_id')->dropDownList(Country::getList());?>
			
			<?= $form->field($model, 'is_active')->checkbox() ?>
				
			<?= $form->field($model, 'image')->widget(ImagePicker::className(), ['width'=>140, 'height'=>50]) ?>
					
            <?= $form->field($model, 'title'); ?>
			
            <?= $form->field($model, 'alias'); ?>
           
			<?= $form->field($model, 'salary'); ?>
			 			
			<?= $form->field($model, 'work_time'); ?>
			
			<?= $form->field($model, 'rank'); ?>
			
			<?=$form->field($model, 'seo_title') ?>
			
			<?=$form->field($model, 'seo_description') ?>
			
			<?=$form->field($model, 'content')->textArea(['class'=>'ckeditor']) ?>