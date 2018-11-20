<?php
use yii\helpers\Html;

$this->beginPage() ?><?php $this->registerJsFile('/admin/js/ckeditor/ckeditor.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>	
<?php $this->registerJsFile('/admin/js/main.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>	
<?php $this->registerJsFile('/admin/js/ckeditor/adapters/jquery.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>	
<?php $this->registerJsFile('/admin/js/jquery.cookie.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>	
<?php $this->registerJsFile('/admin/js/acms9.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>	
	
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
     </head>
	
    <body style="display: none;">
    <?php $this->beginBody() ?>
    
	<div id="wrapper">

        <?= $this->render('header.php') ?>
			
		<div id="content">	
			
			<?php if (!empty($this->context->side)):?>	
				<?= $this->render('side/'.$this->context->side.'.php')?>
			<?php endif;?>
				
		
			<?php if (!empty($this->context->side)):?>	
			<div id="text" style="margin-left: 230px;">
				
				<div id="text_content" style="padding-left: 20px;">	
				
					<?= $this->render('content.php', ['content' => $content]) ?>
				
				</div>
			
			</div>
			<?php else:?>
				<div id="text_content" style="padding-left: 20px;">	
				
					<?= $this->render('content.php', ['content' => $content]) ?>
				
				</div>			
			<?php endif;?>
		</div>	
			
    </div>
	
	<?= $this->render('footer.php') ?>
	
	
		<link href="/admin/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css" media="all" />
		<link href="/admin/css/bootstrap-responsive.css" rel="stylesheet" type="text/css" media="all" />
		<link rel="stylesheet" type="text/css" href="/admin/css/jquery.treeview.css" />
		<link href="/admin/css/main.css" rel="stylesheet" type="text/css" media="all" />
		
    <?php $this->endBody() ?>
	</body>
	
</html>
<?php $this->endPage() ?>
