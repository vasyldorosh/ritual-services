		<?=$form->field($model, 'title_'.$lang) ?>
		
		<?=$form->field($model, 'button_text_'.$lang) ?>
		
		<?=$form->field($model, 'description_'.$lang)->textArea() ?>
		
		<?=$form->field($model, 'content_'.$lang)->textArea(['class'=>'ckeditor']) ?>
	