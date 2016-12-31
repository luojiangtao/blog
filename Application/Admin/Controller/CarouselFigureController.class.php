<?php
/**
 * 轮播图控制器
 * @Author   罗江涛
 * @DateTime 2016-08-18T16:09:56+0800
 */
class CarouselFigureController extends BaseController
{
    /**
     * 轮播图列表
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:10:21+0800
     */
    public function carousel_figure_list()
    {
        // 获取轮播图
        $carousel_figure_list = M('carousel_figure')->order('sort ASC')->select();
        $this->assign('carousel_figure_list', $carousel_figure_list);
        // 载入模版
        $this->display();
    }

    /**
     * 轮播图添加，修改表单
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:12:06+0800
     */
    public function carousel_figure_form()
    {
        // 不管有没有都接收并查询轮播图
        $carousel_figure_id = I('carousel_figure_id', 0);
        $carousel_figure    = M('carousel_figure')->where("carousel_figure_id='{$carousel_figure_id}'")->find();
        // 分配变量到前台模版
        $this->assign('carousel_figure', $carousel_figure);
        // 载入模版
        $this->display();
    }

    /**
     * 添加轮播图
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:13:15+0800
     */
    public function add_carousel_figure()
    {
        // 需要添加的数据
        $carousel_figure = array(
            'url'     => I('url'),
            'sort' => I('sort'),
        );
        // 上传图片
        $upload    = new Upload();
        $file_info = $upload->upload('image');
        if ($file_info['filename']) {
            // 如果上传成功，保存图片名称
            $carousel_figure['image'] = $file_info['filename'];
        }
        // 添加
        M('carousel_figure')->add($carousel_figure);
        // 回到列表页
        redirect(U('Admin/CarouselFigure/carousel_figure_list'));
    }

    /**
     * 修改轮播图
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:14:18+0800
     */
    public function edit_carousel_figure()
    {
        $carousel_figure_id = I('carousel_figure_id');
        // 需要修改的数据
        $carousel_figure = array(
            'url'     => I('url'),
            'sort' => I('sort'),
        );

        // 上传图片
        $upload    = new Upload();
        $file_info = $upload->upload('image');
        if ($file_info['filename']) {
            // 如果上传成功，保存图片名称
            $carousel_figure['image'] = $file_info['filename'];
            // 获取以前的logo地址
            $image = M("carousel_figure")->where("carousel_figure_id='{$carousel_figure_id}'")->get_field("image");
            if ($image) {
                // 补全以前的logo地址
                $image = "./Upload/" . $image;
                // 删除以前的logo地址
                @unlink($image);
            }
        }
        // 更新轮播图
        M('carousel_figure')->where("carousel_figure_id='{$carousel_figure_id}'")->update($carousel_figure);
        // 回到列表页
        redirect(U('Admin/CarouselFigure/carousel_figure_list'));
    }

    /**
     * 删除轮播图
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:15:14+0800
     */
    public function delete_carousel_figure()
    {
        $carousel_figure_id = I('carousel_figure_id');
        // 获取以前的logo地址
        $image = M("carousel_figure")->where("carousel_figure_id='{$carousel_figure_id}'")->get_field("image");
        if ($image) {
            // 补全以前的logo地址
            $image = "./Upload/" . $image;
            // 删除以前的logo地址
            @unlink($image);
        }
        $where      = "carousel_figure_id='{$carousel_figure_id}'";
        // 删除
        M('carousel_figure')->where($where)->delete();
        // 回到列表页
        redirect(U('Admin/CarouselFigure/carousel_figure_list'));
    }
}
