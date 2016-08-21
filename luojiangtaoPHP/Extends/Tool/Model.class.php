<?php
/**
 *
 */
class Model
{
	// 保存连接信息
    public static $link = null;
    // 需要操作的数据库表名
    protected $table = NULL;
    // 初始化查询数据库表信息
    private $opt;

    // 记录发送的sql
    public static $sqls = array();

    /**
     * 构造方法
     * @Author   罗江涛
     * @DateTime 2016-08-16T09:04:45+0800
     * @param    [type]                   $table [需要操作的表名]
     */
    public function __construct($table = NULL){
    	$this->table = is_null($table) ? C('DB_PREFIX') . $this->table : C("DB_PREFIX") . $table;
        // 链接数据库
    	$this->_connect();
    	// 初始化sql信息
    	$this->_opt();
    }

    /**
     * 最底层的数据库查询方法
     * @Author   罗江涛
     * @DateTime 2016-08-09T09:26:00+0800
     * @param    [type]                   $sql [sql语句]
     * @return   [type]                        [查询的结果]
     */
    public function query($sql){
        // 保存sql语句，方便调试
    	self::$sqls[] = $sql;
        // 获取数据库链接
    	$link = self::$link;
        // 执行查询sql
    	$result = $link->query($sql);
    	if($link->errno){
            // 数据库链接出错
    		halt('mysql错误' . $link->error . '<br/>SQL: ' . $sql);
    	}

    	$rows = array();
    	while ($row = $result->fetch_assoc()) {
            // 循环获取查询的值
    		$rows[] = $row;
    	}
        // 释放数据库链接
    	$result->free();
        // 初始化组装sql的基本信息
    	$this->_opt();
        // 返回查询的结果
    	return $rows;
    }

    /**
     * 获取所有的数据
     * @Author   罗江涛
     * @DateTime 2016-08-16T09:09:34+0800
     * @return   [type]                   [查询结果]
     */
    public function select(){
    	$sql = "SELECT " . $this->opt['field'] . " FROM " . $this->table . $this->opt['where'] . $this->opt['group'] . $this->opt['having'] . $this->opt['order'] . $this->opt['limit'];
    	return $this->query($sql);
    }

    /**
     * 查询单条数据
     * @Author   罗江涛
     * @DateTime 2016-08-16T09:09:55+0800
     * @return   [type]                   [查询结果]
     */
    public function find(){
    	$data = $this->limit(1)->select();
    	$data = current($data);
    	return $data;
    }

    /**
     * 获取一条数据的指定字段
     * @Author   罗江涛
     * @DateTime 2016-08-16T09:10:55+0800
     * @param    [type]                   $value [需要获取的字段名]
     * @return   [type]                          [查询结果]
     */
    public function get_field($value){
        $data = $this->find();
        return $data[$value];
    }

    /**
     * 设置需要获取的字段
     * @Author   罗江涛
     * @DateTime 2016-08-16T09:10:55+0800
     * @param    [type]                   $field [需要获取的字段名，多个用,分割]
     * @return   [type]                          [当前对象]
     */
    public function field($field){
    	$this->opt['field'] = $field;
    	return $this;
    }

    /**
     * 设置sql需要获取条件
     * @Author   罗江涛
     * @DateTime 2016-08-16T09:10:55+0800
     * @param    [type]                   $where [需要查询的条件]
     * @return   [type]                          [当前对象]
     */
    public function where($where){
    	$this->opt['where'] = " WHERE " . $where;
    	return $this;
    }

    /**
     * 设置sql排序规则
     * @Author   罗江涛
     * @DateTime 2016-08-16T09:10:55+0800
     * @param    [type]                   $order [排序规则]
     * @return   [type]                          [当前对象]
     */
    public function order($order){
    	$this->opt['order'] = " ORDER BY " . $order;
    	return $this;
    }

