<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<!DOCTYPE html>
<html lang="zh-Hans">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php if($this->_currentPage>1) echo '第 '.$this->_currentPage.' 页 - '; ?><?php $this->archiveTitle(array(
            'category'  =>  _t('分类 %s 下的文章'),
            'search'    =>  _t('包含关键字 %s 的文章'),
            'tag'       =>  _t('标签 %s 下的文章'),
            'date'      =>  _t('在<span> %s </span>发布的文章'),
            'author'    =>  _t('%s 发布的文章')
        ), '', ' - '); ?>
<?php $this->options->title(); ?><?php if ($this->is('index')) echo ' - '; ?>
<?php if ($this->is('index')) $this->options->description() ?></title>
<link rel="stylesheet" href="<?php $this->options->themeUrl('style.css'); ?>">
<?php if ($this->options->iconUrl) : ?>
<link rel="icon" href="<?php $this->options->iconUrl(); ?>" type="image/x-icon"/>
<?php endif; ?>
<?php if ($this->options->addhead) : ?>
<?php $this->options->addhead(); ?>
<?php endif; ?>
<?php $this->header(); ?>
</head>
<body class="body">
<div class="content">
<header class="header">
<div class="header__wrapper">
<a href="<?php $this->options->siteUrl(); ?>" class="brand"><?php $this->options->title() ?></a>
<span class="header__subtitle"><?php $this->options->description() ?></span>
<?php $stats = getSiteStatsWithCache(); ?>
<nav class="header__menu">
<ul class="header__list">
<li class="header__list-item"><a class="header__link" href="<?php $this->options->siteUrl(); ?>"><?php _e('首页'); ?></a></li>
<?php \Widget\Contents\Page\Rows::alloc()->to($pages); ?>
<?php while ($pages->next()): ?>
<li class="header__list-item"><a class="header__link" href="<?php $pages->permalink(); ?>"><?php $pages->title(); ?><?php
                $slug = $pages->slug;
                $count = '';
                if ($slug == 'links') {
                    $count = $stats['totalLinks'];
                } elseif ($slug == 'archives') {
                    $count = $stats['totalPosts'];
                } elseif ($slug == 'tags') {
                    $count = $stats['totalTags'];
                } elseif ($slug == 'categories') {
                    $count = $stats['totalCategories'];
                }
                if ($count !== '') {
                    echo '<sup>' . $count . '</sup>';
                }
                ?></a></li>
<?php endwhile; ?>
<?php if($this->user->hasLogin()):?>
<li class="header__list-item"><a class="header__link" href="<?php $this->options->adminUrl(); ?>" cat_title="管理" target="_blank">管理</a></li>
<?php else: ?>
<li class="header__list-item"><a class="header__link" href="/admin/login.php" cat_title="登录" target="_blank" title="登录">登录</a></li>
<?php endif; ?>
</ul>
</nav>
</div>
</header>