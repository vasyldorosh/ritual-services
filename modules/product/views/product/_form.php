<?php 
use app\modules\product\models\Category;
use app\widgets\ImagePicker;
?>			

<?= $form->field($model, 'category_id')->dropDownList(Category::getList());?>

<?= $form->field($model, 'is_active')->checkbox() ?>
    
<?= $form->field($model, 'image')->widget(ImagePicker::className(), ['width'=>140, 'height'=>50]) ?>
        
<?= $form->field($model, 'title'); ?>

<?= $form->field($model, 'alias'); ?>

<?= $form->field($model, 'price'); ?>

<?= $form->field($model, 'rank'); ?>

<?=$form->field($model, 'seo_title') ?>

<?=$form->field($model, 'seo_description') ?>

<?=$form->field($model, 'content')->textArea(['class'=>'ckeditor']) ?>