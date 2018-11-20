		<div class="control-group">
			<label class="control-label" for="settings-event-from-email">E-mail (от)</label>
			<div class="controls input-xlarge">
				<input type="text" id="settings-event-from-email" class="form-control" name="Settings[event_from_email]" value="<?=isset($values['event_from_email'])?$values['event_from_email']:''?>">
			</div>
		</div>
	
		<div class="control-group">
			<label class="control-label" for="settings-event-from-name">Имя (от)</label>
			<div class="controls input-xlarge">
				<input type="text" id="settings-event-from-name" class="form-control" name="Settings[event_from_name]" value="<?=isset($values['event_from_name'])?$values['event_from_name']:''?>">
			</div>
		</div>
