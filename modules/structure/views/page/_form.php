 		
		<div style="display: none;">
		<?=$form->field($model, 'parent_id')->hiddenInput()->label(false); ?>
		</div>
		
		<?=$form->field($model, 'alias') ?>
		
		<?=$form->field($model, 'title') ?>
		
		<?=$form->field($model, 'template_id')->dropDownList(\app\helpers\Page::getTemplatesList(), ['prompt'=>'-']) ?>
		
		<?=$form->field($model, 'h1') ?>
		
		<?= $form->field($model, 'description')->textArea() ?>	
		
		<?= $form->field($model, 'head')->textArea() ?>	
		
		<?=$form->field($model, 'is_search')->checkbox() ?>
		
		<?=$form->field($model, 'is_canonical')->checkbox() ?>
		
		<?=$form->field($model, 'is_not_breadcrumbs')->checkbox() ?>
		
		<?=$form->field($model, 'is_not_menu')->checkbox() ?>
