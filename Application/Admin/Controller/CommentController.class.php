<?php
/**
 * 评论控制器
 * @Author   罗江涛
 * @DateTime 2016-08-18T16:09:56+0800
 */
class CommentController extends BaseController
{
    /**
     * 评论列表
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:10:21+0800
     */
    public function comment_list()
    {
        // 搜索关键字
        $keyword = I('keyword');
        // 防止报错
        $where = " 1=1 ";
        if ($keyword) {
            // 模糊匹配标题
            $where .= " and (nickname like '%{$keyword}%' or email like '%{$keyword}%' or content like '%{$keyword}%')";
        }
        // 统计评论总数
        $count = M('comment')->where($where)->count();
        // 分页
        $page = new Page($count, 10);
        // 获取评论
        $comment = M('comment')->order('comment_id DESC')->where($where)->limit($page->start_row . ',' . $page->page_size)->select();

        // 分配变量到前台模版
        $this->assign('comment', $comment);
        // 分页
        $this->assign('page', $page->show());
        // 载入模版
        $this->display();
    }

    /**
     * 删除评论
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:15:14+0800
     */
    public function delete_comment()
    {
        $comment_id = I('comment_id');
        $where      = "comment_id='{$comment_id}'";
        // 删除
        M('comment')->where($where)->delete();
        // 回到列表页
        redirect(U('Admin/Comment/comment_list'));
    }
}
