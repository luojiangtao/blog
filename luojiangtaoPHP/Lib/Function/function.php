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
