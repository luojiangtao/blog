<?php
/**
 * 分类模型
 * @Author   罗江涛
 * @DateTime 2016-08-18T16:09:56+0800
 */
class CategoryModel extends Model
{
	public $table="category";

	/**
	 * 获取所有栏目
	 * @Author   罗江涛
	 * @DateTime 2016-08-18T16:26:20+0800
	 * @return   [type]                   [栏目数据]
	 */
	public function get_all(){
		$category = M('category')->select();
		return $category;
	}
}
?>