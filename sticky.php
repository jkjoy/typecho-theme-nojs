<?php
// 在首页、分类页和标签页都显示置顶文章
if ($this->is('index')) {
    $sticky_cids = getStickyPostsCids();
    
    // 如果在分类或标签页面，只显示当前分类/标签下的置顶文章
    if (($this->is('category') || $this->is('tag')) && isset($this->mid)) {
        $db = Typecho_Db::get();
        // 获取当前分类/标签下的文章CID
        $currentPostsCids = $db->fetchAll($db->select('cid')
            ->from('table.relationships')
            ->where('mid = ?', $this->mid));
        $currentPostsCids = array_column($currentPostsCids, 'cid');
        // 只保留属于当前分类/标签的置顶文章
        $sticky_cids = array_intersect($sticky_cids, $currentPostsCids);
    }
    
    if (count($sticky_cids) > 0) {
        $db = Typecho_Db::get();
        $pageSize = $this->options->pageSize;
        
        // 查询置顶文章
        $select1 = $this->select()->where('type = ?', 'post');
        // 查询普通文章
        $select2 = $this->select()
            ->where('type = ?', 'post')
            ->where('status = ?', 'publish')
            ->where('created < ?', time());
        
        // 重置文章列表
        $this->row = [];
        $this->stack = [];
        $this->length = 0;
        
        // 构建置顶文章的排序
        $order = '';
        foreach ($sticky_cids as $i => $cid) {
            if ($i == 0) {
                $select1->where('cid = ?', $cid);
            } else {
                $select1->orWhere('cid = ?', $cid);
            }
            $order .= " when $cid then $i";
            $select2->where('table.contents.cid != ?', $cid);
        }
        
        // 设置置顶文章的排序
        if ($order) {
            $select1->order('', "(case cid$order end)");
        }
        
        // 在第一页显示置顶文章
        if ($this->_currentPage == 1) {
            foreach ($db->fetchAll($select1) as $sticky_post) {
                $this->push($sticky_post); // 添加置顶文章到开头
            }
        }
        
        // 处理当前用户的私有文章
        $uid = $this->user->uid;
        if ($uid) {
            $select2->orWhere('authorId = ?', $uid)->where('status = ?', 'private');
        }
        
        // 获取普通文章并按时间倒序排列
        $sticky_posts = $db->fetchAll(
            $select2->order('table.contents.created', Typecho_Db::SORT_DESC)
                   ->page($this->_currentPage, $this->parameter->pageSize)
        );
        
        // 添加普通文章
        foreach ($sticky_posts as $sticky_post) {
            $this->push($sticky_post);
        }
        
        // 更新总文章数（减去置顶文章数）
        $this->setTotal($this->getTotal() - count($sticky_cids));
    }
    
} else {
    // 在归档等其他页面不显示置顶文章
    return;
} 
?>