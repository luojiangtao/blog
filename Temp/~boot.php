<?php

/**
 * 抛出错误页面
 * @Author   罗江涛
 * @DateTime 2016-08-16T14:31:03+0800
 * @param    [type]                   $error [错误信息]
 * @param    string                   $level [错误类型]
 * @param    integer                  $type  [错误代码]
 * @param    [type]                   $dest  [保存日志的路径]
 */
function halt($error, $level = "ERROR", $type = 3, $dest = null)
{
    if (is_array($error)) {
        Log::write($error['message'], $level, $type, $dest);
    } else {
        Log::write($error, $level, $type, $dest);
    }

    $e = array();
    // 开启DUBUG
    if (DEBUG) {
        if (!is_array($error)) {
            // 运行了哪些文件
            $trace = debug_backtrace();
            $e['message']  = $error;
            $e['file']     = $trace[0]['file'];
            $e['line']     = $trace[0]['line'];
            $e['class']    = isset($trace[0]['class']) ? $trace[0]['class'] : '';
            $e['function'] = isset($trace[0]['function']) ? $trace[0]['function'] : '';
            // 开启缓存区
            ob_start();
            // 把打印的运行的文件信息保存到缓存区
            debug_print_backtrace();
            // 取出缓存区内容后转义
            $e['trace'] = htmlspecialchars(ob_get_clean());
        } else {
            $e = $error;
        }
    } else {
        $e['message'] = "网站出错了，请重试...";
    }

    // 加载并执行错误页面
    include DATA_PATH . "/View/halt.html";
    die;
}

/**
 * 打印函数
 * @Author   罗江涛
 * @DateTime 2016-08-02T10:06:55+0800
 * @param    [type]                   $array [需要打印的数组]
 */
function p($array)
{
    if (is_bool($array)) {
        var_dump($array);
    } else if (is_null($array)) {
        var_dump(null);
    } else {
        echo '<pre style="padding:10px;border-radius:5px;background:#f5f5f5;border;1px solid #ccc;font-size:14px;">';
        print_r($array);
        echo '</pre>';
    }
}

/**
 * 跳转方法
 * @Author   罗江涛
 * @DateTime 2016-08-16T14:33:33+0800
 * @param    [type]                   $url  [跳转的地址]
 * @param    integer                  $time [等待时间]
 * @param    string                   $msg  [提示信息]
 */
function redirect($url, $time = 0, $msg = '')
{
    if (!headers_sent()) {
        // 用header方式跳转
        $time = 0 ? header("location:" . $url) : header("refresh:{$time};url={$url}");
        die($msg);
    } else {
        // 用meta方式跳转
        echo "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if ($time) {
            die($msg);
        }

    }
}

/**
 * 加载配置项
 *
 * 1.加载配置项
 * C($sysConfig) C($userConfig);
 * 2.读取配置项
 * C('CODE_LEN')
 * 3.临时动态改变配置项
 * C('CODE_LEN',20);
 * 读取所有配置项
 * 4.C();
 * @Author   罗江涛
 * @DateTime 2016-08-02T11:20:42+0800
 * @param    [type]                   $array [description]
 */
function C($var = null, $value = null)
{
    // 因为需要多次读写，所以定义为静态
    static $config = array();
    // 如果传入数组，则加载配置项，保证后面加载的优先
    if (is_array($var)) {
        $config = array_merge($config, array_change_key_case($var, CASE_UPPER));
        return;
    }

    // 如果是字符串
    if (is_string($var)) {
        $var = strtoupper($var);
        // 传递两个参数，则临时写入配置项
        if (!is_null($value)) {
            $config[$var] = $value;
            return;
        }
        // 只传入一个，并且是字符串，则返回单项配置信息
        return isset($config[$var]) ? $config[$var] : null;
    }

    // 什么都不传，则返回所有配置项信息
    if (is_null($var) && is_null($value)) {
        return $config;
    }
}

/**
 * 生成url
 * @Author   罗江涛
 * @DateTime 2016-08-15T11:10:35+0800
 * @param    [type]                   $file [方法名]
 * @return   [type]                   $url [生成好的url]
 */
