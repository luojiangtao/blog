<?php
/**
 * 文章控制器
 * @Author   罗江涛
 * @DateTime 2016-08-18T16:09:56+0800
 */
class ArticleController extends BaseController
{
    /**
     * 文章列表
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:10:21+0800
     */
    public function article_list()
    {
        // 搜索关键字
        $keyword = I('keyword');
        // 防止报错
        $where = " status=1 ";
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
        $comment_db = M('comment');
        foreach ($article as $key => $value) {
            $article[$key]['category_name'] = $category_db->where("category_id='{$value['category_id']}'")->get_field('category_name');
            $article[$key]['comment_number'] = $comment_db->where("article_id='{$value['article_id']}'")->count();
        }
        // 分配变量到前台模版
        $this->assign('article', $article);
        // 分页
        $this->assign('page', $page->show());
        // 载入模版
        $this->display();
    }

    /**
     * 文章列表
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:10:21+0800
     */
    public function article_recycle_list()
    {
        // 搜索关键字
        $keyword = I('keyword');
        // 防止报错
        $where = " status=0 ";
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
        $comment_db = M('comment');
        foreach ($article as $key => $value) {
            $article[$key]['category_name'] = $category_db->where("category_id='{$value['category_id']}'")->get_field('category_name');
            $article[$key]['comment_number'] = $comment_db->where("article_id='{$value['article_id']}'")->count();
        }
        // 分配变量到前台模版
        $this->assign('article', $article);
        // 分页
        $this->assign('page', $page->show());
        // 载入模版
        $this->display();
    }

    /**
     * 改变文章状态 status=0 放入回收站，status=1 放入正常列表
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:10:21+0800
     */
    public function change_status()
    {
        // 搜索关键字
        $status = I('status');
        $article_id = I('article_id');

        $article = array(
            'status'=>$status,
            );
        M('article')->where("article_id='{$article_id}'")->update($article);
        redirect($_SERVER['HTTP_REFERER']);
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
        if($article){
            $tags = M("article_tag at, tag t")->where("t.tag_id=at.tag_id and at.article_id={$article['article_id']}")->field('tag_name')->select();
            if($tags){
                $tags_array = array();
                foreach ($tags as $key => $value) {
                    $tags_array[] = $value['tag_name'];
                }
                // 标签
                $tags = implode('，', $tags_array);
            }else{
                $tags = '';
            }
            $article['tags'] = $tags;
        }
        
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
            'summary'     => I('summary'),
            'content'     => I('content'),
            'category_id' => I('category_id'),
            'time'        => time(),
        );
        $tags = I('tags');
        // 上传图片
        $upload    = new Upload();
        $file_info = $upload->upload('image');
        if ($file_info['filename']) {
            // 如果上传成功，保存图片名称
            $article['image'] = $file_info['filename'];
        }
        // 添加
        $article_id = M('article')->add($article);
        if (!$article_id) {
            $this->error('添加文章失败');
        }
        // 标签
        if($tags){
            $tags = str_replace('，', ',', $tags);
            $tags = explode(',', $tags);
            foreach ($tags as $key => $value) {
                $tag_id = M('tag')->where("tag_name='{$value}'")->get_field('tag_id');
                if(!$tag_id){
                    $data = array(
                        'tag_name'       => $value,
                    );
                    $tag_id = M('tag')->add($data);
                }
                $data = array(
                    'article_id'       => $article_id,
                    'tag_id'       => $tag_id,
                );
                M('article_tag')->add($data);
            }
        }
        // 回到列表页
        redirect(U('Admin/Article/article_list'));
    }

    /**
     * 修改文章
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:14:18+0800
     */
    public function edit_article()
    {
        $article_id = I('article_id');
        $tags = I('tags');
        // 需要修改的数据
        $article = array(
            'title'       => I('title'),
            'summary'     => I('summary'),
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

        if($tags){
            $tags = str_replace('，', ',', $tags);
            $tags = explode(',', $tags);
            // 删除全部文章和标签的关系
            M('article_tag')->where("article_id={$article_id}")->delete();
            foreach ($tags as $key => $value) {
                $tag_id = M('tag')->where("tag_name='{$value}'")->get_field('tag_id');
                if(!$tag_id){
                    $data = array(
                        'tag_name'       => $value,
                    );
                    $tag_id = M('tag')->add($data);
                }
                $data = array(
                    'article_id'       => $article_id,
                    'tag_id'       => $tag_id,
                );
                M('article_tag')->add($data);
            }
        }
        // 回到列表页
        redirect(U('Admin/Article/article_list'));
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
        redirect($_SERVER['HTTP_REFERER']);
    }
}
