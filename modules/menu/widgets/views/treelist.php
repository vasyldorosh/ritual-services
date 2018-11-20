<?php
use yii\helpers\Html;

?>
<?php echo Html::beginTag('ul', $htmlOptions);?>
    <?php foreach ($listData as $item): ?>
        <li>
        <?php if (isset($item['link'])): ?>
            <?php $options = ''; ?>
            <?php if (!empty($item['htmlOptions']) && is_array($item['htmlOptions'])) {
                foreach ($item['htmlOptions'] as $key => $value) {
                    $options = " $key=\"$value\" ";
                }
            } ?>
            <a class="is_not_menu<?=$item['is_not_menu']?>" <?=($item['is_not_menu'])?'style="color:red;"':''?> <?php echo $options?> data-is_not_menu="<?=$item['is_not_menu']?>" href="<?php echo $item['link']?>"><?php echo $item['title']?></a>
        <?php else: ?>
            <?php echo $item['title'] ?>
        <?php endif; ?>

        <?php if (isset($item['childs'])): ?>
            <?php echo $item['childs'] ?>
        <?php endif; ?>
        </li>
    <?php endforeach; ?>
<?php echo Html::endTag('ul');?>
