<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $stats = getSiteStatsWithCache(); ?>
<nav class="header__menu">
    <ul class="header__list">
        <li class="header__list-item"> 
            <a class="header__link" href="<?php $this->options->siteUrl(); ?>"><?php _e('首页'); ?></a>
        </li>
        <?php
            $blogUrl = $this->options->siteUrl;
            $linksUrl = $blogUrl . ($this->options->rewrite ? '' : 'index.php/') . 'links';
            $archivesUrl = $blogUrl . ($this->options->rewrite ? '' : 'index.php/') . 'archives';
            $tagsUrl = $blogUrl . ($this->options->rewrite ? '' : 'index.php/') . 'tags';
            $catUrl = $blogUrl . ($this->options->rewrite ? '' : 'index.php/') . 'categories';
        ?>
        <li class="header__list-item">
            <a class="header__link" href="<?php echo $archivesUrl ?>">归档<sup><?php echo $stats['totalPosts']; ?></sup></a>
        </li>
        <li class="header__list-item">
            <a class="header__link" href="<?php echo $tagsUrl ?>">标签<sup><?php echo $stats['totalTags']; ?></sup></a>
        </li>
        <li class="header__list-item">
            <a class="header__link" href="<?php echo $catUrl ?>">分类<sup><?php echo $stats['totalCategories']; ?></sup></a>
        </li>
        <?php $plugin_export = Typecho_Plugin::export(); if (array_key_exists('Links', $plugin_export['activated'])): ?>
            <li class="header__list-item">
                <a class="header__link" href="<?php echo $linksUrl ?>">好友<sup><?php echo $stats['totalLinks']; ?></sup></a>
            </li>
        <?php endif; ?>
        <?php \Widget\Contents\Page\Rows::alloc()->to($pages); ?>
        <?php while ($pages->next()): ?>
        <li class="header__list-item">
            <a class="header__link" href="<?php $pages->permalink(); ?>"><?php $pages->title(); ?></a>
        </li>
        <?php endwhile; ?>
        <?php if($this->user->hasLogin()):?>
            <li class="header__list-item">
            <a class="header__link" href="<?php $this->options->adminUrl(); ?>" cat_title="管理" target="_blank">
                管理
            </a>
            </li>
        <?php else: ?>
            <li class="header__list-item">
            <a class="header__link" href="/admin/login.php" cat_title="登录" target="_blank" title="登录">
                登录
            </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>