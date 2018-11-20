<?php 
use app\helpers\Tree;
?>
		
<?php foreach (Tree::getModulesRightMenu() as $item):$key="module_rank_".$item['id'];?>		
		<div class="control-group">
			<label class="control-label" for="settings-<?=$key?>"><?=$item['title']?></label>
			<div class="controls input-mini">
				<input type="text" id="settings-<?=$key?>" class="input-mini" name="Settings[<?=$key?>]" value="<?=isset($values[$key])?$values[$key]:''?>">
			</div>
		</div>
<?php endforeach;?>