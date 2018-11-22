<div class="main__row row">
     <div class="main__col col-xs-12">
         <div class="category">
             <div class="category__row row">             
                <?php foreach ($items as $item):
                $image = $item->getImageUrl('image', 260, 245, 'crop')
                ?>
                 <div class="category__col col-xs-12 col-sm-6 col-md-3" style="min-width: 260px !important;max-width: 260px !important;">
                     <div class="category__item">
                         <a href="<?= $item->url?>" class="category__main">
                             <div style="background-image: url(<?= $image?>);" class="category__bg">
                                 <div class="category__container">
                                     <div class="category__title"><?= $item->title?></div>
                                     <!--
                                     <div class="category__content">
                                         <div class="category__par"><?= $item->getDescription()?></div>
                                         <div class="category__par">
                                            <span class="btn btn-base">Подробнее</span>
                                         </div>
                                     </div>
                                     -->
                                 </div>
                             </div>
                             <!--
                             <div class="category__price"></div>
                             -->
                         </a>
                     </div>
                 </div>
             <?php endforeach;?>            
             </div>
         </div>
     </div>
 </div>
