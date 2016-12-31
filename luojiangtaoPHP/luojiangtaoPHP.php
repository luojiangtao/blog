<?php
/**
 *
 */
final class luojiangtaoPHP
{
    /**
     * 入口方法，初始化基本信息
     */
    public static function run()
    {
        // 定义常量，用于创建用户目录和找到响应的类和函数
        self::_set_const();
        // 默认关闭调试模式
        defined('DEBUG') || define('DEBUG', false);
        if (DEBUG) {
            // 检查项目相关目录是否存在，不存在则自动生成
            // 载入框架所需文件，一些基本的类和函数
            self::_import_file();
        } else {
            // 关闭错误
            error_reporting(0);
            // 加载融合为一个的框架所需的文件，速度更快
            require TEMP_PATH . '/~boot.php';
        }

        // 载入Application类后，就执行入口方法，继续设置基本信息
        Application::run();
    }

    /**
     * 定义常量，用于创建用户目录和找到响应的类和函数
     * @Author   罗江涛
     * @DateTime 2016-08-03T09:05:38+0800
     */
    private static function _set_const()
    {
        // 这个类的路径，为了兼容而替换路径表示方式
        $path = str_replace('\\', '/', __FILE__);
        // 框架根目录
        define('LUOJIANGTAOPHP_PATH', dirname($path));
        // 框架配置目录
        define('CONFIG_PATH', LUOJIANGTAOPHP_PATH . '/Config');
        // 框架数据文件目录
        define('DATA_PATH', LUOJIANGTAOPHP_PATH . '/Data');
        // 框架类库目录
        define('LIB_PATH', LUOJIANGTAOPHP_PATH . '/Lib');
        // 框架核心类目录
        define('CORE_PATH', LIB_PATH . '/Core');
        // 框架常用方法目录
        define('FUNCTION_PATH', LIB_PATH . '/Function');

        // 框架扩张文件目录
        define('EXTENDS_PATH', LUOJIANGTAOPHP_PATH . '/Extends');
        // 框架常用方法目录
        define('TOOL_PATH', EXTENDS_PATH . '/Tool');
        // 框架常用方法目录
        define('ORG_PATH', EXTENDS_PATH . '/Org');

        // 网站根目录
        define('ROOT_PATH', dirname(LUOJIANGTAOPHP_PATH));

        // 应用目录
        define('APPLICATION_PATH', ROOT_PATH . '/Application');
        // 临时缓存目录
        define('TEMP_PATH', APPLICATION_PATH . '/Temp');
        // 日志目录
        define('LOG_PATH', TEMP_PATH . '/Log');
        // 编译文件文件缓存目录
        define('COMPILE_PATH', TEMP_PATH . '/Compile');
        // 纯html文件缓存目录
        define('CACHE_PATH', TEMP_PATH . '/Cache');

        //用户公共路径
        define('COMMON_PATH', APPLICATION_PATH . '/Common');
        //用户上传文件路径
        define('UPLOAD_PATH', ROOT_PATH . '/Upload');
        //用户公共路径
        define('COMMON_CONFIG_PATH', COMMON_PATH . '/Config');
        //用户公共路径
        define('COMMON_MODEL_PATH', COMMON_PATH . '/Model');
        //用户公共路径
        define('COMMON_LIB_PATH', COMMON_PATH . '/Lib');

        // 系统变量，是否是POST提交
        define('IS_POST', ($_SERVER['REQUEST_METHOD'] == 'POST' ? true : false));
        // 系统变量，是否是AJAX提交
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            define('IS_AJAX', true);
        } else {
            define('IS_AJAX', false);
        }
    }

    /**
     * 载入框架所需文件，一些基本的类和函数
     * @Author   罗江涛
     * @DateTime 2016-08-02T09:57:58+0800
     */
    private static function _import_file()
    {
        // 需要载入的文件
        $fileArray = array(
            FUNCTION_PATH . '/function.php',
            CORE_PATH . '/Router.class.php',
            CORE_PATH . '/Application.class.php',
            ORG_PATH . '/Smarty.class.php',
            CORE_PATH . '/Controller.class.php',
            CORE_PATH . '/Log.class.php',
        );
        $str = '';
        foreach ($fileArray as $key => $value) {
            // 去掉文件中 <?php  ? > 开头和结尾;
            $str .= substr(file_get_contents($value), 5);
            // 引入框架所需的文件
            require_once $value;
        }

        // 把这些文件融合到一个，关闭DEBUG后，只载入这一个，加快速度
        $str = "<?php\r\n" . $str . "\r\n?>";
        file_put_contents(TEMP_PATH . '/~boot.php', $str) || die('access not allow :' . TEMP_PATH);
    }

}

// 入口，引入该类就执行初始化方法，一层一层的执行
luojiangtaoPHP::run();
