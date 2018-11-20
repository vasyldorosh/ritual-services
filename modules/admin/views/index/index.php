<?php 
	$this->title = 'Главная';
?>

<table>
<?php foreach ($modulesData as $moduleId=>$moduleData):?>
	<?php if (isset($moduleData['items'])):?>
	<tr>
		<td align="left"><h4><?= $moduleData['title']?></h4></td>
	</tr>
		<?php foreach ($moduleData['items'] as $model):?>
		<tr>
			<td style="padding-left: 30px;"><a href="/?r=<?= str_replace('.', '/', $model['action'])?>"><?= $model['title']?></a>: &nbsp;
				<?php foreach ($model['data'] as $k=>$v):?>
					<?= $k?> : <?= $v?> &nbsp;
				<?php endforeach;?>
				<?php if (!empty($model['log'])):?>
				последнее изменение: <?= $model['log']['admin']?> / <?= $model['log']['action']?> / <?= date('Y-m-d H:i:s', $model['log']['time'])?> / <?= $model['log']['ip']?>
				<?php endif;?>
			</td>
		</tr>
		<?php endforeach;?>
	<?php endif;?>
<?php endforeach;?>
</table>