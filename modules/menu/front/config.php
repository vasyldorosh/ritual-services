<?php 
use yii\helpers\Html;
use app\modules\menu\models\Menu;
use app\modules\menu\front\Widget;
?>

<div class="form-group">
	<label class="control-label"><?= $model->getAttributeLabel('action')?></label>
	<?= Html::dropDownList('action', isset($action) ? $action : '', Widget::getActions(), ['class' => 'form-control', 'prompt' => '-','id'=>'widget-action']); ?>
</div>

<div id="action-options">
	<div class="form-group js-params index">
		<label class="control-label"><?= $model->getAttributeLabel('menu_id')?></label>
		<?= Html::dropDownList('menu_id', isset($menu_id) ? $menu_id : '', Menu::getList(), ['class' => 'form-control', 'prompt' => '-','id'=>'widget-menu_id']); ?>
	</div>

	<div class="form-group js-params index">
		<label class="control-label"><?= $model->getAttributeLabel('template')?></label>
		<?= Html::dropDownList('template', isset($template) ? $template : '', Widget::getTemplates(), ['class' => 'form-control', 'prompt' => '-','id'=>'widget-template']); ?>
	</div>
</div>

<script>
showActiveTab();
</script>

