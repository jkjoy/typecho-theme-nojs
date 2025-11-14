<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
</div> <!-- end .content -->
<?php $stats = getSiteStatsWithCache(); ?>
<footer class="footer">
    <span class="footer__item"> Copyright&nbsp;
        <?php echo date('Y'); ?>&nbsp;<?php $this->options->title(); ?>
        &nbsp;
        <?php _e('Powered by <a class="footer__link" href="https://typecho.org" target="_blank">Typecho</a>'); ?>
        & <a class="footer__link" href="https://github.com/jkjoy/typecho-theme-nojs"
        title="nojs 1.2" target="_blank">Nojs</a>
        <span>&nbsp;本站共计 </span>
        <span><?php echo $stats['totalViews']; ?></span> 人浏览
        <span>运营时间至今有</span>
        <small><?php echo $stats['siteDays']; ?>天</small>
        <?php if ($this->options->ICP) : ?>
        <p>
            <a class="footer__link" href="https://beian.miit.gov.cn/" target="_blank"><?php $this->options->ICP(); ?></a>
        </p>
        <?php endif; ?>
        <?php if ($this->options->addfooter) : ?>
            <?php $this->options->addfooter(); ?>
        <?php endif; ?>
    </span>
</footer>
<?php $this->footer(); ?>
</body>
</html>