<?php 
use yii\helpers\Html;
use app\modules\menu\models\Menu;

$list = Menu::getList();
?>
	
		<div class="control-group">
			<label class="control-label" for="settings-sitemap_menu">Меню</label>
			<div class="controls input-xlarge">
				<select id="settings-sitemap_menu" name="Settings[sitemap_menu]" class="form-control">
					<option value="">-</option>
					<?php foreach ($list as $id=>$title):?>
						<option value="<?= $id?>" <?= (isset($values['sitemap_menu']) && $values['sitemap_menu']==$id)?'selected="selected"':''?>><?= $title?></option>
					<?php endforeach;?>
				</select>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="settings-sitemap_domain">Домен</label>
			<div class="controls input-xlarge">
				<input type="text" id="settings-sitemap_domain" class="form-control" name="Settings[sitemap_domain]" value="<?=isset($values['sitemap_domain'])?$values['sitemap_domain']:''?>">
			</div>
		</div>		
