<div id="content" class="clearfix">

<article class="post type-post status-publish format-standard hentry">
	
	<div class="entry-content clearfix">
		
		<?php if (!empty($vacancy['image'])):?>
			<img src="<?= $vacancy['image']?>"/>
		<?php endif;?>
		
		<?= $vacancy['content']?>
	</div>

</article>


</div>