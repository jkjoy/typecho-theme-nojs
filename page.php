<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>
<section class="content__item">
    <article class="article">
        <div class="article__header-link">
            <a><h2><?php $this->title(); ?></h2></a>
        </div>
        <blockquote></blockquote>
        <span> 👁️‍🗨️ <?php get_post_view($this); ?></span>
        <div class="article__content">
            <?php $this->content = processArticleContent($this->content);$this->content();?>
        </div>
        <!-- 独立页面不显示上一篇/下一篇，只留编辑按钮 -->
        <div class="article__footer-link">
            <?php if ($this->user->hasLogin() && $this->user->pass('editor', true)): ?>
                <a target="_blank"
                   href="<?php $this->options->adminUrl('write-page.php?cid=' . $this->cid); ?>">
                    🖋️编辑
                </a>
            <?php endif; ?>
        </div>
    </article>
</section>
<div class="content__push"></div>
<div id="comments">
<?php $this->need('comments.php'); ?>
</div>
<?php $this->need('footer.php'); ?>
