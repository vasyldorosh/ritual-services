		<?=$form->field($model, 'subject_'.$lang) ?>
		
		<?=$form->field($model, 'from_name_'.$lang) ?>
		
		<?= $form->field($model, 'content_'.$lang)->textArea(['class' => 'ckeditor'])->hint(\app\components\Event::getVars($model->event_id)) ?>	
