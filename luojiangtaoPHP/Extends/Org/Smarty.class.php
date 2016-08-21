<?php
/**
 * [Smarty模版引擎]
 * @Author   罗江涛
 * @DateTime 2016-08-12T16:54:47+0800
 */
class Smarty
{
    // 存放assign分配的变量
    public $array = array();
    // 模版文件目录
    public $view_dir = APP_VIEW_PATH;
    // 编译文件目录
    public $compile_dir = COMPILE_PATH;
    // 缓存文件目录
    public $cache_dir = CACHE_PATH;
    // 是否开启缓存
    // public $caching = true;
    public $caching = false;
    // public $caching = C('IS_CACHE');

    // 构造方法
    public function __construct()
    {
    	// 检查文件夹是否存在，不存在则创建
        $this->check_dir();
    }
    /**
     * 检查文件夹是否存在，不存在则创建
     * @Author   罗江涛
     * @DateTime 2016-08-12T17:20:52+0800
     * @return   [type]                   [description]
     */
    private function check_dir()
    {
        // 检查文件夹是否存在，不存在则创建
        is_dir($this->view_dir) || mkdir($this->view_dir, 0777, true);
        is_dir($this->compile_dir) || mkdir($this->compile_dir, 0777, true);
        is_dir($this->cache_dir) || mkdir($this->cache_dir, 0777, true);
    }

    /**
     * 分配变量到模版页面
     * @Author   罗江涛
     * @DateTime 2016-08-12T17:21:42+0800
     * @param    [type]                   $key   [变量的名称]
     * @param    [type]                   $value [变量的值]
     */
    public function assign($key, $value)
    {	
    	// 变量保存到数组中
        $this->array["$key"] = $value;
    }

    /**
     * 载入模版
     * @Author   罗江涛
     * @DateTime 2016-08-12T17:22:17+0800
     * @param    [type]                   $file [模版文件名]
     * @return   [type]                         [description]
     */
    public function display($file=NULL)
    {
        // 默认编码utf-8
        header("Content-type:text/html;charset=utf-8");
        // 模版文件路径
        $view_path = "";
        if(is_null($file)){
            // 默认是当前控制器下当前方法的名称
            $view_path = $this->view_dir . "/" . CONTROLLER . "/" . ACTION . ".html";
        }else{

            if(strstr($file, '/')){
                // 如果包含/，则拆分  $this->display("Index/index");
                $temp = explode('/', $file);
                $controller = $temp[0];
                $action = $temp[1];
                $suffix = strrchr($action, ".");
                // 默认.html
                $action = empty($suffix) ? $action . ".html" : $action;
                // 组合全路径
                $view_path = $this->view_dir . "/" . $controller . "/" . $action;
            }else{
                // 如果不包含/，则默认当前控制器名称  $this->display("index");
                // 获取后缀名
                $suffix = strrchr($file, ".");
                // 默认.html
                $action = empty($suffix) ? $file . ".html" : $file;
                // 组合全路径
                $view_path = $this->view_dir . "/" . CONTROLLER . "/" . $action;
            }
            
        }
        if(!is_file($view_path)) halt($view_path . "模版文件不存在");
        // 将assign分配的变量，保存在数组中的转化为变量
        extract($this->array);
        // 组合模版文件目录
        // $view_path = $this->view_dir . '/' . $view_path;
        if (!file_exists($view_path)) {
            die('模版文件: ' . $view_path . ' 不存在');
        }

        // 编译文件路径
        $compile_file = $this->compile_dir . '/' . md5($view_path) . ".html";
        //只有当编译文件不存在或者是模板文件被修改过了才重新编译文件
        if (!file_exists($compile_file) || filemtime($compile_file) < filemtime($view_path)) {
            // 获取模版文件
            $html           = file_get_contents($view_path);
        	// 替换所有模版标签，包括{$value} <foreach> <if><elseif></if> 为PHP代码
            $html           = $this->replace_all($html);
            // 保存缓存文件
            file_put_contents($compile_file, $html);
        }

        //开启了缓存才加载缓存文件，否则直接加载编译文件
        if($this->caching){
        	// 编译文件路径
        	$cache_file = $this->cache_dir . '/' . md5($file) . ".html";
        	//只有当缓存文件不存在，或者编译文件已被修改过,则重新生成缓存文件
        	if(!file_exists($cache_file) || filemtime($cache_file)<filemtime($compile_file)){
        		// 载入编译文件并执行
        		include $compile_file;
        		// 执行$compile_file编译文件后，内容输出到缓存区，不会从输出到屏幕。
        		$content = ob_get_clean();
        		// 保存缓存文件
        		if(!file_put_contents($cache_file, $content)){
        			die('保存缓存文件出错，请检查缓存文件夹写权限');
        		}
        	}
        	// 开启缓存，引入缓存文件,并执行
        	include $cache_file;
        }else{
        	// 没开启缓存，引入编译文件,并执行
        	include $compile_file;
        }
    }


