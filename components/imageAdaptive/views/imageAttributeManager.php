<?php 

//use Yii;

$errors = $model->uploadErrors;
$modelClass = \yii\helpers\StringHelper::basename(get_class($model));
$className 	= get_class($model);
$class= $modelClass.'-'.$attribute;

$rule = Yii::$app->controller->module->id . '.' . Yii::$app->controller->id . Yii::$app->controller->action->id;

//d($model->oldImages);

?>

<?php foreach ($attributeConfig['sizes'] as $size):?>
	<?php $attr = $attribute . '_' . $size['width'] . 'x' . $size['height'];?>
	<?php $nameSize = $size['width'] . 'x'.$size['height']?>
		<div class="control-group">
			<input type="hidden" class="js-filename" name="<?=$modelClass?>[<?=$attribute?>][<?=$attr?>_filename]" id="<?=$modelClass?>_<?=$attribute?>_<?=$attr?>_filename" >
			<label class="control-label">
						<?php if (isset($size['label'])):?>
							<?=$size['label']?> 
						<?php else:?>
							Размер <?=$size['width'] . 'x'.$size['height'];?> 
						<?php endif;?>			
			</label>
			<div class="controls">
				<input class="js-upload-image" maxlength="128"  type="file">
				<input id="<?=$modelClass?>_<?=$attribute?>_<?=$attr?>" type="hidden" class="js-file-binary-data" name="<?=$modelClass?>[<?=$attribute?>][<?=$attr?>]" value="">
				<br/>
				<select class="js-select-<?=$class?>" name="<?=$modelClass?>[<?=$attribute?>][<?=$attr?>_align]" id="<?=$modelClass?>_<?=$attribute?>_<?=$attr?>_align" >
					<option value="">Выравнивание</option>
					<?php foreach (\app\components\behaviors\ImageAdaptiveUploadBehavior::getAlignList() as $k=>$v):?>
						<option value="<?=$k?>" <?=(isset($model->oldImages[$attribute][$nameSize]['align']) && $model->oldImages[$attribute][$nameSize]['align']==$k)?'selected="selected"':''?>><?=$v?></option>
					<?php endforeach;?>
				</select>
				<select class="js-select-<?=$class?>" name="<?=$modelClass?>[<?=$attribute?>][<?=$attr?>_background_size]" id="<?=$modelClass?>_<?=$attribute?>_<?=$attr?>_background_size" >
					<option value="">Background-Size</option>
					<?php foreach (\app\components\behaviors\ImageAdaptiveUploadBehavior::getBackgroundSizeList() as $k=>$v):?>
						<option value="<?=$k?>" <?=(isset($model->oldImages[$attribute][$nameSize]['background_size']) && $model->oldImages[$attribute][$nameSize]['background_size']==$k)?'selected="selected"':''?>><?=$v?></option>
					<?php endforeach;?>
				</select>
				<?php if (isset($errors[$attr])):?>
					<span class="help-inline error"><?=$errors[$attr]?></span>
				<?php endif;?>
			</div>
			<div class="image-preview js-image-<?=$class?>">			
				<?php if (isset($model->oldImages[$attribute][$nameSize]['path'])):?>
					<br/>
					<img style="height:50px;" src="<?=$model->oldImages[$attribute][$nameSize]['path']?>">
					<br/>
					<a href="#" class="js-delete-image-attribute" data-rule="<?=$rule?>" data-model="<?=$className?>" data-attribute="<?=$attribute?>" data-size="<?=$nameSize?>" data-id="<?=$model->id?>" data-i18n="0" data-lang="">Удалить</a>
				<?php endif;?>
			</div>
		</div>	

		<?php if ($attributeConfig['i18n']):?>
			<?php  foreach (array_keys(Yii::app()->params->otherLanguages) as $lang):?>
				<?php $attr = $attribute . '_' . $size['width'] . 'x' . $size['height']?>
				<div class="control-group">
					
					<label class="control-label">
						<?php if (isset($size['label'])):?>
							<?=$size['label']?> <?=$lang?>
						<?php else:?>
							Размер <?=$size['width'] . 'x'.$size['height'];?> <?=$lang?>
						<?php endif;?>
					</label>
					<div class="controls">
						<input class="js-upload-image" maxlength="128"  type="file">
						<input id="<?=$modelClass?>_<?=$attribute?>_<?=$attr?>_<?=$lang?>" type="hidden" class="js-file-binary-data" name="<?=$modelClass?>[<?=$attribute?>][<?=$attr?>_<?=$lang?>]" value="">
						<br/>
						<select class="js-select-<?=$class?>" name="<?=$modelClass?>[<?=$attribute?>][<?=$attr?>_<?=$lang?>_align]" id="<?=$modelClass?>_<?=$attribute?>_<?=$attr?>_<?=$lang?>_align">
							<option value="">Выравнивание</option>
							<?php foreach (app\components\behaviors\ImageAdaptiveUploadBehavior::getAlignList() as $k=>$v):?>
								<option value="<?=$k?>" <?=(isset($model->oldImages[$attribute.'_'.$lang][$nameSize]['align']) && $model->oldImages[$attribute.'_'.$lang][$nameSize]['align']==$k)?'selected="selected"':''?>><?=$v?></option>
							<?php endforeach;?>
						</select>
						<select class="js-select-<?=$class?>" name="<?=$modelClass?>[<?=$attribute?>][<?=$attr?>_<?=$lang?>_background_size]" id="<?=$modelClass?>_<?=$attribute?>_<?=$attr?>_<?=$lang?>_background_size">
							<option value="">Background-Size</option>
							<?php foreach (app\components\behaviors\ImageAdaptiveUploadBehavior::getBackgroundSizeList() as $k=>$v):?>
								<option value="<?=$k?>" <?=(isset($model->oldImages[$attribute.'_'.$lang][$nameSize]['background_size']) && $model->oldImages[$attribute.'_'.$lang][$nameSize]['background_size']==$k)?'selected="selected"':''?>><?=$v?></option>
							<?php endforeach;?>
						</select>
						<?php if (isset($errors[$attr.'_'.$lang])):?>
							<span class="help-inline error"><?=$errors[$attr.'_'.$lang]?></span>
						<?php endif;?>						
					</div>
					<div class="image-preview js-image-<?=$class?>">
					<?php if (isset($model->oldImages[$attribute.'_'.$lang][$nameSize]['path'])):?>
						<br/>
						<img style="height:50px;" src="<?=$model->oldImages[$attribute.'_'.$lang][$nameSize]['path']?>">
						<br/>
						<a href="#" class="js-delete-image-attribute" data-rule="<?=$rule?>" data-model="<?=$className?>" data-attribute="<?=$attribute?>" data-size="<?=$nameSize?>" data-id="<?=$model->id?>" data-i18n="1" data-lang="<?=$lang?>">Удалить</a>
					<?php endif;?>
					</div>
				</div>						
			<?php endforeach;?>		
		<?php endif;?>
<hr/>		
<?php endforeach;?>