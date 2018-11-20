<?php 
use app\widgets\ImagePicker;
use app\modules\structure\models\Domain;
?>
			<?= $form->field($model, 'alias') ?>
				
            <?= $form->field($model, 'is_active')->checkbox() ?>
            
			<?= $form->field($model, 'is_root')->checkbox() ?>
            
			<?=$form->field($model, 'template_id')->dropDownList(\app\helpers\Page::getTemplatesList(), ['prompt'=>'-']) ?>
			
			<?php if (empty($model->id)):?>
				<?=$form->field($model, 'domain_id')->dropDownList(Domain::getAll(), ['prompt'=>'-']) ?>
			<?php endif;?>
			
			<?=$form->field($model, 'lang')->dropDownList(Yii::$app->params['languages'], ['prompt'=>'-']) ?>
			
			<?php $model->post_langs = $model->getPost_langs()?>
			<?=$form->field($model, 'post_langs')->dropDownList(Yii::$app->params['languages'], ['multiple'=>true,]) ?>
			
			<?= $form->field($model, 'logo')->widget(ImagePicker::className(), ['resize'=>false]) ?>
			<?= $form->field($model, 'logo_hover')->widget(ImagePicker::className(), ['resize'=>false]) ?>
			
			<?= $form->field($model, 'logo_width'); ?>
			
			<?= $form->field($model, 'logo_height'); ?>
			
			
			<?= $form->field($model, 'btn_title'); ?>

			<?=$form->field($model, 'phone_type')->dropDownList(Domain::getPhoneTypeList(), ['prompt'=>'-']) ?>
				
			<?= $form->field($model, 'phone'); ?>	
			
			<?= $form->field($model, 'style')->textArea(); ?>
			
			<?= $form->field($model, 'google_analytics')->textArea(); ?>