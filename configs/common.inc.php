<?php
/**
 *@Todo		 :  通用配置
 *@Copyright :	#
 *@Author	 :	michael
 */
#生产时,设为0,关闭所有到屏幕的输出
ini_set ( 'display_errors', 1 );
#生产时,注释掉该行,错误报告类别由系统指定
error_reporting ( E_ALL ^E_NOTICE );
header ( 'Content-Type:text/html;Charset=utf-8' );
date_default_timezone_set ( 'Asia/Shanghai' );
define ( 'APP_DIR', dirname ( __FILE__ ) . '/../' ); #程序所在路径  
define ( 'CACHE_DIR', dirname ( __FILE__ ) . '/../../cache/' ); #程序所在路径  
define ( 'LOG_PATH', APP_DIR . 'logs/' ); #日志路径
#日志级别
define ( 'LOG_LEVEL', 2 );
#运行参数调整

#数据库配置
define ( 'DB_HOST', '183.6.116.151' ); #主机地址
define ( 'DB_USER', 'root' ); #用户名
define ( 'DB_PASS', '123456' ); #密码
define ( 'DB_NAME', 'bcb_fanli' );


include (APP_DIR . "libs/Logger.class.php");
include (APP_DIR . "libs/Mysql.class.php");
include (APP_DIR . "libs/Html.class.php");
include (APP_DIR . "libs/Utils.class.php");
?>
