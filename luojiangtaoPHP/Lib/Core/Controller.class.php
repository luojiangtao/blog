<?php
/**
 * 父类，用户的控制器都要继承
 * @Author   罗江涛
 * @DateTime 2016-08-16T14:26:53+0800
 */
class Controller extends Smarty
{

    /**
     * 构造方法
     * @Author   罗江涛
     * @DateTime 2016-08-16T14:26:53+0800
     */
    public function __construct()
    {
        parent::__construct();
        // echo ('父类构造方法123');
        // 框架初始化构造方法
        if (method_exists($this, '__init')) {
            $this->__init();
        }
        // 子类的子类的初始化构造方法
        if (method_exists($this, '__auto')) {
            $this->__auto();
        }
    }

    /**
     * 载入前端模版
     * @Author   罗江涛
     * @DateTime 2016-08-16T14:28:04+0800
     */
    public function display($file = null)
    {
        parent::display($file = null);
    }

    /**
     * 分配变量
     * @Author   罗江涛
     * @DateTime 2016-08-16T14:28:04+0800
     */
    public function assign($key, $value)
    {
        parent::assign($key, $value);
    }

    /**
     * 成功方法，跳转到成功页面
     * @Author   罗江涛
     * @DateTime 2016-08-16T14:28:04+0800
     */
    protected function success($message, $time = 3)
    {
        header('Content-type:text/html;charset=utf-8');
        include DATA_PATH . '/View/success.html';
        die;
    }

    /**
     * 错误方法，跳转到错误页面
     * @Author   罗江涛
     * @DateTime 2016-08-16T14:28:04+0800
     */
    protected function error($message = '错误', $time = 3)
    {
        header('Content-type:text/html;charset=utf-8');
        include DATA_PATH . '/View/error.html';
        die;
    }
}