    /*-------------------------以下为解析方法--------------------------------*/

    /**
     * 替换全部模版标签为PHP代码
     * {$name} -> <?php echo $name ?>
     * @Author   罗江涛
     * @DateTime 2016-08-09T17:13:37+0800
     * @return   [type]                   [替换后的html]
     */
    private function replace_all($html){
        // 替换include标签为引入文件的类容
        $html = $this->replace_include($html);
        // 替换普通变量标签为PHP代码
        $html = $this->replace_value($html);
        // 替换if标签为PHP代码
        $html = $this->replace_if($html);
        // 替换elseif标签为PHP代码
        $html = $this->replace_elseif($html);
        // 替换else标签为PHP代码
        $html = $this->replace_else($html);
        // 替换endif标签为PHP代码
        $html = $this->replace_endif($html);
        // 替换foreach 循环标签为PHP代码
        $html = $this->replace_foreach($html);
        // 替换endforeach 循环标签为PHP代码
        $html = $this->replace_endforeach($html);
        return $html;
    }
    
    /**
     * 替换普通变量标签为PHP代码
     * {$name} -> <?php echo $name ?>
     * @Author   罗江涛
     * @DateTime 2016-08-09T17:13:37+0800
     * @return   [type]                   [替换后的html]
     */
    private function replace_value($html){
        // 普通变量 {$name}
        $preg = '/\{\$(.+?)\}/';
        $rep='<?php echo $$1; ?>';
        // 标签替换，替换所有 {} 包含的内容
        $html = preg_replace($preg, $rep, $html);

        // 使用函数 {:U('Index/index')}
        $preg = '/\{:(.+?)\}/';
        $rep='<?php echo $1; ?>';
        // 标签替换，替换所有 {} 包含的内容
        $html = preg_replace($preg, $rep, $html);

        // 替换常量 __ROOT__
        $preg = '/__(.+?)__/';
        $rep='<?php echo __$1__; ?>';
        // 标签替换，替换所有 {} 包含的内容
        $html = preg_replace($preg, $rep, $html);

        return $html;
    }

    /**
     * 替换foreach 循环标签为PHP代码
     * <foreach name='person' item='v' key='k'>  ->  <?php if(is_array($person)):  foreach($person as $k=>$v): ?>
     * @Author   罗江涛
     * @DateTime 2016-08-09T17:13:37+0800
     * @return   [type]                   [替换后的html]
     */
    private function replace_foreach($html){
        // 找出判断条件
        $preg = '/<foreach.+?name=(\'|\")(.+?)(\'|\").+?>/';
        preg_match_all($preg, $html, $matches);
        $count = count($matches[0]);
        // 统计匹配到的次数并循环单次替换，防止第一个匹配的值把后面的覆盖了
        while ($count) {
            $preg = '/<foreach.+?name=(\'|\")(.+?)(\'|\").+?>/';
            preg_match($preg,$html, $match);
            $name = empty($match[2]) ? '' : $match[2];

            // 找出键名 默认 k
            $preg = '/<foreach.+?key=(\'|\")(.+?)(\'|\").+?>/';
            preg_match($preg,$html, $match);
            $key = empty($match[2]) ? 'k' : $match[2];

            // 找出值名 默认 v
            $preg = '/<foreach.+?item=(\'|\")(.+?)(\'|\").+?>/';
            preg_match($preg,$html, $match);
            $item = empty($match[2]) ? 'v' : $match[2];

            $preg = '/<foreach(.+?)>/';
            // 标签替换
            $rep='<?php if(is_array($'.$name.')):  foreach($'.$name.' as $'.$key.'=>$'.$item.'): ?>';
            $html = preg_replace($preg, $rep, $html, 1);
            $count--;
        }
        return $html;
    }

