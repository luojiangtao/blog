<?php
/**
 * 文章控制器
 * @Author   罗江涛
 * @DateTime 2016-08-18T16:09:56+0800
 */
class ArticleController extends Controller
{
    /**
     * 文章列表
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:10:21+0800
     */
    public function article_list()
    {
        // 获取分页
        $p = isset($_GET['p']) ? $_GET['p'] : 1;
        $p = $p - 1;
        // 搜索关键字
        $keyword = I('keyword');
        // 防止报错
        $where = " 1=1 ";
        if ($keyword) {
            // 模糊匹配标题
            $where .= " and title like '%{$keyword}%'";
        }
        // 统计文章总数
        $count = M('article')->where($where)->count();
        // 分页
        $page = new Page($count, 10);
        // 获取文章
        $article = M('article')->order('article_id DESC')->where($where)->limit($page->start_row . ',' . $page->page_size)->select();
        // 实例化分类数据库类
        $category_db = M('category');
        foreach ($article as $key => $value) {
            $article[$key]['category_name'] = $category_db->where("category_id='{$value['category_id']}'")->get_field('category_name');
        }
        // 分配变量到前台模版
        $this->assign('article', $article);
        // 分页
        $this->assign('page', $page->show());
        // 载入模版
        $this->display();
    }

    /**
     * 文章添加，修改表单
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:12:06+0800
     */
    public function article_form()
    {
        // 不管有没有都接收并查询文章
        $article_id = I('article_id');
        $article    = M('article')->where("article_id='{$article_id}'")->find();
        $category   = M('category')->select();
        // 分配变量到前台模版
        $this->assign('article', $article);
        $this->assign('category', $category);
        // 载入模版
        $this->display();
    }

    /**
     * 添加文章
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:13:15+0800
     */
    public function add_article()
    {
        // 需要添加的数据
        $article = array(
            'title'       => I('title'),
            'content'     => I('content'),
            'category_id' => I('category_id'),
            'time'        => time(),
        );
        // 上传图片
        $upload    = new Upload();
        $file_info = $upload->upload('image');
        if ($file_info['filename']) {
            // 如果上传成功，保存图片名称
            $article['image'] = $file_info['filename'];
        }
        // 添加
        M('article')->add($article);
        // 回到列表页
        redirect(U('Article/article_list'));
    }

    /**
     * 修改文章
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:14:18+0800
     */
    public function edit_article()
    {
        $article_id = I('article_id');
        // 需要修改的数据
        $article = array(
            'title'       => I('title'),
            'content'     => I('content'),
            'category_id' => I('category_id'),
            'time'        => time(),
        );

        // 上传图片
        $upload    = new Upload();
        $file_info = $upload->upload('image');
        if ($file_info['filename']) {
            // 如果上传成功，保存图片名称
            $article['image'] = $file_info['filename'];
            // 获取以前的logo地址
            $image = M("article")->where("article_id='{$article_id}'")->get_field("image");
            if ($image) {
                // 补全以前的logo地址
                $image = "./Upload/" . $image;

                // 删除以前的logo地址
                @unlink($image);
            }
        }
        // 更新文章
        M('article')->where("article_id='{$article_id}'")->update($article);
        // 回到列表页
        redirect(U('Article/article_list'));
    }

    /**
     * 删除文章
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:15:14+0800
     */
    public function delete_article()
    {
        $article_id = I('article_id');
        $where      = "article_id='{$article_id}'";
        // 删除
        M('article')->where($where)->delete();
        // 回到列表页
        redirect(U('Article/article_list'));
    }
}
