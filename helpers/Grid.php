<?php
namespace app\helpers;

use Yii;
use yii\helpers\Html;

class Grid
{
    public static function checkboxValueColumn($model, $attribute)
    {
        return [
            'attribute' => $attribute,
            'filter' => Html::activeDropDownList(
				$model,
				$attribute,
				[
					0 => 'нет', 
					1 => 'да'
				],
                [
					'class' => 'form-control', 
					'prompt' => '-',
				]
            ),
            'value' => function ($model, $index, $widget) use ($attribute) {
                return $model->$attribute ? '<span class="label label-success">да</span>' : '<span class="label label-danger">нет</span>';
			},
			'format' => 'raw',
        ];
    }
	
    public static function dateValueColumn($model, $attribute, $filter=false)
    {
        return [
            'attribute' => $attribute,
            //'filter' => '<input class="daterange form-control" type="text" >',
			'filter'=>$filter,
            'value' => function ($model, $index, $widget) use ($attribute) {
                return self::datetimeValue($model->$attribute);
			},
        ];
    }
	
    public static function datetimeValue($value)
    {
         return !empty($value) ? date("Y-m-d H:i:s", $value) : '-';
    }	
	
    public static function checkboxValue($value)
    {
         return $value == 1 ? 'да' : 'нет';
    }
	
}
?>