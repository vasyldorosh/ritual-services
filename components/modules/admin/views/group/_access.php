<div class="row">

<div style="margin-left: 30px;">
	<table>
		<tr>
			<td>
				<button type="button" class="btn btn-xs btn-success" onclick="$('.js-checkbox-access').prop('checked', true)">Установить всё</button> 
				<button type="button" class="btn btn-xs btn-success" onclick="$('.js-checkbox-access').prop('checked', false)">Снять всё</button>
			</td>
		</tr>
		<?php $selected = $model->getSelectedAccess();?>
		<?php foreach (\app\components\AccessControl::getThreeList() as $module_id=>$moduleData):?>
			<tr><td><hr/></td></tr>
			<tr>
				<td><label style="font-weight:bold;"><input class="js-checkbox-access" type="checkbox" name="Group[post_access][]" value="<?=$module_id?>" <?=in_array($module_id,$selected)?'checked="checked"':''?>> <?=$moduleData['title']?></label></td>
			</tr>	
			<?php if (isset($moduleData['childs'])):?>
			<tr>
			  <td>	
				<?php foreach ($moduleData['childs'] as $controler_id => $controlerData):?>
				<div style="margin: 5px 10px;">
					<div style="margin-left: 20px;">
						<div style="margin-bottom: 2px;"><?=$controlerData['title']?>:</div>
						<?php foreach ($controlerData['actions'] as $action=>$actionTitle):?>
							<label style="display:inline !important; font-weight:normal;font-style:italic;margin-left: 10px;"><input class="js-checkbox-access" type="checkbox" name="Group[post_access][]" value="<?=$action?>" <?=in_array($action,$selected)?'checked="checked"':''?>> <?=$actionTitle?></label>
						<?php endforeach;?>
					</div>
				</div>
				<?php endforeach;?>
			  </td>
			</tr>
			<?php endif;?>
		<?php endforeach;?>
	</table>	
	</div>  
</div>  