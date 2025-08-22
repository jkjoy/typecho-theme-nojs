<?php
/**
 * NoJS theme 
 * 简约却不简单的单栏主题
 * 仅支持 Typecho 1.2.1 及以下版本，不支持 1.3.0 beta
 * @package NoJS
 * @author Typecho Team
 * @version 1.0.1
 * @link https://typecho.team
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
?>
<!DOCTYPE html>
<html lang="zh-Hans">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php $this->archiveTitle('', '', ' | '); ?><?php $this->options->title(); ?><?php if ($this->is('index')): ?> | <?php $this->options->description() ?><?php endif; ?></title>
    <link rel="stylesheet" href="<?php $this->options->themeUrl('style.css'); ?>">
    <?php if ($this->options->iconUrl) : ?>
        <link rel="icon" href="<?php $this->options->iconUrl(); ?>" type="image/x-icon"/>
    <?php endif; ?>
    <?php if ($this->options->addhead) : ?> 
        <?php $this->options->addhead(); ?>
    <?php endif; ?>
<?php if ($this->is('single')) : ?>
    <meta name="description" content="<?php echo htmlspecialchars($this->description); ?>" />
    <?php $this->header('description='); ?>
<?php elseif ($this->is('archive')) : ?>
    <?php if ($this->request->getPathInfo() == '/links') : ?>
        <meta name="description" content="友情链接" />
    <?php elseif ($this->request->getPathInfo() == '/tags') : ?>
        <meta name="description" content="全部标签" />
    <?php elseif ($this->request->getPathInfo() == '/categories') : ?>
        <meta name="description" content="全部分类" />
    <?php elseif ($this->request->getPathInfo() == '/archives') : ?>
        <meta name="description" content="<?php $this->options->description(); ?>" />
    <?php else : ?>
        <meta name="description" content="<?php $this->options->title() . $this->archiveTitle(); ?>" />
    <?php endif; ?>
    <?php $this->header('description='); ?>
<?php else : ?>
    <?php $this->header(); ?>
<?php endif; ?>
</head>
<body class="body">
<div class="content">
<header class="header">
    <div class="header__wrapper">
        <a href="<?php $this->options->siteUrl(); ?>" class="brand"><?php $this->options->title() ?></a>
        <span class="header__subtitle"><?php $this->options->description() ?></span>
        <?php $this->need('menu.php'); ?> 
    </div>
</header>
<?php $this->need('article.php'); ?> 
<?php if($this->is('single')): ?>
<div id="comments">
<?php $this->need('comments.php'); ?>
</div>
<?php endif; ?>
</div>

<?php $stats = getSiteStatsWithCache(); ?>
<footer class="footer"> 
    <span class="footer__item"> Copyright&nbsp;
        <?php echo date('Y'); ?>&nbsp;<?php $this->options->title(); ?> 
        &nbsp;
        <?php _e('Powered by <a class="footer__link" href="https://typecho.org" target="_blank">Typecho</a>'); ?>
        & <a class="footer__link" href="https://github.com/jkjoy/typecho-theme-nojs" title="nojs 1.0.1" target="_blank">Nojs</a> 
        <span>&nbsp;本站共计 </span>
        <span><?php echo $stats['totalViews']; ?></span> 人浏览 
        <span>运营时间至今有</span>
        <small><?php echo $stats['siteDays']; ?>天</small>
        <?php if ($this->options->ICP) : ?> 
        <p> 
            <a class="footer__link" href="https://beian.miit.gov.cn/" target="_blank"><?php $this->options->ICP(); ?></a> 
        </p>
        <?php endif; ?>
        <?php if ($this->options->addfooter) : ?> 
            <?php $this->options->addfooter(); ?>
        <?php endif; ?>
    </span>
</footer>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 获取文章内容容器（根据主题调整选择器）
    const content = document.querySelector('.content');
    if (!content) return;
    
    // 为所有链接添加 target="_blank"
    content.querySelectorAll('a').forEach(link => {
        // 跳过内部链接（可选）
        if (link.hostname !== window.location.hostname) {
            link.target = '_blank';
            link.rel = 'noopener'; // 安全增强
        }
    });
});
</script>
<?php $this->footer(); ?>
</body>
</html>