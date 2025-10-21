<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>
<!-- 普通文章列表 -->
<?php while ($this->next()): ?>
<section class="content__item">
    <article class="article">
        <div class="article-header">
            <a class="article-header__link" title="<?php $this->title() ?>" href="<?php $this->permalink() ?>">
                <?php if ($this->fields->postSticky == 'sticky') {echo '<span>📌</span>';} ?><?php $this->title() ?>
            </a>
        </div>
        <div class="article__content article__content--index"><?php $this->excerpt(100, '...'); ?></div>
        <div class="article__excerpt">
            <a class="article__excerpt-link" href="<?php $this->permalink() ?>#more" class="more-link" title="read more">阅读全文</a>
        </div>
    </article>
</section>
<?php endwhile; ?>
<!-- 分页 -->
<div class="pagination">
    <span class="pagination__wrapper">
        <?php $this->pageLink('上一页','prev'); ?>
        <?php $this->pageLink('下一页','next'); ?>
    </span>
</div>
<div class="content__push"></div>
<?php $this->need('footer.php'); ?>