function U($file, $array=array())
{
    $url        = '';
    // 控制器名称
    $controller = '';
    // 方法名称
    $action     = '';
    if (strstr($file, '/')) {
        // 如果包涵字符串 /
        $temp       = explode('/', $file);
        $controller = $temp[0];
        $action     = $temp[1];
    } else {
        // 如果不包涵字符串 /
        $controller = CONTROLLER;
        $action     = $file;
    }

    // 获取当前url
    $php_self = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '/') + 1);
    // 加上控制器名称和方法名称
    $url      = $php_self . '?c=' . $controller . '&a=' . $action;

    // 如果穿有参数则加在后面
    if(is_array($array)){
        foreach ($array as $key => $value) {
            $url .= '&'.$key.'='.$value;
        }
    }
    return $url;
}

/**
 * 打印框架定义的常量
 * @Author   罗江涛
 * @DateTime 2016-08-16T14:42:56+0800
 */
function print_const()
{
    $const = get_defined_constants(true);
    p($const['user']);
}

/**
 * Model 的快捷方式
 * @Author   罗江涛
 * @DateTime 2016-08-16T14:43:22+0800
 * @param    [type]                   $table [数据库类]
 */
function M($table = null)
{
    $modle = new Model($table);
    return $modle;
}

/**
 * 实例化模型的快捷方式
 * @Author   罗江涛
 * @DateTime 2016-08-16T14:43:42+0800
 * @param    [type]                   $modle [模型类]
 */
function K($modle)
{
    $modle = $modle . "Model";
    return new $modle;
}

/**
 * 接收$_POST 和 $_GET 传过来的参数
 * @Author   罗江涛
 * @DateTime 2016-08-19T15:57:39+0800
 * @param    [type]                   $name    [接收的名称]
 * @param    [type]                   $default    [没接收到值时的默认值]
 * @return    string                   $default [接收到的值]
 */
