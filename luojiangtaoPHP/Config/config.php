<?php
return array(
    // 默认加载模块
    'DEFAULT_MODUEL'         => 'Index',
    // 验证码位数
    'CODE_LENGTH'         => 4,
    // 默认时区，中国
    'DEFAULT_TIME_ZONE'   => 'PRC',
    // 默认开启session
    'SESSION_AUTO_START'  => true,
    // 控制器名称
    'VAR_CONTROLLER'      => 'c',
    // 方法名称
    'VAR_ACTION'          => 'a',
    // 是否开启记录日志
    'SAVE_LOG'            => true,
    // 是否开启缓存
    'IS_CACHE'            => true,
    // 默认加载用户Common/Lib的文件
    'AUTO_LOAD_USER_FILE' => array('function.php'),

    // 数据库配置
    'DB_CHARSET'          => 'utf8',
    // 数据库地址
    'DB_HOST'             => 'localhost',
    // 数据库端口
    'DB_PORT'             => 3306,
    // 数据库用户名
    'DB_USER'             => 'root',
    // 数据库密码
    'DB_PASSWORD'         => '',
    // 数据库库名
    'DB_DATABASE'         => '',
    // 表前缀
    'DB_PREFIX'           => '',

    // 默认文件上传路径
    'UPLOAD_PATH'           => 'Upload',
    // 默认文件上传路径
    'ROUTER'           => array(),

);
