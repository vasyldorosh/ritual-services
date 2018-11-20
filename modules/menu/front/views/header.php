<nav id="site-navigation" class="main-navigation" role="navigation">
    <h3 class="menu-toggle"><?= t('Меню')?></h3>
    <div class="menu-top-menu-container">
        <ul id="menu-top-menu" class="menu nav-menu">
            <?php foreach ($links as $link):?>
                <li class="menu-item menu-item-type-post_type menu-item-object-page">
                    <a href="<?= $link['link']?>"class="<?= $link['active']?'active':''?>"><?= $link['title']?></a>
                </li>
            <?php endforeach;?>
        </ul>
    </div>						
</nav>
