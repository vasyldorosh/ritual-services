<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
?>
<!-- Main content -->
<section class="content">

    <div class="error-page">
        <h2 class="headline text-info"><i class="fa fa-warning text-yellow"></i></h2>

        <div class="error-content">
            <h3><?= $name ?></h3>

            <p><b><?= nl2br(Html::encode($message)) ?></b></p>

            <p>
                Данная ошибка произошла при обработке веб-сервером вашего запроса.
                Пожалуйста, свяжитесь с нами, если вы думаете, что это ошибка сервера. Спасибо.
            </p>
        </div>
    </div>

</section>