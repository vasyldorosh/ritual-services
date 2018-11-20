<?php foreach ($items as $item):
$image = $item->getImageUrl('image', 200, 180, 'resize')
?>
<article class="post type-post status-publish format-standard hentry">
		<header class="entry-header">
		<h2 class="entry-title">
			<a href="<?= $item->url?>" title="<?= $item->title?>"><?= $item->title?></a>
		</h2>
	</header>

	
	<div class="entry-content clearfix">
		<p>
			<?php if (!empty($image)):?>
				<img src="<?= $image?>" align="left" style="margin-right: 20px;" />
			<?php endif;?>
			<?= $item->getDescription()?>
		</p>
	</div>

	<footer class="entry-meta-bar clearfix">
		<div class=" clearfix">
			<span class="read-more-link"><a class="read-more" href="<?= $item->url?>">Читати повністю</a></span>
		</div>
	 </footer>
</article>
<?php endforeach;?>