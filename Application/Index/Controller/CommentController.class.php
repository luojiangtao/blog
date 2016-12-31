<?php
/**
 * 首页，评论控制器
 * @Author   罗江涛
 * @DateTime 2016-08-18T16:09:56+0800
 */
class CommentController extends Controller
{
    /**
     * 评论详情页
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:30:45+0800
     */
    public function add_comment()
    {
        $comment = array(
            'article_id' => I('article_id'),
            'nickname' => I('nickname'),
            'email'         => I('email'),
            'content'         => I('content'),
            'time'         => time(),
        );
        // 添加
        M('comment')->add($comment);
        // 回到列表页
        redirect($_SERVER['HTTP_REFERER']);
    }

}
