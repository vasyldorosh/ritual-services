<?php
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use app\widgets\ActiveForm;
use app\modules\menu\models\Menu;
use app\modules\menu\widgets\TreeList;
use app\modules\structure\models\Page;
use app\helpers\Tree as TreeHelper;

function renderList(array $list, $o) 
{
    foreach ($list as $item) {

        $id = $item['id'].'-element';
        echo '<li id="'.$id.'" class="clear-element page-item2 sort-handle right parent-menu"><div>';
        echo '<span class="title">'.$item['title'].'</span>';
	    echo '<a onclick="delClick(this);return false;" href="#del" class="del"><span class="icon-trash"></span></a>
			<a onclick="editClick(this);return false;"  href="#edit" class="ed"><span class="icon-pencil"></span></a></div>';
        
        echo '<ol class="page-list">';
        if (isset($item['childs']) && is_array($item['childs'])) {
            renderList($item['childs'], $o);
        }
        echo '</ol>';
        
        echo '</li>';
        
        if (isset($item['childs'])) { unset($item['childs']);}

		$o->registerJs("
			$('#".$id."').data('data', ".json_encode($item).")
		", \yii\web\View::POS_END, 'menu-data-'.$id);				
    }
}

$siteStructure = TreeHelper::getSiteStructureTree();

$form = ActiveForm::begin([
	'options' => [
		'enctype' => 'multipart/form-data',
		'id' => 'form-menu',
		'class' => 'form-horizontal',
	],
]);
?>
		
    <?= $form->field($model, 'title') ?>
							
    <?= $form->field($model, 'is_active')->checkbox() ?>		
    
	<?= $form->field($model, 'type')->dropDownList(Menu::getTypes(), ['prompt'=>'-']) ?>		

	<?=$form->field($model, 'menuTree')->hiddenInput(['id' => 'resultContainer'])->label(false); ?>
	
	<div id="menuItemlayer" style="display:none;"></div>

	<div class="sortablePolygon form-group">
			<div class="structureContainer">
				<h3>Структура сайта</h3>
				<?php echo TreeList::widget([
					'data' => $siteStructure,
					'htmlOptions' => [
						'class' => 'menu_site_structure', 
						'id' 	=> 'siteStructure'
					]
				]);?>
			</div>

			<div class="menuContainer">
				<h3>Структура меню</h3>
				<ol id="menuStructure" class="page-list">
				<?php $links = $model->getLinksTree();?>
					<?=!empty($links)?renderList($links, $this):''?>
				</ol>
			</div>
	</div>
	<div class="clear"></div>
	
	<?= \app\widgets\Buttons::formSubmit($model);?>	

 
<?php ActiveForm::end(); ?>
		

<div id="modal-menu-item-form" class="modal hide fade">
	<div class="modal-header">
		<a class="close" onclick="$('#cancelButton').trigger('click')">&times;</a>
		<h4 class="js-modal-title">Пункт меню</h4>
	</div>	
	<div class="modal-body">
		<?php echo $this->render('_item_tabs');?>
	</div>
	<div class="modal-footer">
		<div class="form-group">
			<?= Html::button('Отменить', ['class' => 'btn', 'id'=>'cancelButton']) ?>
			<?= Html::button('Применить', ['class' => 'btn btn-success', 'id'=>'applyButton']) ?>
		</div>
	</div>		
</div>
		
<?php $this->registerJsFile('/admin/js/jquery.contextMenu.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('/admin/js/tree.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('/admin/js/jquery.json-2.2.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('/admin/js/jquery-ui-1.9.2.custom.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('/admin/js/jquery.mjs.nestedSortable.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('/admin/js/menu.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('/admin/js/jquery.treeview.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('/admin/js/jquery.treeview.async.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('/admin/js/jquery.treeview.edit.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>

<?php $pageTitlesHash = json_encode(TreeHelper::buildTitlesHash($siteStructure))?>

<?php $this->registerJs("
	var siteStructureTitles = ".(!empty($pageTitlesHash) ? $pageTitlesHash : '{}').";
	var menuTitles = {};
	$('#siteStructure').treeview({});

	var siteStructureLinks 	= $('#siteStructure li a');
	var menuLinks 			= $('#menuStructure .page-item2');
	var linkRow 			= $('#ItemLinkRow');
	var pageIdField 		= $('#ItemPage');
	var cancelButton 		= $('#cancelButton');
	var applyButton 		= $('#applyButton');
	var itemContainer 		= $('#modal-menu-item-form');
	var itemLayer 			= $('#menuItemlayer');
	var menuContainer 		= $('#menuStructure');
	var menuType 			= '".$model->type."';
", \yii\web\View::POS_END, 'menu');?>