function I($name, $default='')
{
    $value = isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
    return htmlspecialchars($value);
}
	/**
	* 
	*/
	final class Application
	{
		/**
		 * 入口方法，被加载后就执行
		 * @Author   罗江涛
		 * @DateTime 2016-08-03T09:07:34+0800
		 */
		public static function run(){
			// 加载框架配置项，开启session，设置时区
			self::_init();
			// 获取警告性错误
			set_error_handler(array(__CLASS__, "error"));
			// 获取致命性错误
			register_shutdown_function(array(__CLASS__, "fatal_error"));
			// 设置外部路径，程序员使用这些常量找到相应的路径
			self::_set_url();
			// 载入用户Common/Lib/下面的文件
			self::_import_user_file();
			// 自动载入用户控制器类
			spl_autoload_register(array(__CLASS__, "_autoload"));
			// 检查默认控制器是否存在，不存在则创建默认控制器
			self::_create_demo();
			// 实例化类，并且执行类里面的方法
			self::_app_run();
		}

		/**
		 * 致命性错误处理
		 * @Author   罗江涛
		 * @DateTime 2016-08-08T15:15:02+0800
		 */
		public static function fatal_error(){
			$e = error_get_last();
			if($e){
				self::error($e['type'], $e['message'], $e['file'], $e['line']);
			}
		}

		/**
		 * 错误处理方法，包括警告性错误和致命性错误
		 * @Author   罗江涛
		 * @DateTime 2016-08-08T14:36:43+0800
		 * @param    [type]                   $errno [错误级别]
		 * @param    [type]                   $error [错误信息]
		 * @param    [type]                   $file  [错误文件]
		 * @param    [type]                   $line  [第几行]
		 */
		public static function error($errno, $error, $file, $line){
			switch ($errno) {
				// 致命性错误统一处理
				case E_ERROR:
				case E_PARSE:
				case E_CORE_ERROR:
				case E_COMPILE_ERROR:
				case E_USER_ERROR:
					$message = $error . $file . " 第{$line}行";
					halt($message);
					break;
				// 警告性错误统一处理
				case E_STRICT:
				case E_USER_WARNING:
				case E_USER_NOTICE:
				
				default:
					if(DEBUG){
						// 载入错误模版，显示错误，注释掉，不显示警告信息
						include DATA_PATH . "/View/notice.html";
					}
					break;
			}
		}

		/**
		 * 实例化类，并且执行类里面的方法
		 * @Author   罗江涛
		 * @DateTime 2016-08-02T17:08:25+0800
		 */
		private static function _app_run(){
			// 接收控制器名称，默认Index
			$c = isset($_GET[C("VAR_CONTROLLER")]) ? $_GET['c'] : "Index";
			// 接收方法名称，默认index
			$a = isset($_GET[C("VAR_ACTION")]) ? $_GET['a'] : "index";
			define("CONTROLLER", $c);
			define("ACTION", $a);
			// 补全控制器名称，并且用来区分是控制器，模型
			$c .= "Controller";
			if(class_exists($c)){
				// 实例化类
				$obj = new $c();
				if(!method_exists($obj, $a)){
					// 不存在则尝试执行空方法
					if(method_exists($obj, "__empty")){
						$obj->__empty();
					}else{
						halt("方法：" . $a . "不存在");
					}
				}else{
					// 执行类里面的方法
					$obj->$a();
				} 
			}else{
				$obj = new EmptyController();
				// 执行类里面的方法
				$obj->index();
			}
			
		}

		/**
		 * 检查默认控制器是否存在，不存在则创建默认控制器
		 * @Author   罗江涛
		 * @DateTime 2016-08-02T14:37:16+0800
		 */
		private static function _create_demo(){
			$path = APP_CONTROLLER_PATH . "/IndexController.class.php";
			$str = 
"<?php
class IndexController extends Controller{
	public function index(){
		header('Content-type:text/html;charset=utf-8');
		echo '<h1>欢迎使用luojiangtaoPHP框架 :) </h1>';
	}
}
?>
";
			// 如果不存在则创建控制器
			is_file($path) || file_put_contents($path, $str);
		}

		/**
		 * 自动载入功能
		 * @Author   罗江涛
		 * @DateTime 2016-08-02T16:54:23+0800
		 * @param    [type]                   $clasName [description]
		 */
		private static function _autoload($clasName){
			// 方法会自动传入类的名称，用于自动载入
			switch (true) {
				// 判断是否是控制器
				case strstr($clasName, "Controller") && strlen($clasName)>10:
					$path = APP_CONTROLLER_PATH . "/" . $clasName . ".class.php";
					if(!is_file($path)){
						$path = APP_CONTROLLER_PATH . "/EmptyController.class.php";
					};
					if(!is_file($path)) halt($path . " 控制器未找到");
					break;
				// 判断是否是模型
				case strstr($clasName, "Model") && strlen($clasName)>5:
					$path = COMMON_MODEL_PATH . "/" . $clasName . ".class.php";
					if(!is_file($path)) halt($path . " 模型类未找到");
					break;
				
				default:
					$path = TOOL_PATH . "/" . $clasName . ".class.php";
					if(!is_file($path)) halt($path . " 类未找到");
					break;
			}
			include $path;
		}

		/**
		 * 设置外部路径，程序员使用这些常量找到相应的路径
		 * @Author   罗江涛
		 * @DateTime 2016-08-02T14:19:01+0800
		 */
		private static function _set_url(){
			// 获取网络路径
			$path = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
			// 为了保证linux和windows都兼容，这里进行替换
			$path = str_replace("\\", "/", $path);
			// 网站入口 http://localhost/frame/index.php
			define("__APP__", $path);
			// 网站根目录
			define("__ROOT__", dirname(__APP__));
			// 模版文件目录
			define("__VIEW__", __ROOT__ . "/" . APP_NAME . "/View");
			// 公共文件目录，如js css images
			define("__PUBLIC__", __ROOT__ . "/Public");
		}
		
		/**
		 * 加载框架配置项，开启session，设置时区
		 * 配置项优先级 用户配置项>公共配置项>框架配置项
		 * @Author   罗江涛
		 * @DateTime 2016-08-02T11:17:22+0800
		 */
		private static function _init(){
			// 先加载系统配置项
			C(include(CONFIG_PATH . "/config.php"));

			// 公共配置项目录
			$commonConfigPath = COMMON_CONFIG_PATH . "/config.php";
			// 公共项基本格式
			$commonConfig = 
"<?php
	return array(
	//配置项 => 配置值
	);
?>
";
			// 用户配置项目录
			$userConfigPath = APP_CONFIG_PATH . "/config.php";
			// 配置项基本格式
			$userConfig = 
"<?php
	return array(
	//配置项 => 配置值
	);
?>
";
			// 如果没有公共配置项，则创建公共配置项
			is_file($commonConfigPath) || file_put_contents($commonConfigPath, $commonConfig);
			// 如果没有用户配置项，则创建用户配置项
			is_file($userConfigPath) || file_put_contents($userConfigPath, $userConfig);
			// 加载公共配置项，保证用户的配置项优先
			C(include $commonConfigPath);
			// 加载用户配置项，最后加载，保证用户的配置项优先
			C(include $userConfigPath);
			// 设置时区
			date_default_timezone_get("Asia/Shanghai");
			// 是否开启session
			C("SESSION_AUTO_START") && session_start();
		}

		/**
		 * 导入用户自定义文件 在Common/Lib下
		 * @Author   罗江涛
		 * @DateTime 2016-08-16T14:25:25+0800
		 */
		private static function _import_user_file(){
			$fileArray = C("AUTO_LOAD_USER_FILE");
			if(is_array($fileArray) && !empty($fileArray)){
				foreach ($fileArray as $key => $value) {
					require_once COMMON_LIB_PATH . "/" . $value;
				}
			}
		}
	}


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
/**
 * 父类，用户的控制器都要继承
 * @Author   罗江涛
 * @DateTime 2016-08-16T14:26:53+0800
 */