    /**
     * 设置sql从第几行，获取多少条
     * @Author   罗江涛
     * @DateTime 2016-08-16T09:10:55+0800
     * @param    [type]                   $limit [从第几行，获取多少条]
     * @return   [type]                          [当前对象]
     */
    public function limit($limit){
    	$this->opt['limit'] = " LIMIT " . $limit;
    	return $this;
    }

    /**
     * 设置sql,搜索结果按照哪个字段分组
     * @Author   罗江涛
     * @DateTime 2016-08-16T09:10:55+0800
     * @param    [type]                   $limit [搜索结果按照哪个字段分组]
     * @return   [type]                          [当前对象]
     */
    public function group($group){
    	$this->opt['group'] = " GROUP BY " . $group;
    	return $this;
    }

    /**
     * 设置sql需要获取条件
     * @Author   罗江涛
     * @DateTime 2016-08-16T09:10:55+0800
     * @param    [type]                   $having [需要查询的条件]
     * @return   [type]                          [当前对象]
     */
    public function having($having){
    	$this->opt['having'] = " HAVING " . $having;
    	return $this;
    }

    /**
     * sql语句初始化信息，用来组装sql语句
     * @Author   罗江涛
     * @DateTime 2016-08-16T09:15:23+0800
     */
    private function _opt(){
    	$this->opt = array(
    		'field'=>'*',
    		'where'=>'',
    		'group'=>'',
    		'having'=>'',
    		'order'=>'',
    		'limit'=>'',
    		);
    }

    /**
     * 链接数据库，单例模式
     * @Author   罗江涛
     * @DateTime 2016-08-16T09:16:03+0800
     */
    private function _connect(){
        // 如果连接过，就不链接了。使用之前的链接，节约资源，提高效率
    	if(is_null(self::$link)){
            // 获取数据库名称
    		$db = C("DB_DATABASE");
    		if(empty($db)){
    			halt("请先配置数据库");
    		}
            // 连接数据库
    		$link = new Mysqli(C("DB_HOST"), C("DB_USER"), C("DB_PASSWORD"), $db, c("DB_PORT"));
    		if($link->connect_error){
    			halt("数据库连接错误，请检查配置项");
    		}
            // 设置数据库支付编码
    		$link->set_charset(C("DB_CHARSET"));
            // 保存数据库链接对象
    		self::$link = $link;
    	}
    }

    /**
     * 最底层的增加，修改，删除方法
     * @Author   罗江涛
     * @DateTime 2016-08-16T09:19:32+0800
     * @param    [type]                   $sql [执行的sql语句]
     * @return   [type]                        [增加则返回自增ID， 删除修改则返回影响行数]
     */
    public function execute($sql){
        // 保存sql语句，方便调试
    	self::$sqls[] = $sql;
        // 获取数据库链接对象
    	$link = self::$link;
        // 执行的sql语句
    	$result = $link->query($sql);
        // 初始化sql信息
    	$this->_opt();
    	// 不允许用select查询
    	if(is_object($result)){
    		halt("请用query方法发送查询sql");
    	}

    	if($result){
    		// 增加则返回自增ID， 删除修改则返回影响行数
    		return $link->insert_id ? $link->insert_id : $link->affected_rows;
    	}else{
    		halt("mysql错误：" . $link->error . "<br/>SQL：" . $sql);
    	}
    }

    /**
     * 更新操作
     * @Author   罗江涛
     * @DateTime 2016-08-16T09:22:14+0800
     * @param    [type]                   $data [需要保存到数据库的数组]
     * @return   [type]                         [影响的行数]
     */
    public function update($data=NULL){
    	if(empty($this->opt['where'])){
    		halt("更新语句必须有where条件");
    	}
        // 没有传值，则从$_POST 中获取
    	if(is_null($data)){
    		$data = $_POST;
    	}

        // 组装sql语句
    	$values = "";
    	foreach ($data as $key => $value) {
    		$values .= "`" . $this->_safe_str($key) . "`=" . "'" . $this->_safe_str($value) . "',";
    	}
        // 去掉最后的,
    	$values = trim($values, ',');
    	$sql = "UPDATE " . $this->table . " SET " . $values . $this->opt['where'];

    	return $this->execute($sql);
    }

