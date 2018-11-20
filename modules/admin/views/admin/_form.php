		<!-- В профиле не позволяем менять себе группу -->	
			<?php if (!$profile):?>	
				
				<?= $form->field($model, 'group_id')->dropDownList(\app\modules\admin\models\Group::getList(), ['prompt'=>'-']);?>
						
			<?php endif;?>			
						
            <?= $form->field($model, 'name') ?>
			
            <?= $form->field($model, 'email') ?>
								
            <?= $form->field($model, 'is_active')->checkbox() ?>
            
			<?= $form->field($model, 'new_password')?>

			<div class="control-group">
				<div class="controls">
					<button class="btn" onclick="buildNewPassword('admin-new_password')" type="button">Сгенерировать пароль</button>
				</div>
			</div>			