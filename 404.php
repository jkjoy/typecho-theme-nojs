<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>
<section class="content__item">
    <article class="article">
        <div class="article__header-link">
            <h2>404 - 页面未找到</h2>
        </div>

        <div class="article__content">
            <p>抱歉，您访问的页面不存在。</p>
            <p>可能原因：</p>
            <ul>
                <li>页面已被删除或移动</li>
                <li>链接地址错误</li>
                <li>您没有访问权限</li>
            </ul>
            <p><a href="<?php $this->options->siteUrl(); ?>">返回首页</a></p>
        </div>
    </article>
</section>
<div class="content__push"></div>
<?php $this->need('footer.php'); ?>