    /**
     * 更新操作的别名
     * @Author   罗江涛
     * @DateTime 2016-08-18T10:05:15+0800
     * @param    [type]                   $data [需要保存到数据库的数组]
     * @return   [type]                         [影响的行数]
     */
    public function save($data=NULL){
        return $this->update($data=NULL);
    }

    /**
     * 删除操作
     * @Author   罗江涛
     * @DateTime 2016-08-16T09:25:19+0800
     * @return   [type]                   [影响行数]
     */
    public function delete(){
    	if(empty($this->opt['where'])){
    		halt("删除语句必须有where条件");
    	}
        // 组装sql语句
    	$sql = "DELETE FROM " . $this->table . $this->opt['where'];
    	return $this->execute($sql);
    }

    /**
     * 添加操作
     * @Author   罗江涛
     * @DateTime 2016-08-16T09:26:18+0800
     * @param    [type]                   $data [自增主键ID]
     */
    public function add($data=NULL){
        // 没有传值，则从$_POST 中获取
    	if(is_null($data)){
    		$data = $_POST;
    	}

        // 组装sql语句
    	$fields = "";
    	$values = "";
    	foreach ($data as $key => $value) {
    		$fields .= "`" . $this->_safe_str($key) . "`,";
    		$values .= "'" . $this->_safe_str($value) . "',";
    	}
        // 去掉最后的,
    	$fields = trim($fields, ',');
    	$values = trim($values, ',');

    	$sql = "INSERT INTO " . $this->table . " (" . $fields . ") VALUES (" . $values . ")";
    	return $this->execute($sql);
    }

    /**
     * 统计有多少条数据
     * @Author   罗江涛
     * @DateTime 2016-08-16T09:27:59+0800
     * @return   [type]                   [有多少条数据]
     */
    public function count(){
        $sql = "SELECT COUNT(*) FROM " . $this->table . $this->opt['where'];
        // 执行sql语句
        $count = $this->query($sql);
        $count = $count[0]['COUNT(*)'];
        return $count;
    }

    /**
     * 增加数字
     * @Author   罗江涛
     * @DateTime 2016-08-16T09:27:59+0800
     * @return   [type]                   [有多少条数据]
     */
    public function set_field($field, $value){
        $data[$field] = $value;
        return $this->update($data);
    }

    /**
     * 增加数字
     * @Author   罗江涛
     * @DateTime 2016-08-16T09:27:59+0800
     * @return   [type]                   [有多少条数据]
     */
    public function set_inc($field, $step=1){
        if(empty($this->opt['where'])){
            halt("更新语句必须有where条件");
        }
        $sql = "UPDATE " . $this->table . " SET `" . $field . "`=`" . $field. "`+". $step . $this->opt['where'];
        return $this->execute($sql);
    }

    public function set_dec($field, $step=1){
        if(empty($this->opt['where'])){
            halt("更新语句必须有where条件");
        }
        $sql = "UPDATE " . $this->table . " SET `" . $field . "`=`" . $field. "`-". $step . $this->opt['where'];
        return $this->execute($sql);
    }

    /**
     * 安全检测，把不安全的字符串转化为安全的字符串
     * @Author   罗江涛
     * @DateTime 2016-08-16T09:28:49+0800
     * @param    [type]                   $str [待转化的字符串]
     * @return   [type]                        [转化后的字符串]
     */
    private function _safe_str($str){
        // 检查是否开启自动转义
    	if(get_magic_quotes_gpc()){
            // 如果开启了，就转回来
    		$str = stripslashes($str);
    	}

        // 用自带的方法处理
    	return self::$link->real_escape_string($str);
    }
}
