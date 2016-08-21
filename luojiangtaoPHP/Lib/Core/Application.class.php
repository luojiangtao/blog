<?php
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

?>