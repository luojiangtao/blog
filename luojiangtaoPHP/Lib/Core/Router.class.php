<?php
/**
 * 路由类
 * @Author   罗江涛
 * @DateTime 2016-08-16T14:26:53+0800
 */
class Router
{
    /**
     * 入口执行文件
     * @罗江涛      <1368761119@qq.com>
     * @DateTime 2016-12-17T13:35:54+0800
     * @return   [type]                   [description]
     */
    public static function run()
    {
        // 读取用户路由配置，并处理后返回结果

        // $path_info   = str_replace('/index.php/', '', $_SERVER['REQUEST_URI']);
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $path_info = substr($path_info, 1);
        if(!$path_info){
            $path_info = isset($_GET['r']) ? $_GET['r'] : '';
        }

        // 读取用户路由配置，并处理后返回用户自定义路由
        $path_info = self::read_conf_router($path_info);
        // 定义分组名，控制器名，方法名，给其他地方用
        self::define_url_param_by_path_info($path_info);

    }

    /**
     * 处理伪静态 .php .html .htm
     * @罗江涛      <1368761119@qq.com>
     * @DateTime 2016-12-17T13:35:54+0800
     * @return   [type]                   [description]
     */
    public static function delete_suffix($path_info)
    {
        if (strstr($path_info, '.')) {
            $array     = explode('.', $path_info);
            $path_info = $array[0];
        }
        return $path_info;
    }

    /**
     * 读取用户路由配置，并找到对应的地址
     * @罗江涛      <1368761119@qq.com>
     * @DateTime 2016-12-17T13:35:54+0800
     * @return   [type]                   [description]
     */
    public static function read_conf_router($path_info)
    {
        if (!$path_info) {
            return $path_info;
        }
        // 处理伪静态 .php .html .htm
        $path_info       = self::delete_suffix($path_info);
        $path_info_array = explode('/', $path_info);

        // 用户路由配置
        $router = C('ROUTER');
        if (!$router) {
            return $path_info;
        }

        if (!is_array($router)) {
            return $path_info;
        }

        foreach ($router as $key => $value) {
            // 查看配置项里面的key存在于$_SERVER['PATH_INFO']中
            if (!strstr($key, '/')) {
                // 自定义路由如果不包含 /
                if ($key == $path_info_array[0]) {
                    return $value;
                }
            } elseif (strstr($key, '/') && !strstr($key, '/^')) {
                // 自定义路由如果包含 / 但是不包含 /^ 则表示是普通路由 如： 'Index/index'
                $temp_array = explode('/', $key);
                if (count($temp_array) <= count($path_info_array)) {
                    $flag = true;
                    foreach ($temp_array as $key2 => $value2) {
                        // 用户自定义路由按 / 拆封后对比 都能匹配上才返回自定义路由
                        if ($value2 != $path_info_array[$key2]) {
                            $flag = false;
                            break;
                        }
                    }
                    if ($flag) {
                        return $value;
                    }

                }
            } elseif (strstr($key, '/^')) {
                if (preg_match($key, $path_info)) {
                    // 例子 ： '/^(\d+?)$/' =>'Index/Article/article_detail/article_id/$1',
                    // 文章详情，以数字为结尾 http://localhost/blog/index.php/17.html
                    $path_info = preg_replace($key, $value, $path_info);
                }

            }
        }
        return $path_info;
    }

    /**
     * pathinfo的方式解析路由，定义项目组，控制器，方法名称，并赋值给$_GET
     * @罗江涛      <1368761119@qq.com>
     * @DateTime 2016-12-17T13:36:30+0800
     * @param    [type]                   $path_info [解析字符串，如：'Test/index' ]
     * @return   [type]                              [数组，包含控制器和方法的值]
     */
    public static function define_url_param_by_path_info($path_info)
    {
        $path_info = self::delete_suffix($path_info);
        // 分组名称
        $app_name   = 'Index';
        // 控制器名称
        $controller = 'Index';
        // 方法名称
        $action     = 'index';

        $path_info = trim($path_info);
        $first_str = substr($path_info, 0, 1);

        // 如果第一字符是 / 就去掉
        if ($first_str == '/') {
            $path_info = substr($path_info, 1);
        }

        // 拆分
        $params = explode('/', $path_info);

        // 分组名称
        if (isset($params[0]) && $params[0]) {
            // 删除数组第一个值，并赋值给$app_name
            $app_name = array_shift($params);
        }
        if (isset($params[0]) && $params[0]) {
            // 删除数组第一个值，并赋值给$controller
            $controller = array_shift($params);
        }
        if (isset($params[0]) && $params[0]) {
            // 删除数组第一个值，并赋值给$action
            $action = array_shift($params);
        }

        $app_path = APPLICATION_PATH . '/' . $app_name;
        if(!is_dir($app_path)){
            // 默认分组
            $app_name = C('DEFAULT_MODUEL');
        }

        // 分组名称
        defined('APP_NAME') or define('APP_NAME', $app_name);
        // 控制器名称
        defined('CONTROLLER_NAME') or define('CONTROLLER_NAME', $controller);
        // 方法名称
        defined('ACTION_NAME') or define('ACTION_NAME', $action);
        // 分组路径
        defined('APP_PATH') or define('APP_PATH', APPLICATION_PATH . '/' . APP_NAME);
        // 用户项目组，控制器目录
        defined('APP_CONTROLLER_PATH') or define('APP_CONTROLLER_PATH', APP_PATH . '/Controller');
        // 用户项目组配置项目录
        defined('APP_CONFIG_PATH') or define('APP_CONFIG_PATH', APP_PATH . '/Config');
        // 用户视图模版文件目录
        defined('APP_VIEW_PATH') or define('APP_VIEW_PATH', APP_PATH . '/View');
        // 用户公共文件目录，js css images
        defined('APP_PUBLIC_PATH') or define('APP_PUBLIC_PATH', APP_PATH . '/Public');

        // 后面部分赋值给 $_GET
        foreach ($params as $key => $value) {
            if ($key % 2 == 0) {
                $_GET[$value] = isset($params[$key + 1]) ? $params[$key + 1] : '';
            }
        }
    }

}
