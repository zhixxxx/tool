<?php
include("../configs/common.inc.php");
$db = Mysql::getInstance();
define("URL","http://www.myip.ms/info/whois/");
include "../libs/Snoopy.class.php";
$snoopy = new Snoopy ();
while(1){
    $res = $db->getAll('ip_in_big','*','status=0','ips ASC','0,100');
    if(empty($res))break;
    foreach($res as $val){
		sleep(2);
        $ip1 = $val['ip1'];
		$ips = $val['ips'];
        $arrData = getData(getBySnoopy($ip1));
        var_dump($arrData);
		if(!$arrData['country'] || !$arrData['state']){
			Logger::w ( LL_EVENT, "error:" . implode("|",$arrData) ,"getdataerror");
			continue;
		}
		$rs = updateData($arrData,$ips);
		if($rs){
            Logger::w ( LL_EVENT, "ok:" . implode("|",$arrData) ,"insertdata");
        	$i++;
		}else{
            Logger::w ( LL_EVENT, "fail:" . implode("|",$arrData) ,"insertdata");
        }
    }
	break;
}
echo "success:".$i."\n";

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
#	return file_get_contents ( "./txt/".$ip);
	
    $ip = trim ( $ip );
    $url = URL . $ip;
    global $snoopy;
    
    #$snoopy->proxy_host = "http://myip.ms/info/whois/1.22.0.0";
    #$snoopy->proxy_port = "80";
    // set browser and referer:
    $snoopy->agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)";
    $snoopy->referer = "http://myip.ms/info/whois/1.22.0.0";
    // set some cookies:
    $snoopy->cookies ["PHPSESSID"] = '9g3uq1trv8k5lki8g3agvnhkb2';
    $snoopy->cookies ["s2_uLang"] = "en";
    $snoopy->cookies ["s2_theme_ui2"] = "red";
    $snoopy->cookies ["__utma"] = "126509969.66698761.1348814114.1348908325.1349363044.5";
    $snoopy->cookies ["__utmz"] = "126509969.1348908325.4.2.utmcsr=botvisit.myip.ms|utmccn=(referral)|utmcmd=referral|utmcct=/bing___WUBDSgdvbCswJT98OCs=___2012-09-29_09-44.html";
    $snoopy->cookies ["__unam"] = "81039ec-13a0b961c15-7598818c-85";
    $snoopy->cookies ["s2_csrf_cookie_name"] = "fd7925cb49a7525998aaa797449f6527";
    $snoopy->cookies ["__utmc"] = "126509969";
    $snoopy->cookies ["__utmb"] = "126509969.4.10.1349363044";
    $snoopy->cookies ["s2_uID"] = "516";
    $snoopy->cookies ["s2_uKey"] = "ba9dea75e6deb5c7b5f03df7c4fabe6b016c887e";
    // set an raw-header:
    $snoopy->rawheaders ["Pragma"] = "no-cache";
    // set some internal variables:
    $snoopy->maxredirs = 2;
    $snoopy->offsiteok = false;
    $snoopy->expandlinks = false;
    // set username and password (optional)//
    #$snoopy->user = "joe";
    
    #$snoopy->pass = "bloe";
    // fetch the text of the website www.google.com:
    if ($snoopy->fetch ( $url )) 
    {
        file_put_contents ( "./txt/".$ip, $snoopy->results);
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
	$arrA = getHTML('whois_tbl','whoisdata_tbl',$content);
	$content_1 = $arrA[1][0];
	if($content_1 == '')return '';
	
	//
	$arrA = getHTML('IP Location:','Resolve Host:',$content_1);
	$strB = $arrA[1][0];
    $arrA = getA($strB);
    $arrB = $arrA[2];
    $arrData['country'] = $arrB[0];
    $arrData['state'] = $arrB[1];
    $arrData['city'] = $arrB[2];
    
    //获取owner
    $arrA = getHTML('Owner:','Address:',$content_1);
	$arrB = $arrA[1][0];
    $arrA = getHTML('border=\'0\' >','<\/td><\/tr>',$arrB);
	$arrB = $arrA[1][0];
	$arrB = explode("</td></tr>",$arrB);
    $arrData['owner'] = trim($arrB[0]);
    //获取ASN: 
    $arrA = getHTML('ASN: ','IP Blacklist Check:',$content_1);
	$arrB = $arrA[1][0];
	$arrA = getHTML('<td>','<\/td><\/tr>',$arrB);
	$arrB = $arrA[1][0];
    $arrData['asn'] = $arrB;
    //获取Maps: 
    $arrA = getHTML('link_extmap','newwin.png\'><\/a><\/div>',$content_1);
	$arrB = $arrA[0][0];
	$arrA = getA($arrB);
	$arrB = $arrA[1][0];
	$temp = parse_url($arrB);
	parse_str($temp['query'],$arrB);
	$arrB = explode("(IP",$arrB['q']);
    $arrData['map'] = trim($arrB[0]);
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
    
    
    $arrData = array();
	$arrData['country'] = addslashes($country);
    $arrData['state'] = addslashes($state);
    $arrData['city'] = addslashes($city);
    $arrData['map'] = addslashes($map);
    $arrData['asn'] = addslashes($asn);
	$arrData['owner'] = addslashes($owner);
    $arrData['md_str'] = $md_str;
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
