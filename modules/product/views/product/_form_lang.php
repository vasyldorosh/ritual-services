		<?=$form->field($model, 'title_'.$lang) ?>
				
		<?=$form->field($model, 'seo_title_'.$lang) ?>
		
		<?=$form->field($model, 'seo_description_'.$lang) ?>
		
		<?=$form->field($model, 'content_'.$lang)->textArea(['class'=>'ckeditor']) ?>
	