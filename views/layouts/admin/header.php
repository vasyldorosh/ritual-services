<?php
use yii\helpers\Html;
use app\components\AccessControl;
use app\modules\admin\models\Admin;
$module_id = Yii::$app->controller->module->id;
?>

<div id="header">
	
	<a id="header_logo" href="/acms">
		<img src="/admin/img/logo.png" alt="Aiken Interactive">
	</a>
	
	<ul id="header_user">
		<li class="welcome">Здравствуйте, 		
			<?php if (session()->get("root_id") && $admin = Admin::findOne(session()->get("root_id"))):?>
				<span onclick="window.location = '/?r=admin/admin/login&id=<?=$admin->id?>&root=1'" style="font-weight: bold;cursor:pointer"><?=$admin->name?>(<?=$admin->email?>)</span> =>
			<?php endif;?>
			<span><?=admin()->identity->name?></span> (<?=admin()->identity->email?>)
		</li>
		<li><a class="exit" href="/?r=admin/auth/logout"><span></span>Выйти</a></li>
		<li><a class="settings" href="/?r=settings/settings" title="options"><span></span>Настройки</a></li>
	</ul>
</div>

<div id="header_nav" style="height: 110px;">

	<a href="#" id="header_nav_toggle"></a>
		<ul class="main-nav" id="yw3">
			<li <?=((!in_array($module_id, ['event', 'structure', 'menu']) && $module_id!='admin') || ($module_id=='admin' &&  Yii::$app->controller->id == 'index'))?'class="active"':''?>><a class="modules" href="/acms"><span></span>Модули</a></li>
			<?php if (AccessControl::can('event.event.index')):?><li <?=$module_id=='event'?'class="active"':''?>><a class="events" href="/?r=event/event"><span></span>События</a></li><?php endif;?>
			<?php if (AccessControl::can('admin.admin.index')):?><li <?=($module_id=='admin' && Yii::$app->controller->id != 'index')?'class="active"':''?>><a class="access" href="/?r=admin/admin"><span></span>Доступ</a></li><?php endif;?>
			<?php if (AccessControl::can('structure.structure.index')):?><li <?=$module_id=='structure'?'class="active"':''?>><a class="structure" href="/?r=structure/structure"><span></span>Структура</a></li><?php endif;?>
			<?php if (AccessControl::can('menu.menu.index')):?><li <?=$module_id=='menu'?'class="active"':''?>><a class="menus" href="/?r=menu/menu"><span></span>Меню</a></li><?php endif;?>
	</ul>	
	
	<div class="clear"></div>
</div>