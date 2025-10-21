<?php
/**
 * 全部分类
 *
 * @package custom
 * @author NoJS
 * @link https://github.com/jkjoy/typecho-theme-nojs
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>
<!-- 全部分类 -->
<section class="content__item">
    <ul class="content__list">
    <?php $this->widget('Widget_Metas_Category_List')->to($categories); ?>
    <?php while($categories->next()): ?>
        <li class="content__list-item"><a class="content__list-link" href="<?php $categories->permalink(); ?>"><?php $categories->name(); ?>(<?php $categories->count(); ?>)</a></li>
    <?php endwhile; ?>
    </ul>
</section>  
<div class="content__push"></div>
<?php $this->need('footer.php'); ?>