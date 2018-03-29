<?php
/**
 * 日志记录
 * @author michael
 *
 */

class Logger {
    /**
     * @desc 系统日志-按级别输出
     * @access public
     * @param Int $level 日志级别
     * @param String $msg 日志内容
     * @return void
     */
    public static function w($level, $msg, $fileFormat = '', $msgFormat = '') {
        static $logLevelMsg = array (LL_EVENT => "Event", LL_ERROR => "Error", LL_WARN => "Warn", LL_INFO => "Info", LL_DEBUG => "Debug" );
        static $logFilePrefix = array (LL_EVENT => "event", LL_ERROR => "error", LL_WARN => "error", LL_INFO => "debug", LL_DEBUG => "debug" );
        if ($level == LL_EVENT || ($level <= LOG_LEVEL && $level >= 0)) {
            if ($level == LL_ERROR)
                self::logMail ( 'error', $msg, LOG_ERROR_MAILTO );
            if (substr ( $msg, - 1 ) != "\n")
                $msg = $msg . "\n";
            $logMsg = sprintf ( "%s #%s: %s", date ( "Y-m-d H:i:s" ), $logLevelMsg [$level], $msg );
            if (! $msgFormat) {
                $logMsg = sprintf ( "%s #%s: %s", date ( "Y-m-d H:i:s" ), $logLevelMsg [$level], $msg );
            } else {
                #自定义日志格式
                $logMsg = sprintf ( "%s", $msg );
            }
            if (! $fileFormat) {
                $logFileName = sprintf ( "%s/%s_%s.log", LOG_PATH, $logFilePrefix [$level], date ( "Ymd" ) );
            } else {
                #自定义日志文件格式
                $logFileName = sprintf ( "%s/%s.%s", LOG_PATH, $fileFormat, date ( "Ymd" ) );
            }
            $f = fopen ( $logFileName, 'a+' );
            if ($f) {
                fwrite ( $f, $logMsg );
                fclose ( $f );
            }
        }
    }
    //统计日志,$h 为true时,按小时输出
    public static function d($name, $msg, $h = false) {
        if ($name == 'sql') {
            $fName = LOG_PATH . "{$name}_";
        } else {
            $fName = LOG_PATH . "{$name}_";
        }
        if ($h)
            $fName .= date ( 'YmdH' );
        else
            $fName .= date ( 'Ymd' );
        if (substr ( $msg, - 1 ) != "\n")
            $msg = $msg . "\n";
        file_put_contents ( $fName, $msg, FILE_APPEND );
    }
    //邮件通知，一般异常时使用
    public static function logMail($title, $msg, $to = '') {
        if (trim ( $to ) == '')
            return;
        @mail ( $to, $title, $msg );
    }
    public static function HTML($content){
        file_put_contents ( LOG_PATH . "content.html", $content);
    }
}

/**
 * 日志相关常量定义
 */
define ( "LL_EVENT", 0 );
define ( "LL_ERROR", 1 );
define ( "LL_WARN", 2 );
define ( "LL_INFO", 3 );
define ( "LL_DEBUG", 4 );
define ( "LL_PROFILING", 5 );
defined ( 'LOG_LEVEL' ) || define ( 'LOG_LEVEL', 4 );
//要求在项目总配置中定义,如果没有定义,此处设定默认值
if (! defined ( 'LOG_PATH' ))
    throw new Exception ( '"LOG_PATH" not defined!' );