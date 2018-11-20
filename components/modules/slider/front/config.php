<?php 
use \yii\helpers\Html
?>

<div class="form-group">
	<label class="control-label"><?= $model->getAttributeLabel('action')?></label>
	<?= Html::dropDownList('action', isset($action) ? $action : '', \app\modules\slider\front\Widget::getActions(), ['class' => 'form-control', 'prompt' => '-', 'id'=>'widget-action']); ?>
</div>

<div class="form-group">
	<label class="control-label"><?= $model->getAttributeLabel('slider_id')?></label>
	<?= Html::dropDownList('slider_id', isset($slider_id) ? $slider_id : '', \app\modules\slider\models\Slider::getList(), ['class' => 'form-control', 'prompt' => '-', 'id'=>'widget-slider_id']); ?>
</div>

