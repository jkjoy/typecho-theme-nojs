## 主题

移植`hexo`主题`Nojs`

## 预览

![预览](/screenshot.webp)

## 说明

极简单的后台设置

让博客更专注于文字内容的呈现

为避免重复查询数据库,所有统计数缓存1小时,非实时刷新

在`usr` - `cache` 下生成`site_stats.cache`缓存文件,删除该文件会立即更新统计数据


### 使用

将`typecho-theme-nojs`文件夹上传至`usr/themes/`目录下，在后台外观中启用主题即可。

建议给于`usr`目录下的`cache`文件夹写权限。

- 设置独立页面`全部分类`slug为`categories`
- 设置独立页面`全部标签`slug为`tags`
- 设置独立页面`文章归档`slug为`archives`
- 设置独立页面`友情链接`slug为`links`