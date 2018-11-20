			<?= $form->field($model, 'is_active')->checkbox() ?>
			
            <?= $form->field($model, 'title') ?>
			
            <?= $form->field($model, 'image')->fileInput() ?>
			
			<?php if (!empty($model->image)):?>
				<?= \yii\helpers\Html::img($model->getImageUrl('image', 120, 80, 'crop'));?>
			<?php endif;?>
			
			<?= $form->field($model, 'is_not_editor')->checkbox() ?>
			
			<?= $form->field($model, 'content')->textArea(['class'=>!$model->is_not_editor?'ckeditor js-editor form-control':'js-editor form-control'])?>
			
			<?= $form->field($model, 'js')->textArea()?>	
			
<?php $this->registerJs("
	$('#contentblock-is_not_editor').change(function(){
		toogleEditor($(this).is(':checked'));
	})                                                                 

	function toogleEditor(val) {
		if (val){
			$('textarea.js-editor').each(function(i,v){
				CKEDITOR.instances[$(this).attr('id')].destroy();
			})
		} else {	
			$('textarea.js-editor').each(function(i,v){
				$('#'+$(this).attr('id')).ckeditor();
			})		
		}
	}
", \yii\web\View::POS_END, 'content_block');?>                                       