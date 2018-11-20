<?php 
//use \Yii;
use \app\helpers\Tree;
?>


<div id="sidebar">
	<div id="sidebar_content">
		<div class="acms_menu acms_menu_access">
			<ul>
				<li><span class="tree-head">Доступ</span>
					<ul class="treeview" id="yw2">
						<?=Tree::sideMenu(Tree::getSideConfigByModule('admin'));?>			
					</ul>		
				</li>
			</ul>
		</div>
	</div>
	<a href="#" id="sidebar_toggle" class=""></a>
</div>