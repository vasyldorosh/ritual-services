<?php 
use yii\helpers\Html;
use app\modules\contentblock\models\ContentBlock;

$list = ContentBlock::getList();

$map = [
	'cb_contact_footer_home' => 'Контакты в футере на главной',
	'cb_contact_footer_style' => 'Контакты в футере на стилевой',
];

?>		
	
<?php foreach ($map as $k=>$v):?>	
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
