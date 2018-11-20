<?php 
use app\modules\structure\models\Page;
use yii\helpers\Html;
?>			
			
<?=$form->field($model, 'is_active')->checkbox(['name'=>'is_active', 'id'=>'menulink-is_active']); ?>	
		
<?=$form->field($model, 'title')->input('text', ['name'=>'title'])?>	
	
<?=$form->field($model, 'description')->textArea(['name'=>'description'])?> 		
	
<?=$form->field($model, 'page_id')->dropDownList(Page::getList(), ['prompt'=>'-', 'name'=>'page_id']) ?>
		
<?=$form->field($model, 'link')->input('text', ['name'=>'link'])?>	
		
<?=$form->field($model, 'style')->input('text', ['name'=>'style'])?>
	
<?=$form->field($model, 'id')->hiddenInput(['name'=>'id'])->label(false); ?>

<?=$form->field($model, 'class')->input('text', ['name'=>'class'])?>

<div class="control-group field-menulink-image">
	<label class="control-label" for="menulink-image">Изображение</label>
	 <div class="controls">
		<input name="image" id="menulink-image" type="text" style="display: none;">
		<input type="file" value="" id="file" name="file">
		 <img src="" id="menu_link_image" height="40" width="50"/>
		<div class="help-inline"></div>
	</div> 

</div>