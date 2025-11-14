<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>
<section class="content__item">
    <article class="article">
        <div class="article__header-link">
            <a><h2><?php $this->title(); ?></h2></a>
        </div>
        <span> 👁️‍🗨️ <?php get_post_view($this); ?></span>
        <div class="article__content">
            <?php $this->content = processArticleContent($this->content);$this->content();?>
        </div>
        <span class="article__category">
            <?php if ($this->categories): ?>
                <?php foreach ($this->categories as $category): ?>
                    📁<a class="article__category-link"
                         href="<?php echo $category['permalink']; ?>"
                         title="查看分类<?php echo $category['name']; ?>下的文章">
                       <?php echo $category['name']; ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if ($this->tags): ?>
                <?php foreach ($this->tags as $tag): ?>
                    🔖<a href="<?php echo $tag['permalink']; ?>"
                         class="article__category-link"
                         title="查看标签<?php echo $tag['name']; ?>下的文章">
                       <?php echo $tag['name']; ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </span>
        <!-- 文章专属：上一篇 / 下一篇 -->
        <div class="article__footer-link">上一篇：<?php $this->thePrev('%s', '没有了'); ?></div>
        <div class="article__footer-link">下一篇：<?php $this->theNext('%s', '没有了'); ?></div>
        <!-- 公共：编辑 -->
        <div class="article__footer-link">
            <?php if ($this->user->hasLogin() && $this->user->pass('editor', true)): ?>
                <a target="_blank"
                   href="<?php $this->options->adminUrl('write-post.php?cid=' . $this->cid); ?>">
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
<?php $this->need('footer.php'); ?>