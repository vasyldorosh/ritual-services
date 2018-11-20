<?php

use app\components\imageAdaptive\Manager;

    echo Manager::widget([
        'model' => $model,
        'attribute' => $attribute,
        'attributeConfig' => $attributeConfig,
    ]);