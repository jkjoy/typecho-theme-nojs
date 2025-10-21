<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<section class="content__item">
    <?php $this->comments()->to($comments); ?>
    <?php if($this->allow('comment')): ?>
    <div id="<?php $this->respondId(); ?>" class="comment-form-wrapper" id="comment-form-wrapper">
        <form method="post" action="<?php $this->commentUrl() ?>" id="comment-form" class="comment-form" role="form">
            <input type="hidden" name="parent" id="comment-parent" value="0" />
            <?php if($this->user->hasLogin()): ?>
            <p>登录身份: <a href="<?php $this->options->profileUrl(); ?>"><?php $this->user->screenName(); ?></a>. 
            <a href="<?php $this->options->logoutUrl(); ?>" title="退出">退出 &raquo;</a></p>
            <?php else: ?>
            <span class="comment-form__input-wrapper">
                <input type="text" name="author" id="author" class="comment-form__input" 
                    placeholder="称呼 *" value="<?php $this->remember('author'); ?>" required />
            </span>
            <span class="comment-form__input-wrapper">
                <input type="email" name="mail" id="mail" class="comment-form__input" 
                    placeholder="邮箱<?php if ($this->options->commentsRequireMail): ?> *<?php endif; ?>" 
                    value="<?php $this->remember('mail'); ?>"<?php if ($this->options->commentsRequireMail): ?> required<?php endif; ?> />
            </span>
            <span class="comment-form__input-wrapper">
                <input type="url" name="url" id="url" class="comment-form__input" 
                    placeholder="网站" value="<?php $this->remember('url'); ?>" />
            </span>
            <?php endif; ?>
            <span class="comment-form__input-wrapper">
                <textarea rows="8" cols="50" name="text" id="textarea" class="comment-form__input comment-form__input--textarea" 
                    placeholder="请输入评论内容" required><?php $this->remember('text'); ?></textarea>
            </span>
            <p class="form-submit">
                <input type="submit" name="submit" id="comment-submit-button" class="comment-form__input comment-form__input--button" 
                    value="提交评论" />
                <?php $comments->cancelReply(); ?>
            </p>
        </form>
    </div>
    <?php else: ?>
    <?php endif; ?>
    <?php if ($comments->have()): ?>
    <div class="comment">
        <?php $comments->listComments(array(
            'before'        =>  '<ul class="comment__list">',
            'after'         =>  '</ul>',
            'beforeAuthor'  =>  '',
            'afterAuthor'   =>  '',
            'beforeDate'    =>  '',
            'afterDate'     =>  '',
            'dateFormat'    =>  'Y/m/d'
        )); ?>    
        <?php $comments->pageNav(
            '&laquo;', '&raquo;',
            1, '...',
            array(
                'wrapTag' => 'div',
                'wrapClass' => 'page-navigator',
                'itemTag' => 'span',
                'currentClass' => 'current'
            )
        ); ?>
    </div>
    <?php endif; ?>
</section>
<?php function threadedComments($comments, $options) {
    $commentClass = '';
    if ($comments->authorId) {
        if ($comments->authorId == $comments->ownerId) {
            $commentClass .= ' comment-by-author';
        }
    }
?>
    <li id="li-<?php $comments->theId(); ?>" class="comment__list-item<?php 
    if ($comments->levels > 0) {
        echo ' comment-child';
        $comments->levelsAlt(' comment-level-odd', ' comment-level-even');
    } else {
        echo ' comment-parent';
    }
    $comments->alt(' comment-odd', ' comment-even');
    echo $commentClass;
    ?>">
        <div class="comment__box" id="<?php $comments->theId(); ?>">
            <div class="comment__header">
                <span class="comment__author-name"><?php $comments->author(); ?></span>
                <span class="comment__submit-date"><?php $comments->date('Y/m/d H:i'); ?></span>
            </div>
            <div class="comment__content">
<?php
// 在 comments.php 中替换评论输出代码
$reply_link = '';
if ($comments->parent) {
    // 直接实现逻辑，不依赖外部函数
    try {
        $db = Typecho_Db::get();
        $row = $db->fetchRow($db->select('author')
            ->from('table.comments')
            ->where('coid = ? AND status = ?', $comments->parent, 'approved')
        );
        
        if (!empty($row)) {
            $reply_link = '<a href="#comment-'.$comments->parent.'" style="text-decoration: none; color: inherit;">@'.$row['author'].'</a>';
        }
    } catch (Exception $e) {
        // 忽略错误
    }
}
// 获取评论内容
$content = $comments->content;
// 插入回复链接
if (!empty($reply_link)) {
    // 处理HTML内容
    if (strpos($content, '<p>') === 0) {
        $content = '<p>' . $reply_link . ' ' . substr($content, 3);
    } else {
        $content = $reply_link . ' ' . $content;
    }
}
// 输出最终内容
echo $content;
?>
                <p class="comment__reply-button">
                    <?php $comments->reply('回复'); ?>
                </p>
            </div>
        </div>
        <?php if ($comments->children) { ?>
            <div class="comment-children">
                <?php $comments->threadedComments($options); ?>
            </div>
        <?php } ?>
    </li>
<?php } ?>