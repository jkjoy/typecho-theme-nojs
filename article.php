<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php if ($this->is('post')): ?>
    <!-- ===================== 文章 (post) ===================== -->
    <section class="content__item">
        <article class="article">
            <div class="article__header-link">
                <a href="#"><h2><?php $this->title(); ?></h2></a>
            </div>
          
            <span> 👁️‍🗨️ <?php get_post_view($this); ?></span>

            <div class="article__content">
                <?php $this->content(); ?>
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
<?php elseif ($this->is('page')): ?>
    <!-- ===================== 独立页面 (page) ===================== -->
    <section class="content__item">
        <article class="article">
            <div class="article__header-link">
                <a href="#"> <h2><?php $this->title(); ?></h2></a>
            </div>
            <blockquote></blockquote>
  
            <span> 👁️‍🗨️ <?php get_post_view($this); ?></span>

            <div class="article__content">
                <?php $this->content(); ?>
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
<?php elseif($this->is('archive') || $this->is('index')): ?>
    <?php if ($this->request->getPathInfo() == '/links') :?>
    <!-- 友情链接 -->
       <section class="content__item">
         <ul class="article-header-list">
        <?php
            Links_Plugin::output('
            <li class="article-header-list-item">
            <a href="{url}" target="_blank">
                <span class="links_author">{name}</span>
            </a> - {title}
            </li>');
        ?>
        </ul>
       </section>
       <div class="content__push"></div>
    <?php elseif ($this->request->getPathInfo() == '/archives') :?>
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
        <!-- 标签云 -->
        <?php elseif ($this->request->getPathInfo() == '/tags') :?>
        <section class="content__item content__item--tags">
            <?php $this->widget('Widget_Metas_Tag_Cloud', 'sort=mid&ignoreZeroCount=1&desc=0')->to($tags); ?>
<?php if($tags->have()): ?>
<?php while ($tags->next()): ?>
                    <a href="<?php $tags->permalink(); ?>" rel="tag" title="<?php $tags->count(); ?> 篇文章">
                    <?php $tags->name(); ?> <sup><?php $tags->count(); ?></sup>
                </a>
<?php endwhile; ?>
            <?php else: ?>
<?php endif; ?>
        </section>
        <div class="content__push"></div>
        <!-- 全部分类 -->
        <?php elseif ($this->request->getPathInfo() == '/categories') :?>
            <section class="content__item">
                <ul class="content__list">
    <?php $this->widget('Widget_Metas_Category_List')->to($categories); ?>
    <?php while($categories->next()): ?>
        <li class="content__list-item"><a class="content__list-link" href="<?php $categories->permalink(); ?>"><?php $categories->name(); ?>(<?php $categories->count(); ?>)</a></li>
    <?php endwhile; ?>
                </ul>
            </section>  
            <div class="content__push"></div>
        <?php else :?>
<!-- 文章列表 -->   
<?php $this->need('sticky.php'); ?>
<!-- 普通文章列表 -->
<?php while ($this->next()): ?>
<section class="content__item">
    <article class="article">
        <div class="article-header"> 
            <a class="article-header__link" title="<?php $this->title() ?>" href="<?php $this->permalink() ?>">
                <?php if ($this->fields->postSticky == 'sticky') {
                        echo '<span>📌</span>';
                    } ?><?php $this->title() ?>
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
    <?php endif; ?>
<!-- 其他页 -->
<?php else: ?>
<?php endif; ?>