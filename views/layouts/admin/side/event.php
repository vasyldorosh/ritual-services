<?php 
//use \Yii;
use \app\helpers\Tree;
?>


<div id="sidebar">
	<div id="sidebar_content">
		<div class="acms_menu acms_menu_events">
			<ul>
				<li><span class="tree-head">События</span>
					<ul class="treeview" id="yw2">
						<?=Tree::sideMenu(Tree::getSideConfigByModule('event'));?>			
					</ul>		
				</li>
			</ul>
		</div>
	</div>
	<a href="#" id="sidebar_toggle" class=""></a>
</div>