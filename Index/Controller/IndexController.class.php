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
    	$where = ' 1=1 ';
    	if($category_id){
            // 查询某个栏目的文章
    		$where .=" and category_id='{$category_id}'";
    	}
    	if($keyword){
            // 模糊匹配标题
    		$where .=" and title like '%{$keyword}%'";
    	}

        // 统计文章总数
        $count = M('article')->count();
        // 分页
        $page = new Page($count,12);
        // 获取栏目
    	$category=M('category')->order('`order` ASC')->select();
        // 获取文章
    	$article=M('article')->where($where)->order('article_id DESC')->limit($page->start_row.','.$page->page_size)->select();
        // 分配变量到前台模版
        $this->assign('category', $category);
        $this->assign('article', $article);
        $this->assign('page', $page->show());
        // 载入模版
        $this->display();
    }

    /**
     * 文章详情页
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:30:45+0800
     */
    public function article_detail()
    {
        // 文章ID
    	$article_id = intval(I('article_id', 1));
    	$where =" article_id='{$article_id}'";
        // 获取文章详情
    	$article=M('article')->where($where)->find();
        // 点击量+1
        M('article')->where($where)->set_inc('click_number');
        // 获取栏目数据
    	$category=M('category')->order('`order` ASC')->select();
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
        header("Content-type:text/html;charset=utf-8");
        echo "页面没找到，<a href='".__ROOT__."'>回到首页</a>";
    }

    /**
     * 测试用
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:32:04+0800
     */
    public function test()
    {
        print_const();
        $name='tao';
        $this->assign('name', $name);
        $this->display();
    }
}
