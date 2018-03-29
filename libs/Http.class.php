<?php
/**
 * 外部链接访问
 * @author michael
 *
 */
class Http
{
    public static $connectTimeOut = 10;//单位秒
    public static $streamTimeOut = 20000;//单位毫秒
    //单实例
    private static $_obj        = null;
	/**
	 * @desc 获取单实例对象
	 */
	public static function getInstance()
	{
		if(self::$_obj == null)
		{
			self::$_obj = new self();
		}
		return self::$_obj;
	}

    public function __construct()
    {
    	$this->snoopy = new Snoopy ();
    }

	/*
	 * @desc file_get_contents方式发送请求，支持get,post方式,支持超时断开功能
	 * @param $url 请求地址及GET参数信息
	 * @param $paramStr POST参数信息
	 * @return content
	 */	
	public function file_get_contents($url, $paramStr = '') {
		if($paramStr)$method = 'POST';
		else $method = 'GET';
		$context ['http'] = array ('method' => $method, 'header' => 'Content-type: application/x-www-form-urlencoded', 'timeout' => self::$connectTimeOut, 'content' => "" );
		if ($paramStr)$context ['http'] ['content'] = http_build_query ( $paramStr, '', '&' );
		$results = file_get_contents ( ($url), true, stream_context_create ( $context ) );
		return $results;
	}
	/*
	 * @desc curl方式发送请求，支持get,post方式,支持超时断开功能
	 * @param $url 请求地址及GET参数信息
	 * @param $paramStr POST参数信息
	 * @return content
	 */
	public function curl_get_contents($url, $paramStr = ''){
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, self::$connectTimeOut );
		curl_setopt ( $ch, CURLOPT_USERAGENT, _USERAGENT_ );
		curl_setopt ( $ch, CURLOPT_REFERER, _REFERER_ );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		if($paramStr){
			$encoded = http_build_query ( $paramStr, '', '&' );
			curl_setopt($ch, CURLOPT_POST,count($paramStr)) ;
			curl_setopt($ch, CURLOPT_POSTFIELDS,$encoded) ;
		}
		$results = curl_exec ( $ch );
		curl_close ( $ch );
		return $results;
	 }
	/*
	 * @desc sock方式发送请求，支持get,post方式，支持超时断开功能
	 * @param $url 请求地址及GET参数信息
	 * @param $paramStr POST参数信息
	 * @return content
	 */
	public function sock_get_contents($url, $paramStr = ''){
		$url = parse_url($url);  
	    if (!$url){
	    	return "count not parse url";
	    } 
	    if (!$url['port']) $url['port'] = 80;  
	    if($paramStr){
	    	$method = 'POST';
	    	$encoded = http_build_query ( $paramStr, '', '&' );
	    }else{
	    	$method = 'GET';
	    }
	    $fp = fsockopen($url['host'], $url['port'], $errno, $errstr,self::$connectTimeOut);  
	    if (!$fp){  
	        return "Failed to open socket ERROR: $errno - $errstr";
	    }

	    fputs($fp, sprintf("{$method} %s?%s HTTP/1.0\n", $url['path'],  $url['query']) );  
	    fputs($fp, "Host: {$url['host']}\n");
	    
	    if($paramStr){
		    fputs($fp, "Content-type: application/x-www-form-urlencoded\n");  
		    fputs($fp, "Content-length: " . strlen($encoded) . "\n");
		    fputs($fp, "Connection: close\n\n");
		    fputs($fp, "$encoded\n");
	    }else{
	    	fputs($fp, "Connection: close\n\n");
	    }
	    
		stream_set_blocking($fp, True);
		stream_set_timeout($fp, 0, (self::$streamTimeOut)*1000);//获取流媒体超时设置
		
	    $results = "";  
	    $inheader = 1;  
	    while(!feof($fp))   
	    {  
	        $line = fgets($fp,1024);  
	        if ($inheader && ($line == "\n" || $line == "\r\n"))   
	        {  
	            $inheader = 0;  
	        }  
	        elseif (!$inheader)   
	        {  
	            $results .= $line;  
	        }
	    	$status = stream_get_meta_data($fp);
			if ($status['timed_out']) {
				$results = "stream_timed_out ERROR: ".self::$streamTimeOut." ms";
				break;
			}
	    }  
	    fclose($fp);  
		return $results;
	 }
	public function snoopy_get_contents($url){
	    // set browser and referer:
	    $this->snoopy->agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)";
	    $this->snoopy->referer = "http://www.youban.com";
	    // set some cookies:
	    
	    // fetch the text of the website www.google.com:
	    if ($this->snoopy->fetch ( $url )) 
	    {
	        //file_put_contents ( "./txt2/".$ip, $snoopy->results);
	        return $this->snoopy->results; 
		}
	    return '';
	}
}