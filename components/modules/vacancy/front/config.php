<?php 
use \yii\helpers\Html
?>

<div class="form-group">
	<label class="control-label"><?= $model->getAttributeLabel('action')?></label>
	<?= Html::dropDownList('action', isset($action) ? $action : '', \app\modules\vacancy\front\Widget::getActions(), ['class' => 'form-control', 'prompt' => '-', 'id'=>'widget-action']); ?>
</div>
