<?php
/**
 * 栏目控制器
 * @Author   罗江涛
 * @DateTime 2016-08-16T20:25:48+0800
 */
class CategoryController extends BaseController
{
    /**
     * 栏目列表
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:18:03+0800
     */
    public function category_list()
    {
        // 统计栏目数量
        $count = M('category')->count();
        // 分页
        $page = new Page($count, 10);
        // 获取栏目数据
        $category_list = M('category')->order('`order` ASC')->limit($page->start_row . ',' . $page->page_size)->select();
        // 分配变量到前台模版
        $this->assign('category_list', $category_list);
        $this->assign('page', $page->show());
        // 载入模版
        $this->display();
    }

    /**
     * 栏目添加修改表单
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:20:38+0800
     */
    public function category_form()
    {
        // 不管有没有都接收并查询文章
        $category_id = I('category_id');
        $category    = M('category')->where("category_id='{$category_id}'")->find();
        // 分配变量到前台模版
        $this->assign('category', $category);
        // 载入模版
        $this->display();
    }
    
    /**
     * 添加栏目
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:21:14+0800
     */
    public function add_category()
    {
        $category = array(
            'category_name' => I('category_name'),
            'order'         => I('order'),
        );
        // 添加
        M('category')->add($category);
        // 回到列表页
        redirect(U('Category/category_list'));
    }

    /**
     * 修改栏目
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:21:36+0800
     * @return   [type]                   [description]
     */
    public function edit_category()
    {
        $category_id = I('category_id');
        $category    = array(
            'category_name' => I('category_name'),
            'order'         => I('order'),
        );
        // 修改栏目
        M('category')->where("category_id='{$category_id}'")->update($category);
        // 回到列表页
        redirect(U('Category/category_list'));
    }

    /**
     * 删除栏目
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:21:55+0800
     * @return   [type]                   [description]
     */
    public function delete_category()
    {
        $category_id = I('category_id');

        $where = "category_id='{$category_id}'";
        // 查看是否该分类下面有文章，如果有请将文章转移或删除后在删除该分类
        $article = M('article')->where($where)->count();
        if ($article > 0) {
            $this->error('该分类下面有文章，请将文章转移或删除后在删除该分类');
        }
        // 删除
        M('category')->where($where)->delete();
        // 回到列表页
        redirect(U('Category/category_list'));
    }
}
