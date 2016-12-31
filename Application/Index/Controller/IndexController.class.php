<?php
/**
 * 首页，文章控制器
 * @Author   罗江涛
 * @DateTime 2016-08-18T16:09:56+0800
 */
class IndexController extends Controller
{
    /**
     * 首页
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:29:13+0800
     */
    public function index()
    {	
        // 栏目ID
    	$category_id = intval(I('category_id', 0));
        // 关键字
    	$keyword = I('keyword');
        // 防止报错
    	$where = ' status=1 ';
    	if($category_id){
            // 查询某个栏目的文章
    		$where .=" and category_id='{$category_id}'";
    	}
    	if($keyword){
            // 模糊匹配标题
    		$where .=" and title like '%{$keyword}%'";
    	}
        
        // 获取文章
    	$article=M('article')->where($where)->field('article_id,title,summary,time,image,category_id,click_number')->order('article_id DESC')->limit('0,10')->select();

        foreach ($article as $key => $value) {
            $article[$key]['category_name']=M('category')->field('category_name')->where("category_id={$value['category_id']}")->get_field('category_name');
            $article[$key]['comment_number'] = M('comment')->where("article_id='{$value['article_id']}'")->count();
        }
        
        $carousel_figure=M('carousel_figure')->order('`sort` ASC')->select();
        // 分配变量到前台模版
        $this->assign('carousel_figure', $carousel_figure);
        $this->assign('article', $article);
        // p($article);die;
        // 载入模版
        $this->display();
    }

    /**
     * 首页
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:29:13+0800
     */
    public function ajax_get_article_list()
    {   
        // p(U('1', '', '.html'));die;
        // 栏目ID
        $category_id = intval(I('category_id', 0));
        // 关键字
        $keyword = I('keyword');
        $page = I('page', 0);
        // 防止报错
        $where = ' status=1 ';
        if($category_id){
            // 查询某个栏目的文章
            $where .=" and category_id='{$category_id}'";
        }
        if($keyword){
            // 模糊匹配标题
            $where .=" and title like '%{$keyword}%'";
        }

        // 获取文章
        $article=M('article')->where($where)->field('article_id,title,time,image,category_id,click_number')->order('article_id DESC')->limit("{$page},2")->select();
        if(!$article){
            $result = array(
                'status'=>0,
                'message'=>'没有找到文章',
                );
            echo json_encode($result);
            return;
        }
        foreach ($article as $key => $value) {
            $article[$key]['category_name']=M('category')->field('category_name')->where("category_id={$value['category_id']}")->get_field('category_name');
            $article[$key]['comment_number'] = M('comment')->where("article_id='{$value['category_id']}'")->count();
        }
        // 分配变量到前台模版
        $result = array(
            'status'=>1,
            'message'=>'查询文章成功',
            'data'=>$article,
            );
        echo json_encode($result);
        // p($article);die;
        // 载入模版
    }

    /**
     * 文章详情页
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:30:45+0800
     */
    public function article_detail()
    {
        // 文章ID
        $article_id = intval(I('article_id', 0));
    	$where =" article_id='{$article_id}'";
        // 获取文章详情
    	$article=M('article')->where($where)->find();
        // 点击量+1
        M('article')->where($where)->set_inc('click_number');
        // 获取栏目数据
    	$category=M('category')->order('`sort` ASC')->select();
        // 分配变量到前台模版
        $this->assign('category', $category);
        $this->assign('article', $article);
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

    /**
     * 测试用
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:32:04+0800
     */
    public function test()
    {
        $name='tao';
        $this->assign('name', $name);
        $this->display();
    }
}
