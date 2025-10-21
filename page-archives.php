<?php
/**
 * 文章归档
 *
 * @package custom
 * @author NoJS
 * @link https://github.com/jkjoy/typecho-theme-nojs
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>
<!-- 文章归档 -->
<section class="content__item">
<ul class="article-header-list">
<?php
    $stat = Typecho_Widget::widget('Widget_Stat');
    Typecho_Widget::widget('Widget_Contents_Post_Recent', 'pageSize=' . $stat->publishedPostsNum)->to($archives);
    $output = '';
    while ($archives->next()) {
        $output .= '<li class="article-header-list-item"><a class="article-header-list-link" title="' . $archives->title . '" href="' . $archives->permalink . '">';
        $output .= $archives->title . '</a></li>';
    }
    echo $output;
?>
</ul>
</section>
<div class="content__push"></div>
<?php $this->need('footer.php'); ?>
