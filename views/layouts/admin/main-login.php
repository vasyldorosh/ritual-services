<?php
use yii\helpers\Html;

$this->beginPage() ?><?php $this->registerJsFile('/admin/js/ckeditor/ckeditor.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>	
<?php $this->registerJsFile('/admin/js/main.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>	
<?php $this->registerJsFile('/admin/js/jquery.cookie.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>	
<?php $this->registerJsFile('/admin/js/acms9.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>	
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
       
		<link href="/admin/css/bootstrap-responsive.css" rel="stylesheet" type="text/css" media="all" />
		<link href="/admin/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css" media="all" />
		<link href="/admin/css/main.css" rel="stylesheet" type="text/css" media="all" />
		<link href="/admin/css/auth.css" rel="stylesheet" type="text/css" media="all" />
		
		
    </head>
	
    <body class="login-page">
    <?php $this->beginBody() ?>
    
	<div id="wrapper">
		<div id="header">
			<a id="header_logo" href="/acms">
				<img src="/admin/img/logo.png" alt="Aiken Interactive">
			</a>
		</div>
		
		<div id="content">
			<?php echo $content?>
		</div>
		
		<div class="push"></div>
	</div>
	
	
	<?= $this->render('footer.php') ?>
	
    <?php $this->endBody() ?>
    </body>
	
</html>
<?php $this->endPage() ?>