class Controller extends Smarty{

	/**
	 * 构造方法
	 * @Author   罗江涛
	 * @DateTime 2016-08-16T14:26:53+0800
	 */
	public function __construct(){
		parent::__construct();
		// echo ("父类构造方法123");
		// 框架初始化构造方法
		if (method_exists($this, "__init")) {
			$this->__init();
		}
		// 子类的子类的初始化构造方法
		if (method_exists($this, "__auto")) {
			$this->__auto();
		}
	}

	/**
	 * 成功方法，跳转到成功页面
	 * @Author   罗江涛
	 * @DateTime 2016-08-16T14:28:04+0800
	 */
	protected function success($message, $time=3){
		header("Content-type:text/html;charset=utf-8");
		include(DATA_PATH . "/View/success.html");
		die;
	}

	/**
	 * 错误方法，跳转到错误页面
	 * @Author   罗江涛
	 * @DateTime 2016-08-16T14:28:04+0800
	 */
	protected function error($message = "错误", $time=3){
		header("Content-type:text/html;charset=utf-8");
		include(DATA_PATH . "/View/error.html");
		die;
	}
}

/**
 * 记录日志类
 * @Author   罗江涛
 * @DateTime 2016-08-16T14:26:53+0800
 */
class Log{
	/**
	 * 写日志
	 * @Author   罗江涛
	 * @DateTime 2016-08-16T14:29:11+0800
	 * @param    [type]                   $message [日志信息]
	 * @param    string                   $level   [错误级别]
	 * @param    integer                  $type    [错误类型]
	 * @param    [type]                   $dest    [日志路径]
	 */
	static public function write($message, $level="ERROR", $type=3, $dest=NULL){
		// 查看是否需要写日志
		if(!C("SAVE_LOG")) return;

		// 如果路径为空则只用系统默认
		if(is_null($dest)){
			$dest = LOG_PATH . "/" . date("Y_m_d") . ".log";
		}

		$message = "[TIME] : ". date("Y_m_d H:i:s") . " " .  $level. " : " . $message . "\r\n";
		// 写日志
		if(is_dir(LOG_PATH)) error_log($message, $type, $dest);
	}
}

?>