<?php
/**
 * 视频链接解析器
 * 支持 YouTube、Bilibili、优酷
 */

class VideoParser {

    /**
     * 处理文章内容
     * 1. 给所有a标签添加 target="_blank"
     * 2. 将视频链接替换为播放器
     */
    public static function parseContent($content) {
        if (empty($content)) {
            return $content;
        }

        // 1. 给所有a标签添加 target="_blank"
        $content = self::addBlankTarget($content);

        // 2. 替换视频链接为播放器
        $content = self::replaceVideoLinks($content);

        return $content;
    }

    /**
     * 给所有a标签添加 target="_blank"
     */
    private static function addBlankTarget($content) {
        // 使用DOMDocument处理HTML
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8">' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $links = $dom->getElementsByTagName('a');
        foreach ($links as $link) {
            // 添加 target="_blank"
            $link->setAttribute('target', '_blank');
            // 添加安全属性
            $link->setAttribute('rel', 'noopener noreferrer');
        }

        $content = $dom->saveHTML();
        // 移除XML声明
        $content = str_replace('<?xml encoding="UTF-8">', '', $content);

        return $content;
    }

    /**
     * 替换视频链接为播放器
     */
    private static function replaceVideoLinks($content) {
        // 处理a标签中的视频链接
        $content = preg_replace_callback(
            '/<a\s+[^>]*href=["\']([^"\']*)["\'][^>]*>.*?<\/a>/i',
            function($matches) {
                $url = $matches[1];
                $videoInfo = self::extractVideoInfo($url);

                if ($videoInfo) {
                    return self::generatePlayer($videoInfo);
                }

                return $matches[0]; // 保持原样
            },
            $content
        );

        // 处理纯文本中的视频链接
        $content = preg_replace_callback(
            '/https?:\/\/(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/|bilibili\.com\/video\/|v\.youku\.com\/v_show\/id_)[^\s<]+/i',
            function($matches) {
                $url = $matches[0];
                $videoInfo = self::extractVideoInfo($url);

                if ($videoInfo) {
                    return self::generatePlayer($videoInfo);
                }

                return $matches[0]; // 保持原样
            },
            $content
        );

        return $content;
    }

    /**
     * 提取视频信息
     */
    private static function extractVideoInfo($url) {
        // YouTube
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/i', $url, $matches)) {
            return [
                'platform' => 'youtube',
                'videoId' => $matches[1]
            ];
        }

        // Bilibili - BV号
        if (preg_match('/bilibili\.com\/video\/(BV\w+)/i', $url, $matches)) {
            return [
                'platform' => 'bilibili',
                'videoId' => $matches[1]
            ];
        }

        // Bilibili - AV号
        if (preg_match('/bilibili\.com\/video\/(av\d+)/i', $url, $matches)) {
            return [
                'platform' => 'bilibili',
                'videoId' => $matches[1]
            ];
        }

        // 优酷
        if (preg_match('/v\.youku\.com\/v_show\/id_(.+)\.html/i', $url, $matches)) {
            return [
                'platform' => 'youku',
                'videoId' => $matches[1]
            ];
        }

        return null;
    }

    /**
     * 生成播放器HTML
     */
    private static function generatePlayer($videoInfo) {
        $platform = $videoInfo['platform'];
        $videoId = $videoInfo['videoId'];
        $embedUrl = self::getEmbedUrl($platform, $videoId);

        $platformLabel = strtoupper($platform);

        $html = '<div class="video-player-wrapper">';
        $html .= '<div class="platform-label label-' . $platform . '">' . $platformLabel . '</div>';
        $html .= '<div class="player-container ' . $platform . '">';
        $html .= '<iframe src="' . htmlspecialchars($embedUrl) . '" ';
        $html .= 'allowfullscreen ';
        $html .= 'allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" ';
        $html .= 'style="width: 100%; height: 500px; border: none;">';
        $html .= '</iframe>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * 获取嵌入URL
     */
    private static function getEmbedUrl($platform, $videoId) {
        switch($platform) {
            case 'youtube':
                return 'https://www.youtube.com/embed/' . $videoId;
            case 'bilibili':
                return '//player.bilibili.com/player.html?bvid=' . $videoId . '&high_quality=1';
            case 'youku':
                return 'https://player.youku.com/embed/' . $videoId;
            default:
                return '';
        }
    }
}
