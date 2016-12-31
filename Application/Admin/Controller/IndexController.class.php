<?php
/**
 * 后台首页控制器
 * @Author   罗江涛
 * @DateTime 2016-08-16T20:25:48+0800
 */
class IndexController extends BaseController
{
    /**
     * 后台首页
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:23:15+0800
     */
    public function index()
    {
        $this->display();
    }

    /**
     * 修改密码
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:24:39+0800
     */
    public function change_password()
    {
        // 如果不是POST提交，显示模版
        if (!IS_POST) {
            $this->display();
            return;
        }
        $admin_id=$_SESSION['admin_id'];

        $data=array(
            'password'=>md5(I('password'))
            );
        M('admin')->where("admin_id='{$admin_id}'")->update($data);
        // 跳转到后台首页
        redirect(U("Index/index"));
    }
    
}
