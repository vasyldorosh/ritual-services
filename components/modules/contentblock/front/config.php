<?php 
use \yii\helpers\Html
?>

<div class="form-group">
	<label class="control-label"><?= $model->getAttributeLabel('action')?></label>
	<?= Html::dropDownList('action', isset($action) ? $action : '', \app\modules\contentblock\front\Widget::getActions(), ['class' => 'form-control', 'prompt' => '-', 'id'=>'widget-action']); ?>
</div>

<div class="form-group">
	<label class="control-label"><?= $model->getAttributeLabel('template')?></label>
	<?= Html::dropDownList('template', isset($template) ? $template : '', \app\modules\contentblock\front\Widget::getTemplates(), ['class' => 'form-control', 'prompt' => '-', 'id'=>'widget-template']); ?>
</div>

<div class="form-group">
	<label class="control-label"><?= $model->getAttributeLabel('block_id')?></label>
	<?= Html::dropDownList('block_id', isset($block_id) ? $block_id : '', \app\modules\contentblock\models\ContentBlock::getList(), ['class' => 'form-control', 'prompt' => '-', 'id'=>'widget-block_id']); ?>
</div>

