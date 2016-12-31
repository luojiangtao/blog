<?php
/**
 * 文章控制器
 * @Author   罗江涛
 * @DateTime 2016-08-18T16:09:56+0800
 */
class TagController extends BaseController
{
    /**
     * 文章列表
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:10:21+0800
     */
    public function tag_list()
    {
        // 搜索关键字
        $keyword = I('keyword');
        // 防止报错
        $where = " 1=1 ";
        if ($keyword) {
            // 模糊匹配标题
            $where .= " and tag_name like '%{$keyword}%'";
        }
        // 统计文章总数
        $count = M('tag')->where($where)->count();
        // 分页
        $page = new Page($count, 10);
        // 获取文章
        $tag = M('tag')->order('tag_id DESC')->where($where)->limit($page->start_row . ',' . $page->page_size)->select();

        // 分配变量到前台模版
        $this->assign('tag', $tag);
        // 分页
        $this->assign('page', $page->show());
        // 载入模版
        $this->display();
    }

    /**
     * 删除文章
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:15:14+0800
     */
    public function delete_tag()
    {
        $tag_id = I('tag_id');
        $where      = "tag_id='{$tag_id}'";
        // 删除
        M('tag')->where($where)->delete();
        // 删除关系
        M('article_tag')->where($where)->delete();
        // 回到列表页
        redirect(U('Admin/Tag/tag_list'));
    }
}
