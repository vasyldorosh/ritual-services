<?php
namespace app\widgets;

class ActiveField extends \yii\widgets\ActiveField
{
	public $options = ['class' => 'control-group'];
	
	public $errorOptions = ['class' => 'help-inline'];
	
    public $template = "{label}\n <div class='controls'>{input}\n{error}</div> \n{hint}";

}
