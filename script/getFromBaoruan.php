<?php
include("../configs/common.inc.php");
$db = Mysql::getInstance();
define("URL","http://wap.baoruan.com");
define("TABLE","ip_in");
define("SLEEP_TIME",5);
define("CHANGE_IP",false);
define("IP_BLACK_SIGN","You've reached the maximum amount");
include "../libs/Http.class.php";

$content = Http::file_get_contents(URL);
#echo $content;
#Logger::HTML($content);
$arrData = getData($content);

exit;


$snoopy = new Snoopy ();

while(1){
    $res = $db->getAll(TABLE,'*','country IS NULL','RAND()','0,10');
    if(empty($res))break;
    foreach($res as $val){
		sleep(SLEEP_TIME);
        $ip1 = $val['ip1'];
		$ips = $val['ips'];
		$wgetContent = getBySnoopy($ip1);
		if(strpos($wgetContent,IP_BLACK_SIGN) !== false){
			echo "restartTPLINK....\n";
			restartTPLINK();
			$rest++;
			$wgetContent = getBySnoopy($ip1);
			if(strpos($wgetContent,IP_BLACK_SIGN) !== false){
				echo "restartTPLINK2...";
				$rest2++;
				restartTPLINK(10);
				$wgetContent = getBySnoopy($ip1);
				if(strpos($wgetContent,IP_BLACK_SIGN) !== false){
					echo "sorry! restartTPLINK fail\n";exit;
				}
			}
		}
        $arrData = getData($wgetContent);
        echo $ip1."\n";
		var_dump($arrData);
		if(!$arrData['country']){
			$j++;
			Logger::w ( LL_EVENT, "error:" . $ip1 . " " . @implode("|",$arrData) ,"getdataerror");
		}else{
			$rs = updateData($arrData,$ips);
			if($rs){
        		$i++;
			}else{
            	Logger::w ( LL_EVENT, "fail:" .$ip1. " " . @implode("|",$arrData) ,"insertdata");
        	}
		}
		echo "success:".$i." jump:".$j." rest:{$rest} rest2:{$rest2}}\n";		
    }
	if(!$arrData['country']){
		restartTPLINK();
	}
	if($lastI == $i){
		$warnS++;
	}else{
		$warnS = 0;
	}
	if($warnS >=5){
		break;
	}
	$lastI = $i;
}
echo "success:".$i." jump:".$j." rest:{$rest} rest2:{$rest2}\n";

function restartTPLINK($sleepTime=0){
	if(CHANGE_IP == false)return;
	Funcs::restartTPLINK($sleepTime);
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
	}
    return '';
}

//分析html里的内容
function getData($content) {
    if (! $content)
        return '';
	$arrA = HTML::getHTML('#tlink1','<a style=\"color:blue\" href=\"http:\/\/a.baoruan.com\">安卓<\/a><br \/>',$content);
	$arrA = HTML::getA($arrA[0]);
	$arrA[1] = HTML::expandlinks($arrA[1],URL);
	
	
	var_dump($arrA);exit;
	
    return $arrData;
}
//分析html里的内容
function updateData($content) {
    global $db;
    $arrData = array();
	$arrData['country'] = addslashes($country);
    $arrData['state'] = addslashes($state);
    $arrData['city'] = addslashes($city);
    $arrData['map1'] = addslashes($map1);
	$arrData['map2'] = addslashes($map2);
    $arrData['asn'] = addslashes($asn);
	$arrData['owner'] = addslashes($owner);
    $arrData['md_str'] = $md_str;
    $arrData['isp'] = addslashes($isp);
	$rs = $db->update(TABLE,$arrData,"ips={$ips}");
    if ($rs){
		return true;
	}else{
		return false;
	}
}


?>
