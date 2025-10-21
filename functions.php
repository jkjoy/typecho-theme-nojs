<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

function themeConfig($form)
{
    $iconUrl = new Typecho_Widget_Helper_Form_Element_Text('iconUrl', NULL, NULL, _t('Favicon图标'), _t('填写Favicon图标的URL'));
    $form->addInput($iconUrl);
    $addhead = new Typecho_Widget_Helper_Form_Element_Textarea('addhead', NULL, NULL, _t('添加到 &lt;head&gt; 的内容'), _t('可以添加一些自定义的CSS样式或JS脚本'));
    $form->addInput($addhead);
    $addfooter = new Typecho_Widget_Helper_Form_Element_Textarea('addfooter', NULL, NULL, _t('添加到 &lt;body&gt; 底部的内容'), _t('可以添加一些自定义的JS脚本'));
    $form->addInput($addfooter);
    $ICP = new Typecho_Widget_Helper_Form_Element_Text('ICP', NULL, NULL, _t('ICP 备案号'), _t('展示网站备案ICP号'));
    $form->addInput($ICP);
}

// 自定义字段
function themeFields($layout) {
    $postSticky = new Typecho_Widget_Helper_Form_Element_Radio('postSticky',
    array('normal'=> _t('否'), 'sticky'=> _t('是')),
    'normal', _t('是否置顶文章'));
    $layout->addItem($postSticky);
}

/**
 * 获取所有置顶文章的CID
 * @return array 置顶文章的CID数组
 */
function getStickyPostsCids() {
    $db = Typecho_Db::get();

    // 构造查询语句，联接 typecho_fields 表
    $query = $db->select('table.contents.cid')
        ->from('table.contents')
        ->join('table.fields', 'table.fields.cid = table.contents.cid')
        ->where('table.contents.type = ?', 'post')
        ->where('table.contents.status = ?', 'publish')
        ->where('table.fields.name = ?', 'postSticky')
        ->where('table.fields.str_value = ?', 'sticky')
        ->order('table.contents.created', Typecho_Db::SORT_DESC);

    // 执行查询
    $cids = $db->fetchAll($query);

    // 提取 cid 到数组
    $cidArray = [];
    foreach ($cids as $cid) {
        $cidArray[] = $cid['cid'];
    }

    return $cidArray;
}

/*
* 文章浏览数统计
*/
function get_post_view($archive) {
    $cid = $archive->cid;
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();
    if (!array_key_exists('views', $db->fetchRow($db->select()->from('table.contents')))) {
        $db->query('ALTER TABLE `' . $prefix . 'contents` ADD `views` INT(10) DEFAULT 0;');
        echo 0;
        return;
    }
    $row = $db->fetchRow($db->select('views')->from('table.contents')->where('cid = ?', $cid));
    if ($archive->is('single')) {
        $views = Typecho_Cookie::get('extend_contents_views');
        if (empty($views)) {
            $views = array();
        } else {
            $views = explode(',', $views);
        }
        if (!in_array($cid, $views)) {
            $db->query($db->update('table.contents')->rows(array('views' => (int)$row['views'] + 1))->where('cid = ?', $cid));
            array_push($views, $cid);
            $views = implode(',', $views);
            Typecho_Cookie::set('extend_contents_views', $views); //记录查看cookie
            
        }
    }
    echo $row['views'];
}

// 获取站点统计信息（带缓存）
function getSiteStatsWithCache() {
    // 使用文件缓存（兼容性最好）
    $cacheFile = __TYPECHO_ROOT_DIR__ . '/usr/cache/site_stats.cache';
    $cacheTime = 3600; // 1小时缓存
 
    if (file_exists($cacheFile)) {
        $cacheData = json_decode(file_get_contents($cacheFile), true);
        if (time() - $cacheData['cache_time'] < $cacheTime) {
            return $cacheData;
        }
    }
    
    $db = Typecho_Db::get();
    
    // 1. 正确的总分类数
    $stats['totalCategories'] = $db->fetchObject($db->select('COUNT(*) AS cnt')
        ->from('table.metas')
        ->where('type = ?', 'category'))->cnt;
    
    // 2. 正确的总标签数
    $stats['totalTags'] = $db->fetchObject($db->select('COUNT(*) AS cnt')
        ->from('table.metas')
        ->where('type = ?', 'tag'))->cnt;
    
    // 3. 总文章数
    $stats['totalPosts'] = $db->fetchObject($db->select('COUNT(*) AS cnt')
        ->from('table.contents')
        ->where('type = ?', 'post')
        ->where('status = ?', 'publish'))->cnt;
    
    // 4. 总文章字数
    $stats['totalWords'] = $db->fetchObject($db->select('SUM(LENGTH(text)) AS total')
        ->from('table.contents')
        ->where('type = ?', 'post')
        ->where('status = ?', 'publish'))->total;
    
    // 5. 建站时间
    $oldestPost = $db->fetchObject($db->select('MIN(created) AS created')
        ->from('table.contents')
        ->where('type = ?', 'post')
        ->where('status = ?', 'publish'));
    $stats['siteCreationDate'] = date('Y-m-d', $oldestPost->created);
    $stats['siteDays'] = ceil((time() - $oldestPost->created) / 86400);
    
    // 6. 友情链接数量
    $stats['totalLinks'] = 0;
    if (class_exists('Links_Plugin')) {
        $stats['totalLinks'] = $db->fetchObject($db->select('COUNT(*) AS cnt')
            ->from('table.links'))->cnt;
    }
    
    // 7. 总留言数量
    $stats['totalComments'] = $db->fetchObject($db->select('COUNT(*) AS cnt')
        ->from('table.comments')
        ->where('status != ?', 'spam'))->cnt;
    
    // 8. 总访问量
    $stats['totalViews'] = $db->fetchObject($db->select('SUM(views) AS cnt')
        ->from('table.contents')
        ->where('status = ?', 'publish'))->cnt;

    // 保存缓存
    $stats['cache_time'] = time();
    if (!is_dir(dirname($cacheFile))) {
        mkdir(dirname($cacheFile), 0755, true);
    }
    file_put_contents($cacheFile, json_encode($stats));
    
    return $stats;
}