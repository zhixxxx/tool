<?php
include("../configs/common.inc.php");
$db = Mysql::getInstance();
define("URL","http://xml.utrace.de/?query=");
include "../libs/Snoopy.class.php";
$snoopy = new Snoopy ();
while(1){
    $res = $db->getAll('ip_in_big','*','country IS NULL','ips ASC','0,100');
    if(empty($res))break;
	$res[0] = array('ip1'=>'1.22.0.0');
    foreach($res as $val){
		sleep(2);
        $ip1 = $val['ip1'];
		$ips = $val['ips'];
        $arrData = getData(getBySnoopy($ip1));
        var_dump($arrData);
		if(!$arrData['country']){
			$j++;
			Logger::w ( LL_EVENT, "error:" . $ip1 . " " . implode("|",$arrData) ,"getdataerror");
			continue;
		}
		$rs = updateData($arrData,$ips);
		if($rs){
            Logger::w ( LL_EVENT, "ok:" .$ip1 . " " . implode("|",$arrData) ,"insertdata");
        	$i++;
		}else{
            Logger::w ( LL_EVENT, "fail:" . implode("|",$arrData) ,"insertdata");
        }
		echo "success:".$i." jump:".$j."\n";		
    }
	break;
}
echo "success:".$i." jump:".$j."\n";

#获取html内容
function getContent($ip) {
    $ip = trim ( $ip );
    $url = URL . $ip;
    $i = 0;
    $content = '';
    while ( ! $content && $i < 2 ) {
        $content = @file_get_contents ( $url );
        $i ++;
    }
    return $content;
}

function getBySnoopy($ip){
    $ip = trim ( $ip );
    $url = URL . $ip;
    global $snoopy;
    
    // set browser and referer:
    $snoopy->agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)";
    $snoopy->referer = "http://xml.utrace.de";
    // set some cookies:
    
    // fetch the text of the website www.google.com:
    if ($snoopy->fetch ( $url )) 
    {
        file_put_contents ( "./txt2/".$ip, $snoopy->results);
        return $snoopy->results; 
    }else{
		Logger::w ( LL_EVENT, "get content fail:" . $uel);
		exit;
	}
    return '';
}


//分析html里的内容
function getData($content) {
    if (! $content)
        return '';
    //
	$arrA = getHTML('<results>','<\/results>',$content);
	$content_1 = $arrA[1][0];
	if($content_1 == '')return '';
	
	//
	$arrA = getHTML('<countrycode>','<\/countrycode>',$content_1);
    $arrData['country'] = $arrA[1][0];
	$arrA = getHTML('<region>','<\/region>',$content_1);
    $arrData['city'] = $arrA[1][0];
    
    //获取owner
    $arrA = getHTML('<isp>','<\/isp>',$content_1);
	$arrData['isp'] = $arrA[1][0];
	$arrA = getHTML('<org>','<\/org>',$content_1);
	$arrData['owner'] = $arrA[1][0];
    //获取Maps: 
    $arrA = getHTML('<latitude>','<\/latitude>',$content_1);
	$arrData['map'] = $arrA[1][0];
	$arrA = getHTML('<longitude>','<\/longitude>',$content_1);
	$arrData['map'] = $arrData['map'].','.$arrA[1][0];
    Logger::w ( LL_EVENT, "get data:" . implode("|",$arrData) ,"get-myip-ms");
    return $arrData;
}
//分析html里的内容
function updateData($content,$ips) {
    global $db;
    $country = $content ['country'];
	$state = $content ['state'];
    $city = $content ['city'];
    $map = $content ['map'];
    $asn = $content ['asn'];
	$owner = $content ['owner'];
    $md_str = md5($state.'||||'.$city);
    $isp = $content['isp'];
    
    $arrData = array();
	$arrData['country'] = addslashes($country);
    $arrData['state'] = addslashes($state);
    $arrData['city'] = addslashes($city);
    $arrData['map'] = addslashes($map);
    $arrData['asn'] = addslashes($asn);
	$arrData['owner'] = addslashes($owner);
    $arrData['md_str'] = $md_str;
    $arrData['isp'] = addslashes($isp);
	$rs = $db->update('ip_in_big',$arrData,"ips={$ips}");
    if (! $rs){
		Logger::w ( LL_ERROR, "update fail:" . $db->getLastError() );
	}
	return true;
}

function getHTML($s, $e, $str) {
    $arrImgUrl = array ();
    $preg = "/{$s}(.*){$e}/is";
    preg_match_all ( $preg, $str, $arrTemp );
    return $arrTemp;
}
function getA($str) {
    $arrImgUrl = array ();
    $preg = '/<a.*?(?: |\\t|\\r|\\n)?href=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>(.+?)<\/a.*?>/sim';
    preg_match_all ( $preg, $str, $arrTemp );
    return $arrTemp;
}
function getIMG($str) {
    $arrImgUrl = array ();
    $preg = "/<img(.[^<]*)src=\"?'?(.[^<\"']*)\"?'?(.[^<]*)\/?>/is";
    preg_match_all ( $preg, $str, $arrTemp );
    return $arrTemp;
}
?>
