<?php 
use yii\helpers\Html;
use app\modules\menu\models\Menu;
use app\modules\menu\front\Widget;

$list = Menu::getList();
$mapMenus = [
	'menu_header' => 'Header',
];
?>		
	
<?php foreach ($mapMenus as $k=>$v):?>	
		<div class="control-group">
			<label class="control-label" for="settings-<?= $k?>"><?= $v?></label>
			<div class="controls input-xlarge">
				<select id="settings-<?= $k?>" name="Settings[<?= $k?>]" class="form-control">
					<option value="">-</option>
					<?php foreach ($list as $id=>$title):?>
						<option value="<?= $id?>" <?= (isset($values[$k]) && $values[$k]==$id)?'selected="selected"':''?>><?= $title?></option>
					<?php endforeach;?>
				</select>
			</div>
		</div>
<?php endforeach;?>		
