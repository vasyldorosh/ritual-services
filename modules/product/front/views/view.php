<div id="content" class="clearfix">

<article class="post type-post status-publish format-standard hentry">
	
	<div class="entry-content clearfix">
		
          <h1><?= $product['title']?></h1>  
          
          <?php if (!empty($photos)):?>
          <div class="gallery">
            <div class="gallery__list">
                <?php foreach ($photos as $k=>$item):
                $title = !empty($item['title']) ? $item['title'] : ($product['title'] . ' ' . ($k+1));
                ?>
                <a data-fancybox="gallery" href="<?= $item['image_1200x760']?>" data-caption="<?= $title?>" class="gallery__item">
                    <img src="<?= $item['image_360x230']?>" alt="<?= $title?>" title="<?= $title?>" width="360" height="230" class="gallery__img">
                </a>
                <?php endforeach;?>
            </div>  
          </div>
          <?php endif;?>
            
         
         <?= $product['content']?>  
         
	</div>

</article>

</div>


<link rel="stylesheet" href="/markup/fancybox/jquery.fancybox.css" />
<?php $this->registerJsFile('/markup/fancybox/jquery.fancybox.js'); ?>
