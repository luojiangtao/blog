<?php
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