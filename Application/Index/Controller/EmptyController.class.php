<?php
/**
 * 空控制器，没有找到控制器会执行
 * @Author   罗江涛
 * @DateTime 2016-08-18T16:09:56+0800
 */
class EmptyController extends Controller{
	/**
	 * 默认方法
	 * @Author   罗江涛
	 * @DateTime 2016-08-18T16:28:39+0800
	 */
	public function index(){
		$this->display('Index/404');
	}
}
?>
