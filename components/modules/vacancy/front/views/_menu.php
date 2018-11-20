<div>
	<ul class="menu-vacancy">
	<?php foreach (\app\modules\vacancy\models\Country::getItems() as $item):?>
		<li><a href="<?= $item['url']?>"><?= $item['title']?></a></li>
	<?php endforeach;?>
	</ul>
</div>
<hr/>

<style>
.menu-vacancy {
	margin-bottom: 20px;
}
.menu-vacancy li {
	display: inline;
	margin-right: 20px;
}
.menu-vacancy li a{
	font-size: 20px;
}
</style>