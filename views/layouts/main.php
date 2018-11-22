<?php

use yii\helpers\Html;
?>
<?php $this->beginPage() ?>
<!DOCTYPE HTML>
<html lang="<?= l() ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="description" content="<?php echo isset($this->context->description) ? Html::encode($this->context->description) : ''; ?>" />
        
		<?= $this->context->head ?>
        
		<?= Html::csrfMetaTags(); ?>
		
		<?php $this->head() ?>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
        <title><?php echo isset($this->context->title) ? Html::encode($this->context->title) : ''; ?></title>
        <link href="/markup/css/genericons.css?<?= time()?>" rel="stylesheet">
        <link href="/markup/css/style.css?<?= time()?>" rel="stylesheet">
        <link href="/markup/css/styles.css?<?= time()?>" rel="stylesheet">
		<?php if (Yii::$app->controller->id == 'site'): ?>
            <script src="/markup/js/jquery.js?<?= time()?>"></script>
            <script src="/js/jquery.maskedinput.min.js?<?= time()?>"></script>
            <script src="/js/main.js?<?= time()?>"></script>
        <?php endif; ?>
		
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-129674634-1"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', 'UA-129674634-1');
        </script>
        
        
    </head>
    <body>

<?php $this->beginBody() ?>

        <div id="page" class="hfeed site">
            <header id="masthead" class="site-header clearfix">
				<a name="top"></a>

                <div id="header-text-nav-container">
                    <div class="inner-wrap">

                        <div id="header-text-nav-wrap" class="clearfix">
                            <div id="header-left-section">
                                <div id="header-logo-image">

                                    <a href="<?= d_l('/')?>" class="custom-logo-link" rel="home" itemprop="url"><img width="100" height="100" src="/markup/img/logo.png" class="custom-logo" alt="" itemprop="logo" srcset="/markup/img/logo.png 100w, /markup/img/logo-45x45.png 45w" sizes="(max-width: 100px) 100vw, 100px"></a>							</div><!-- #header-logo-image -->
                                <div id="header-text" class="">
                                    <h3 id="site-title">
                                        <a href="<?= d_l('/')?>" title="<?= t('Ритуальні послуги')?>" rel="home"><?= t('Ритуальні послуги')?></a>
                                    </h3>
                                    <p id="site-description">
                                        <?= conf('contact_phone')?>
                                     </p>
                            
                                </div>
                            </div>
                            <div id="header-right-section">
                                
                                <?php if (count(Yii::$app->params['languages']) > 1):?>
                                <div id="header-right-sidebar" class="clearfix">
                                  <aside class="widget widget_search">
									
									<span style="white-space:nowrap;">
									<?php $i=0;$langs=Yii::$app->params['languages'];foreach ($langs as $k=>$v):?>
										<?php if ($k != l()):?>
											<a href="<?= switchUrl(r()->url, $k)?>"><?= $k?></a> 
										<?php else:?>
											<?= $k?>
										<?php endif;?>
											
										<?php if (count($langs)-1 > $i):?>| <?php endif;?>
									<?php $i++;endforeach;?>
									</span>
									</aside>						
								</div>
                                <?php endif;?>
									
								<?= \app\modules\menu\front\Widget::widget([
									'action'	=> 'index',
									'template'	=> 'header',
									'menu_id'	=> conf('menu_header'),
								])?>
								
									
                            </div>

                        </div><!-- #header-text-nav-wrap -->
                    </div><!-- .inner-wrap -->
                </div><!-- #header-text-nav-container -->

                <?php if (!empty(Yii::$app->controller->activeWidgetData['menu'])):?>
                <div class="header-post-title-container clearfix">
                    <div class="inner-wrap">
                        
                        <?php if (!empty(Yii::$app->controller->activeWidgetData['menu'])):?>
                        <ul class="header-addt-menu">    
                            <?php foreach (Yii::$app->controller->activeWidgetData['menu'] as $item):?>
                            <li>
                                <a href="<?= $item['url']?>" class="<?= (!empty(Yii::$app->controller->activeWidgetData['menu_active_url']) && Yii::$app->controller->activeWidgetData['menu_active_url']==$item['url'])?'active':''?>"><?= $item['title']?></a>
                            </li>
                            <?php endforeach;?>
                        </ul>    
                        <?php endif;?>
                    </div>
                </div>
                <?php endif;?>
                
            </header>
            <div id="main" class="clearfix">
                <div class="inner-wrap">


                    <div id="primary">
                        <div id="content" class="clearfix">

							<?= $content?>
                     
                        </div><!-- #content -->
                    </div><!-- #primary -->


        
                </div><!-- .inner-wrap -->
            </div><!-- #main -->
            
			
			
<style>	

.footer-column .footer-column-contact {
	color: #fff;
}

.footer-columns {
	white-space:nowrap;
}
.footer-columns .footer-column {
	display:inline-block;
}

.footer-column-social {
}

.footer-column-social a{
	margin-left: 20px;
}

.contact-form{white-space:nowrap}
.contact-form .form-row{
	width:250px;
	display:inline-block;
	margin-right: 15px;
}
.contact-form .form-row input[type="submit"]{
	line-height: 30px;
}
.contact-form .form-row input[type="text"]{
    border: 1px solid #EAEAEA;
    line-height: 32px;		
    padding: 1%;
    margin-left: 3px;;
	width: 98%;
    margin: 0 0 30px;
    background-color: #F8F8F8;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;	
}

@media (max-width: 1024px) {
  .contact-form .form-row {
    display:block;
  }
  .contact-form .form-row{
	  width:300px;
  }
  .footer-columns .footer-column {
	  display:block;	
  }
  .footer-column-social a{
	  margin-left: 70px;
   }  
}
		
</style>			
			
			
<footer id="colophon" class="clearfix">
	<div class="footer-widgets-wrapper">
        <div class="inner-wrap">
		<?php if (l() == 'ua'):?>			
			<div class="footer-column footer-column-contact" style="margin-top: 10px;margin-bottom:0px;padding-bottom:0px;">
				<h3 class="widget-title" style="color: #fff; border: none;"><span><?= t('Зворотній зв\'язок')?></span></h3>				
				<form id="form-contact" class="contact-form">
					<div class="form-row">
						<input type="text" placeholder="<?= t('Імя')?>" data-placeholder="<?= t('Імя')?>" name="Contact[name]" id="contact-name">
					</div>
					<div class="form-row">
						<input type="text" placeholder="<?= t('Телефон')?>" data-placeholder="<?= t('Телефон')?>" name="Contact[phone]" id="contact-phone" class="mask-phone">
					</div>
					<div class="form-row">
						<input type="text" placeholder="<?= t('E-mail')?>" data-placeholder="<?= t('E-mail')?>" name="Contact[email]" id="contact-email">
					</div>
					<div class="form-row">
						<input type="submit" value="<?= t('Отправить')?>">
					</div>
				</form>
				
			</div>
			
		<?php endif;?>
	
	</div>
</div>
		</footer>
          	
			<a href="#top" id="scroll-up" style="display: inline;"></a>
        </div><!-- #page -->

		<?php if (Yii::$app->controller->id == 'site'): ?>
            <script src="/markup/js/navigation.js"></script>
			
        <?php endif; ?>
		
		
<?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
