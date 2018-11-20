<?php

namespace app\components;

use yii\web\AssetBundle;

class JqueryAsset extends AssetBundle
{
    public $sourcePath = '@app/web/admin/js/';
    public $js = [
        'jquery.js',
    ];
}
