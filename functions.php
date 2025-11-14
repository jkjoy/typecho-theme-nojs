<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

function themeConfig($form)
{
    // 主题色选择器
    $themeColor = new Typecho_Widget_Helper_Form_Element_Select(
        'themeColor',
        array(
            'red' => _t('经典红色（默认）'),
            'blue' => _t('优雅蓝色'),
            'green' => _t('清新绿色'),
            'purple' => _t('紫罗兰'),
            'orange' => _t('活力橙'),
            'gray' => _t('深灰色'),
            'pink' => _t('粉红色'),
            'cyan' => _t('青色'),
            'yellow' => _t('黄色'),
            'indigo' => _t('深蓝色'),
            'custom' => _t('自定义颜色')
        ),
        'red',
        _t('主题配色'),
        _t('选择主题的主要颜色方案')
    );
    $form->addInput($themeColor);

    // 自定义主题色
    $customPrimaryColor = new Typecho_Widget_Helper_Form_Element_Text(
        'customPrimaryColor',
        NULL,
        '#e74c3c',
        _t('自定义主题色'),
        _t('当选择"自定义颜色"时生效，填写十六进制颜色代码，例如：#e74c3c')
    );
    $form->addInput($customPrimaryColor);

    // 自定义悬停色
    $customHoverColor = new Typecho_Widget_Helper_Form_Element_Text(
        'customHoverColor',
        NULL,
        '#c0392b',
        _t('自定义悬停色'),
        _t('当选择"自定义颜色"时生效，填写十六进制颜色代码，例如：#c0392b')
    );
    $form->addInput($customHoverColor);
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
/**
 * 为文章内容生成目录并添加锚点
 *
 * 这个函数会查找文章中的所有 h1-h6 标签，
 * 在文章开头生成一个嵌套的目录列表，
 * 并为每个标题添加一个ID，用于目录链接跳转。
 *
 * @param string $content 原始文章内容
 * @return string 处理后的文章内容，包含目录和锚点
 */
function generateContentIndex($content)
{
    if (preg_match_all("/<h(\d)>(.*)<\/h\d>/isU", $content, $matches, PREG_SET_ORDER)) {
        $indexData = [];
        $minLevel = 6;
        foreach ($matches as $key => $match) {
            $level = (int)$match[1];      // 标题级别 (1-6)
            $title = trim($match[2]);     // 标题文本
            $anchorId = "toc_" . $key; // 为每个标题生成唯一的ID
            if ($level < $minLevel) {
                $minLevel = $level;
            }
            $originalTag = $match[0];
            $newTag = str_replace('>', " id=\"{$anchorId}\">", $originalTag);
            $content = str_replace($originalTag, $newTag, $content);
            $indexData[] = [
                'level' => $level,
                'link'  => "<a href=\"#{$anchorId}\">{$title}</a>"
            ];
        }
        $indexHtml = '';
        $currentLevel = 0; // 记录上一个目录项的级别
        foreach ($indexData as $item) {
            $level = $item['level'];
            if ($level > $currentLevel) {
                $indexHtml .= str_repeat("<ul>\n", $level - $currentLevel);
            }
            elseif ($level < $currentLevel) {
                $indexHtml .= str_repeat("</ul>\n", $currentLevel - $level);
            }
            $currentLevel = $level;
            $indexHtml .= "<li>{$item['link']}</li>\n";
        }
        $indexHtml .= str_repeat("</ul>\n", $currentLevel - $minLevel + 1);
        $content = "<div id=\"theContentIndex\">\n<h3>目录</h3>\n{$indexHtml}</div>\n" . $content;
    }
    return $content;
}

// ==================== 视频链接解析器 ====================

// 引入视频解析器
require_once('video-parser.php');

/**
 * 统一的内容处理函数
 * 按顺序处理：
 * 1. 视频链接解析（给链接添加 target="_blank" + 替换视频链接为播放器）
 * 2. 生成文章目录
 *
 * @param string $content 原始文章内容
 * @return string 处理后的文章内容
 */
function processArticleContent($content) {
    // 1. 先处理视频链接和添加 target="_blank"
    $content = VideoParser::parseContent($content);

    // 2. 再生成目录（这样目录链接也会有 target，但目录内链接不需要）
    $content = generateContentIndex($content);

    return $content;
}

/**
 * 获取主题颜色配置
 * 返回主题颜色的CSS类名和自定义颜色值
 */
function getThemeColorConfig() {
    $options = Typecho_Widget::widget('Widget_Options');

    $themeColor = $options->themeColor ? $options->themeColor : 'red';
    $customPrimaryColor = $options->customPrimaryColor ? $options->customPrimaryColor : '#e74c3c';
    $customHoverColor = $options->customHoverColor ? $options->customHoverColor : '#c0392b';

    return array(
        'theme' => $themeColor,
        'customPrimary' => $customPrimaryColor,
        'customHover' => $customHoverColor
    );
}