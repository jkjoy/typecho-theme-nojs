<?php
/**
 * 标签云
 *
 * @package custom
 * @author NoJS
 * @link https://github.com/jkjoy/typecho-theme-nojs
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>
<!-- 标签云 -->
<section class="content__item content__item--tags">
    <?php $this->widget('Widget_Metas_Tag_Cloud', 'sort=mid&ignoreZeroCount=1&desc=0')->to($tags); ?>
    <?php if($tags->have()): ?>
    <?php while ($tags->next()): ?>
        <a href="<?php $tags->permalink(); ?>" rel="tag" title="<?php $tags->count(); ?> 篇文章">
            <?php $tags->name(); ?> <sup><?php $tags->count(); ?></sup>
        </a>
    <?php endwhile; ?>
</section>
<?php endif; ?>
<?php $this->need('footer.php'); ?>