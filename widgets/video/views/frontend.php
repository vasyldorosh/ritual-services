<div id="videoManager">                        
						<input type="hidden" name="<?=$modelName?>[<?= $attributeDeletedIds?>]" id="<?= strtolower($modelName)?>-<?= $attributeDeletedIds?>">
						<h5 class="field__title"><?= t('Видео')?></h5>
                        <div class="field__row field__row_middle">
                          <div class="set-btn-right">
                            <div class="btn-gray-middle width-all js-btn-add-video"><?= t('добавить')?></div>
                          </div><!-- /.set-btn -->
                          <div class="set-field-left">
                            <input type="text" name="title" value="" class="field__input field__input_all js-url-video-result">
                          </div><!-- /.set-field -->
                        </div>
      
                        <ul class="preview-list js-preview-video">
						<?php foreach ($videos as $video):?>
                          <li class="preview-list__item">
                            <i class="close-red-small-ico close-red-small-ico_over js-close-preview-video-uploaded" data-id="<?= $video->id?>"></i>
                            <img src="<?= $video->getImageUrl('image', 75, 42, 'crop')?>" alt="">
                          </li>
						<?php endforeach;?>  
                        </ul>
</div>						