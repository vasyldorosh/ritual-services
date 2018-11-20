<?php
/* @var $this yii\web\View */
/* @var $model app\modules\event\models\Event */

$this->title = 'Редактирование пользователя админки: ' . ' ' . $model->id;

echo $this->render('_tabs', ['model'=>$model, 'profile'=>false]) ?>