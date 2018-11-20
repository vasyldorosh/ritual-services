<?php

$this->title = 'Мой профиль: ';

echo $this->render('/admin/_tabs', ['model'=>$model, 'profile'=>true]) ?>