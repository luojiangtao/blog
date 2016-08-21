<?php
/**
 * 基类
 * @Author   罗江涛
 * @DateTime 2016-08-16T20:25:48+0800
 */
class BaseController extends Controller
{
    /**
     * 框架初始化方法，如果没有登录跳转到登录页面
     * @Author   罗江涛
     * @DateTime 2016-08-16T20:25:48+0800
     */
    public function __init()
    {
        // 如果没有登录，跳转到登录页
        if (!isset($_SESSION['admin_id'])) {
            redirect(U('Login/login'));
        }
    }

    /**
     * 错误方法
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:16:01+0800
     * @param    [type]                   $message [错误信息]
     */
    public function error($message='错误', $time=3)
    {
        // 分配错误信息到前台模版
        $this->assign('message', $message);
        // 载入模版
        $this->display('Index/error');
        // 停止运行
        die;
    }
}
