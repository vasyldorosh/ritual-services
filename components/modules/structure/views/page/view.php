<?php 
	use app\components\AccessControl;

	$this->registerJsFile('/admin/js/jquery.contextMenu.js',['depends' => [\yii\web\JqueryAsset::className()]]);
	$this->registerJsFile('/admin/js/common.js',['depends' => [\yii\web\JqueryAsset::className()]]);
	$this->registerCssFile('/admin/css/jquery.contextMenu.css');
	$this->registerCssFile('/admin/css/default.css?t='.time());	
?>

<?php echo $content;?>

<ul id="pageMenu" class="contextMenu">
	<?php if (AccessControl::can('structure.block.update')):?>
	<li><a href="#text" id = 'text_loader'>Текст</a>
    <li><a href="#widget" id = 'widget_loader'>Виджет</a></li>
	</li><?php endif;?>
	
	<?php if (AccessControl::can('structure.block.delete')):?>
    <li><a href="#clear">Очистить блок</a></li>
	<?php endif;?>
</ul>

<?php $this->registerJs("
jQuery(function(){
	if (/msie/i.test(navigator.userAgent)) { jQuery(document)[0].oncontextmenu = function() {return false;}}

	jQuery('div.acms_content-block').contextMenu({menu: 'pageMenu'}, function(action, el, pos) {
		window.parent.blockProcess(".$pageId.", action, el);
	})
});", \yii\web\View::POS_END, 'structure-blocks');?>



<script>
/*	
$(function  () {
  $(".acms_content-block").sortable();
})		
*/		
</script>