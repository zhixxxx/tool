<?php
/**
 * 工具集，与业务无关
 * @author michael
 *
 */
class Utils {

	/**
 	*函数作用：随机字串
 	*函数名称：randString()
 	*返 回 值：$string
 	*作	  者：michael
 	*创建日期：2006-1-3
 	*/
	public static function randString($len, $scope= "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890") {
		srand((double) microtime() * 1000000);
		$str_len= strlen($scope) - 1;
		$string= '';
		for ($i= 0; $i < $len; $i ++) {
			$string .= substr($scope, rand(0, $str_len), 1);
		}
		return $string;
	}
	/**
 	*函数作用：得到客户端IP地址
 	*函数名称：getClientIp()
 	*返 回 值：$ip
 	*作	  者：michael
 	*创建日期：2006-1-3
 	*/
	public static function getClientIp() {
		if (getenv('HTTP_CLIENT_IP')) {
			$ip= getenv('HTTP_CLIENT_IP');
		}
		elseif (getenv('HTTP_X_FORWARDED_FOR')) {
			list ($ip)= explode(',', getenv('HTTP_X_FORWARDED_FOR'));
		}
		elseif (getenv('REMOTE_ADDR')) {
			$ip= getenv('REMOTE_ADDR');
		} else {
			$ip= $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	
    /**
     * 创建目录(可以创建父目录)
     */
    public static function makedir($path,$mode = 0777){
    	$path = dirname($path);
    	$dirstack = explode('/',$path);
    	$path = '';
    	while($newdir = array_shift($dirstack)){
    		$path .= $newdir.'/';
    		$stat = mkdir($path,$mode);
    	}
    	return $stat;
    }
    
    /**
     * 写磁盘文件,会自动创建文件名中带的目录路径
     */
    public static function wfile($file,$text,$mode='w') {
        $oldmask = umask(0);
        $fp = @fopen($file, $mode);
        if (!$fp){
    		if(!self::makedir($file) || !($fp = @fopen($file, $mode)))
    			return false;
    	}
        fwrite($fp,$text);
        fclose($fp);
        umask($oldmask);
        return true;
    }
    /**
     * 用addslashes处理变量,可处理多维数组
     */
    public static function addQuotes($vars) {
    	if (is_array($vars)) {
    		foreach ($vars as $k => $v) {
    			if (is_array($v)) {
    				foreach ($v as $k1 => $v1) {
    					$vars[$k][$k1] = self::addQuotes($v1);
    				}
    			} else {
    				$vars[$k] = addslashes($v);
    			}
    		}
    	} else {
    		$vars = addslashes($vars);
    	}
    	return $vars;
    }
    
    /**
     * 对指定变量进行stripslashes处理,可处理多维数组
     */
    public static function stripQuotes($vars) {
    	if (is_array($vars)) {
    		foreach ($vars as $k => $v) {
    			if (is_array($v)) {
    				foreach ($v as $k1 => $v1) {
    					$vars[$k][$k1] = self::stripQuotes($v1);
    				}
    			} else {
    				$vars[$k] = stripslashes($v);
    			}
    		}
    	} else {
    		$vars = stripslashes($vars);
    	}
    	return $vars;
    }
    
    /**
     * 用trim处理变量,可处理多维数组
     */
    public static function trimArr($vars) {
    	if (!count($vars))
    		return;
    	if (is_array($vars)) {
    		foreach ($vars as $k => $v) {
    			if (is_array($v)) {
    				foreach ($v as $k1 => $v1) {
    					$vars[$k][$k1] = self::trimArr($v1);
    				}
    			} else {
    				$vars[$k] = trim($v);
    			}
    		}
    	} else {
    		$vars = trim($vars);
    	}
    	return $vars;
    }
}
?>