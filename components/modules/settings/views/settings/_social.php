<?php 
use yii\helpers\Html;

$map = [
	'social_vk' 	=> 'VK',
	'social_fb' 	=> 'Facebook',
	'social_inst' 	=> 'Instagram',
	'social_you' 	=> 'Youtube',
];
?>		
	
<?php foreach ($map as $k=>$v):?>	
		<div class="control-group">
			<label class="control-label" for="settings-<?= $k?>"><?= $v?></label>
			<div class="controls input-xlarge">
				<input type="text" id="settings-<?= $k?>" class="form-control" name="Settings[<?= $k?>]" value="<?=isset($values[$k])?$values[$k]:''?>">
			</div>
		</div>
<?php endforeach;?>		
