<?php
/**
 * 友情链接
 *
 * @package custom
 * @author NoJS
 * @link https://github.com/jkjoy/typecho-theme-nojs
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>
<!-- 友情链接 -->
<section class="content__item">
    <ul class="article-header-list">
        <?php
            Puock_Plugin::output('
            <li class="article-header-list-item">
            <a href="{url}" target="_blank">
                <span class="links_author">{name}</span>
            </a> - {title}
            </li>');
        ?>
    </ul>
</section>
<div class="content__push"></div>
<div class="content__push"></div>
<div id="comments">
<?php $this->need('comments.php'); ?>
</div>
<?php $this->need('footer.php'); ?>