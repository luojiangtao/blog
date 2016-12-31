<?php
return array(
    //配置项 => 配置值
    
    // 默认加载模块
    'DEFAULT_MODUEL'         => 'Index',
    "AUTO_LOAD_USER_FILE" => array("function.php"),

    // 数据库配置
    "DB_CHARSET"          => "utf8",
    // "DB_HOST"             => "localhost",
    "DB_HOST"             => "localhost",
    "DB_PORT"             => 3306,
    "DB_USER"             => "root",
    "DB_PASSWORD"         => "",
    "DB_DATABASE"         => "blog",
    "DB_PREFIX"           => "",


    "UPLOAD_PATH"           => "Upload",
    "ROUTER"           => array(
        'aa'=>'Index/Test/test',
        // 文章详情，以数字为结尾 http://localhost/blog/index.php/17.html
        '/^(\d+?)$/' =>'Index/Article/article_detail/article_id/$1',
        // 分类列表，
        '/^list\/(\d+?)$/' =>'Index/Article/article_list/category_id/$1',
        ),
);
