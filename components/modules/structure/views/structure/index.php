<?php 

use yii\bootstrap\Modal;
use yii\helpers\Html;
use app\components\AccessControl;

?>

<link rel="stylesheet" type="text/css" href="/admin/css/jquery.contextMenu.css" />

<?php $this->registerJsFile('/admin/js/jquery.treeview.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('/admin/js/jquery.treeview.edit.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('/admin/js/jquery.treeview.async.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('/admin/js/jquery.contextMenu.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('/admin/js/jquery-ui-1.9.2.custom.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('/admin/js/jquery.cookie.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>

<?php 
$pageId = \Yii::$app->request->get('id');
?>

<div id="sidebar">

    <div id="sidebar_content">
		<div id="siteStructureDialog" class="acms_menu_structore">
			<span class="tree-head">Структура</span>
		
			<div id="siteStructure">
				<ul id="structure_tree" class="treeview">
					<?=\app\helpers\Page::saveDataAsHtml(\app\helpers\Page::getAll())?>
				</ul>
			</div>
			
			<ul id="pageMenu" class="contextMenu">
			<?php if (AccessControl::can('structure.page.create')):?> <li class="add"><a href="#create">Добавить</a></li> <?php endif;?>
			<?php if (AccessControl::can('structure.page.delete')):?> <li class="delete"><a href="#remove">Удалить</a></li> <?php endif;?>
			<?php if (AccessControl::can('structure.page.move')):?>   <li class="move separator"><a href="#move">Переместить</a></li> <?php endif;?>
			<?php if (AccessControl::can('structure.page.update')):?> <li class="properties"><a href="#update">Свойства</a></li> <?php endif;?>
			</ul>
			
			<?php if (AccessControl::can('structure.archive.create')):?> <input type="button" class="btn btn-success btn-xs" value="В архив" id="btn-structure-add-to-archive"> <?php endif;?>
			<?php if (AccessControl::can('structure.archive.index')):?> <input type="button" class="btn btn-apply btn-xs" value="Архив" id="btn-structure-archive"> <?php endif;?>
		
		</div>
    </div>

</div>

<div id="text">
	<div id="text_content">
		<iframe name="visualEditor" frameborder="0" border="0" id="visualEditor" src="/index?r=structure/page/view&id=<?php echo !empty($pageId) ? $pageId : 1;?>"></iframe>
	</div>
</div>

<div id="page-properties" class="modal hide fade">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h4 class="js-modal-title">Создание страницы</h4>
	</div>	
	<div class="modal-body page-properties">
		<?=$this->render('/page/tabs');?>
	</div>
	<div class="modal-footer">
		<div class="form-group">
			<?= Html::submitButton('Создать', ['class' => 'btn btn-success', 'id'=>'page-properties-save-button']) ?>
		</div>
	</div>		
</div>

<div id="page-move" class="modal hide fade">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h4 class="js-modal-title">Перемещение страницы</h4>
	</div>	
	<div class="modal-body">
		<form class="form-horizontal">
			<div class="control-group field-page-template_id required">
				<label class="control-label" for="page-template_id">Родитель: </label>
				<div class="controls">
					<input type="hidden" id="move_page_id">
					<select id="move_parent_id" class="form-control">
						<option value="">-</option>
					</select>
					<div class="help-inline" style="display: none;" id="page_move_errors"></div>
				</div> 
			</div>			
		</form>
	</div>
	<div class="modal-footer">
		<div class="form-group">
			<?= Html::submitButton('Переместить', ['class' => 'btn btn-success', 'id'=>'page-move-button']) ?>
		</div>
	</div>		
</div>

<div id="widget-structure" class="modal hide fade">
    <form class="form-vertical" id="widget-settings" action="/?r=structure/structure" method="post">
	
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h4>Параметры виджета</h4>
	</div>	
		
    <div class="modal-body add-widget-box">
		<div id="widgetsStructure" class="widgets-list">
			<ul id="yw0">
			<?php foreach (app\helpers\Page::getAllWidgets() as $module=>$data):?>
				<li><a class="wdgt" name="<?=$module?>"><?=$data['title']?></a></li>
			<?php endforeach;?>	
			</ul>		
		</div>
		
		<div class="widget-params">
			<div name="widgetConfigBox"></div>
		</div>
		<div class="clear"></div>
    </div>

    <div class="modal-footer">
		<a data-dismiss="modal" class="btn btn-" href="#">Закрыть</a>        
		<a id="widget-properties-save-button" data-dismiss="modal" class="btn-success btn">Создать</a>    
	</div>
	

	</form>	
	
</div>

<div id="text-content" class="modal hide fade">

	<form class="well form-vertical" id="text-settings" action="/?r=structure/tree" method="post">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h4>Текстовая информация</h4>
	</div>

	<div class="modal-body">
		<textarea name="textContent" id="textContent"></textarea>

	</div>
	<div class="modal-footer">
		<a data-dismiss="modal" class="btn" href="#">Закрыть</a>	<a id="text-content-save-button" data-dismiss="modal" class="btn-success btn">Создать</a></div>

    
	</form>
</div>

<div id="modal-archive-list" class="modal hide fade">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h4>Архив</h4>
	</div>	
    <div class="modal-body">
		<?=$this->render('/archive/index')?>
	</div>
</div>


    	
<?php $this->registerJs("
$('#btn-structure-archive').click(function(e){
	$('#modal-archive-list').modal('show');
})	
$('#btn-structure-add-to-archive').click(function(e){
    $.post('/?r=structure/archive/create', {}, function(response){
		alert('Архив успешно создан');
	}, 'text')
})	

$('body').on('click', '.js-archive-restore', function(e){
	e.preventDefault();
	if (!confirm('Восстановить?')) return; 
	self = $(this);
	
	
	$.post(self.attr('href'), {}, function(response){
		if (response.success) {
			window.location = window.location;
		} else {
			alert(response.error);
		}
	}, 'json')		
})

		$(function() {
			$('#widget-structure').show();
			$('.widget-params').css({'min-height': $('.widgets-list ul').height() })
			$('#widget-structure').hide();
			
			
			$('.widgets-list ul li a').on('click', function() {
				$('.widgets-list ul li').removeClass('active');
				$(this).parent().addClass('active');
				
				return false
			});
		})

		 $('[data-toggle=tab]').on('click', function(){
			$(this).tab('show')
		})
	
		$('.treeview a').each(function() {
			if( $(this).attr('href') == '/?r=structure/structure' ) {
				$(this).parent().addClass('active');
			}
		})

jQuery(function($) {
	jQuery('#yw0').treeview({});
	jQuery('#yw1').tab('show');
	jQuery('#structure_tree').treeview({});
});", 

\yii\web\View::POS_END, 'structure-tree');?>

<style>
option:disabled {
	background: #ccc;
	color: red;
}
</style>