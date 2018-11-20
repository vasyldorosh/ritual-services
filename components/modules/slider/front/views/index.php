		<section id="featured-slider">
			<div class="slider-cycle" id="main-slider">
				<?php foreach ($slides as $i=>$slide):?>
						<div class="slides js-slide-item">
							<figure>
								<img src="<?= $slide['image']?>">
							</figure>
							<div class="entry-container">
								<div class="entry-description-container">
									<div class="slider-title-head"><h3 class="entry-title"><a href="<?= $slide['url']?>"><span><?= $slide['title']?></span></a></h3></div>
									<div class="entry-content"><p><?= $slide['description']?></p></div>
								</div>
								<div class="clearfix"></div>
								<?php if ($slide['is_button']):?>
								<a class="slider-read-more-button" href="<?= $slide['url']?>"><?= $slide['button_text']?></a>
								<?php endif;?>
							</div>
						</div>
				<?php endforeach;?>		
			</div>
		</section>
		<br/>
		<br/>


		

<script type="text/javascript" src="/markup/bxslider/jquery.bxslider.js"></script>
<link href="/markup/bxslider/jquery.bxslider.css" rel="stylesheet">
<script>
$(document).ready(function(){
	var slider = $('#main-slider').bxSlider({controls: false});
	modifyDelay(0);

	function modifyDelay(startSlide){
		slider.reloadSlider({
			mode: 'horizontal',
			infiniteLoop: true,
			auto: true,
			autoStart: true,
			autoDirection: 'next',
			autoHover: true,
			pause: 30000,
			pager: true,
			pagerType: 'full',
			controls: false,
			captions: true,
			speed: 500,
			startSlide: startSlide,
			onSlideAfter: function($el,oldIndex, newIndex){
				modifyDelay(newIndex);  
			} 
		});
	}    
});

/*
	var mainSlider = new sliderBX(
		'#main-slider',
		{
            preloadImages:'visible',
            
            
            easing: 'linear'
//            easing: 'cubic-bezier(0.680, -0.550, 0.265, 1.550)', // easeInOutBack (http://matthewlein.com/ceaser/)
		}
    );
*/

</script>
		