    /**
     * 替换endforeach 循环标签为PHP代码
     * </foreach>  ->  <?php endforeach; endif; ?>
     * @Author   罗江涛
     * @DateTime 2016-08-09T17:13:37+0800
     * @return   [type]                   [替换后的html]
     */
    private function replace_endforeach($html){
        $preg = '/<\/foreach>/';
        $rep='<?php endforeach; endif; ?>';
        // 标签替换
        $html = preg_replace($preg, $rep, $html);
        return $html;
    }

    /**
     * 替换if标签为PHP代码
     * <if condition="$person[0]['name']=='taotao'">  -> <?php if($person[0]['name']=='taotao'): ?>
     * @Author   罗江涛
     * @DateTime 2016-08-09T17:13:37+0800
     * @return   [type]                   [替换后的html]
     */
    private function replace_if($html){
        // 找出判断条件
        $preg = '/<if condition=(\'|\")(.+?)(\'|\")>/';
        preg_match_all($preg, $html, $matches);
        $count = count($matches[0]);
        // 统计匹配到的次数并循环单次替换，防止第一个匹配的值把后面的覆盖了
        while ($count) {
            preg_match($preg,$html, $match);
            $condition = empty($match[2]) ? '' : $match[2];
            $rep="<?php if(".$condition."): ?>";
            // 标签替换
            $html = preg_replace($preg, $rep, $html, 1);
            $count--;
        }
        return $html;
    }

    /**
     * 替换elseif标签为PHP代码
     * <elseif condition="$person[0]['name']=='taotao2'"/> -> <?php elseif($person[0]['name']=='taotao2'): ?>
     * @Author   罗江涛
     * @DateTime 2016-08-09T17:13:37+0800
     * @return   [type]                   [替换后的html]
     */
    private function replace_elseif($html){
        // 找出判断条件
        $preg = '/<elseif condition=(\'|\")(.+?)(\'|\")\s?\/>/';
        preg_match_all($preg, $html, $matches);
        $count = count($matches[0]);
        // 统计匹配到的次数并循环单次替换，防止第一个匹配的值把后面的覆盖了
        while ($count) {
            preg_match($preg,$html, $match);
            $condition = empty($match[2]) ? '' : $match[2];
            $rep="<?php elseif(".$condition."): ?>";
            // 标签替换
            $html = preg_replace($preg, $rep, $html, 1);
            $count--;
        }
        return $html;
    }

    /**
     * 替换else标签为PHP代码
     * <else /> -> <?php else: ?>
     * @Author   罗江涛
     * @DateTime 2016-08-09T17:13:37+0800
     * @return   [type]                   [替换后的html]
     */
    private function replace_else($html){
        $preg = '/<else\s?\/>/';
        $rep="<?php else: ?>";
        // 标签替换
        $html = preg_replace($preg, $rep, $html);
        return $html;
    }
    /**
     * 替换endif标签为PHP代码
     * </if> -> <?php endif; ?>
     * @Author   罗江涛
     * @DateTime 2016-08-09T17:13:37+0800
     * @return   [type]                   [替换后的html]
     */
    private function replace_endif($html){
        $preg = '/<\/if>/';
        $rep="<?php endif; ?>";
        // 标签替换
        $html = preg_replace($preg, $rep, $html);
        return $html;
    }

    /**
     * 替换include标签为被引入文件的内容
     * @Author   罗江涛
     * @DateTime 2016-08-09T17:13:37+0800
     * @return   [type]                   [替换后的html]
     */
    private function replace_include($html){
        // 找到被引入的文件名
        $preg = '/<include\s{1}file=(\'|\")(.+?)(\'|\")\s?\/>/';
        preg_match_all($preg, $html, $matches);
        $count = count($matches[0]);
        // 统计匹配到的次数并循环单次替换，防止第一个匹配的值把后面的覆盖了
        while ($count) {
            preg_match($preg,$html, $match);
            $include = empty($match[2]) ? '' : $match[2];
            if(!empty($include)){
                $suffix = strrchr($include, ".");
                // 默认.html
                $include = empty($suffix) ? $include . ".html" : $include;
                $include = APP_VIEW_PATH . '/' . $include;
                $include_file = file_get_contents($include);
                // 标签替换
                $html = preg_replace($preg, $include_file, $html, 1);
            }
            $count--;
        }
        return $html;
    }
}
