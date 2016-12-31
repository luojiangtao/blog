<?php
/**
 * 首页，文章控制器
 * @Author   罗江涛
 * @DateTime 2016-08-18T16:09:56+0800
 */
class ArticleController extends Controller
{
    /**
     * 文章详情页
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:30:45+0800
     */
    public function article_detail()
    {

        // 文章ID
        $article_id = intval(I('article_id', 0));
        $where      = " article_id='{$article_id}'";

        // 点击量+1
        M('article')->where($where)->set_inc('click_number');

        // 获取文章详情
        $article = M('article')->where($where)->find();
        if (!$article) {
            $this->__empty();
            return;
        }
        $article['category_name']  = M('category')->where("category_id={$article['category_id']}")->get_field('category_name');
        $article['comment_number'] = M('comment')->where("article_id='{$article_id}'")->count();
        $tags                      = M("article_tag at, tag t")->where("t.tag_id=at.tag_id and at.article_id={$article_id}")->field('tag_name')->select();
        $tags_array                = array();
        foreach ($tags as $key => $value) {
            $tags_array[] = $value['tag_name'];
        }

        // 相关文章推荐
        $recommend_article = M('article')->where("category_id='{$article['category_id']}' and status=1")->field('article_id,title')->order('click_number DESC')->limit('5')->select();

        $comment = M('comment')->where("article_id='{$article_id}'")->field('comment_id,nickname,content,time')->order('comment_id DESC')->limit('100')->select();

        // 分配变量到前台模版
        $this->assign('article', $article);
        $this->assign('tags_array', $tags_array);
        $this->assign('recommend_article', $recommend_article);
        $this->assign('comment', $comment);
        // p($article);die;
        // 载入模版
        $this->display();
    }

    /**
     * 空方法 没有找到方法时执行
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:31:40+0800
     */
    public function __empty()
    {
        $this->display('Index/404');
    }
}
