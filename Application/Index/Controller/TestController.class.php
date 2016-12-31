<?php
/**
 * 首页，文章控制器
 * @Author   罗江涛
 * @DateTime 2016-08-18T16:09:56+0800
 */
class TestController extends Controller
{
    /**
     * 首页
     * @Author   罗江涛
     * @DateTime 2016-08-18T16:29:13+0800
     */
    public function index()
    {	
        p($_GET);
        // 载入模版
        $this->display();
    }
    public function test()
    {   
        echo C('DEFAULT_TIME_ZONE');
    }

}
