<?php
/**
 * 登录，退出登录
 * @Author   罗江涛
 * @DateTime 2016-08-16T20:25:48+0800
 */
class LoginController extends Controller
{
    /**
     * 登录
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:23:42+0800
     */
    public function login()
    {
        // 如果不是POST提交，显示模版
        if (!IS_POST) {
            $this->display();
            return;
        }

        //有数据提交
        $username = $_POST["username"];
        // md5加密
        $password = md5($_POST["password"]);

        // 根据传值查找管理员
        $admin = M('admin')->where("username='{$username}'")->find();

        if ($admin['password'] == $password) {
            // 如果密码正确保存管理的昵称，和ID，
            $_SESSION['admin_id'] = $admin["admin_id"];
            $_SESSION['username'] = $admin["username"];
            // 跳转到后台首页
            redirect(U("Index/index"));
        } else {
            // 如果密码不准确，报错，并返回
            $this->assign('errorMessage', '用户名或密码不正确，请重试');
            $this->display();
        }
    }

    /**
     * 退出登录
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:24:39+0800
     */
    public function logout()
    {
        // 清除session
        unset($_SESSION['admin_id']);
        unset($_SESSION['username']);
        // 跳转到登录页
        redirect(U("Login/login"));
    }
}
