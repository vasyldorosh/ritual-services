		<?=$form->field($model, 'title_'.$lang) ?>
		
		<?= $form->field($model, 'content_'.$lang)->textArea(['class'=>!$model->is_not_editor?'ckeditor js-editor form-control':'form-control js-editor'])?>	
