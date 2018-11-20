			<?= $form->field($model, 'event_id')->dropDownList(\app\components\Event::getList(), ['prompt'=>'-']);?>
			
			<?= $form->field($model, 'content_type')->dropDownList(\app\components\Event::getListContentType(), ['prompt'=>'-']);?>
            
            <?= $form->field($model, 'subject') ?>
			
            <?= $form->field($model, 'from_name') ?>
			
            <?= $form->field($model, 'from_email') ?>
				
			<?= $form->field($model, 'content')->textArea(['class' => 'ckeditor'])->hint(\app\components\Event::getVars($model->event_id)) ?>	
				
            <?= $form->field($model, 'is_active')->checkbox() ?>
			
            <?= $form->field($model, 'is_instant')->checkbox() ?>
