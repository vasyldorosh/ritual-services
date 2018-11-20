<?php
use yii\helpers\Html;

$this->title = $name;
?>

      <div class="center">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-3 col-aside-up">
				<div class="inner-fluid">
					<br/>
					<br/>
					<br/>
					<br/>
					<h1><?= Html::encode($this->title) ?></h1>
					<?= nl2br(Html::encode($message)) ?>
				</div>
            </div>
          </div>
        </div>
      </div>
