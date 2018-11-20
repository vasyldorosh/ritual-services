
	<?php if (!$model->isNewRecord):?>
	<?php $post_events = $model->getPost_events()?>
		<table>
			<?php foreach (\app\modules\event\models\Event::getList() as $event_id=>$event_title):?>
			<tr>
				<td><label><input type="checkbox" name="Admin[post_events][<?=$event_id?>]" value="<?=$event_id?>" <?=in_array($event_id,$post_events)?'checked="checked"':''?>> <?=$event_title?></label></td>
			</tr>
			<?php endforeach;?>
		</table>
	<?php else:?>
		Доступно после сохранения
	<?php endif;?>	
