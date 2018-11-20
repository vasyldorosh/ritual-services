<?php 
//use \Yii;
use \app\helpers\Tree;
?>


<div id="sidebar">
	<div id="sidebar_content">
		<div class="acms_menu acms_menu_modules">
			<ul>
				<li><span class="tree-head">Модули</span>
					<ul class="treeview" id="yw2">
					<?php foreach (Tree::getModulesRightMenu() as $item):?>
						<?=Tree::sideMenu(Tree::getSideConfigByModule($item['id']));?>			
					<?php endforeach;?>			
					</ul>		
				</li>
			</ul>
		</div>
	</div>
	<a href="#" id="sidebar_toggle" class=""></a>
</div>