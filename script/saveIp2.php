<?php
include("../configs/common.inc.php");
$db = Mysql::getInstance();

$res = $db->getAll('ip','*','country="IN"');
foreach($res as $val){
    $ip1 = $val['ip1'];
    $ip2 = $val['ip2'];
    $a = bindec(decbin(ip2long($ip1)));
    $b = bindec(decbin(ip2long($ip2)));
    $i=0;
	$temp1 = explode('.',$ip1);
	$temp2 = explode('.',$ip2);
	$var1 = $temp1[0].'.'.$temp1[1].'.0.0';
	$var2 = $temp2[0].'.'.$temp2[1].'.0.0';
	$var1int = bindec(decbin(ip2long($var1)));
	$var2int = bindec(decbin(ip2long($var2)));
	if($var1int < $var2int){
		$tempip2 = $temp1[0].'.'.$temp1[1].'.255.255';
		$tempipe = bindec(decbin(ip2long($tempip2)));
	}else{
		$tempip2 = $ip2;
		$tempipe = $b;
	}
	$arrIn['ip1'] = $ip1;
	$arrIn['ip2'] = $tempip2;
	$arrIn['ips'] = $a;
	$arrIn['ipe'] = $tempipe;
	$db->insert('ip_in_big',$arrIn);
	var_dump($arrIn);
    $i = 1;
	if($var1int != $var2int){
	
	$ttempa = $var1int + 256 * 256;
	while($ttempa <= $b){
		$arrIn = array();
		$arrIn['ip1'] = long2ip($ttempa);
		$arrIn['ips'] = $ttempa;
		
		if($ttempa + 256 * 256 > $b){
			$arrIn['ip2'] = $ip2;
			$arrIn['ipe'] = $b;
		}else{
			$arrIn['ip2'] = long2ip($ttempa + 256 * 256 - 1);
			$arrIn['ipe'] = $ttempa + 256 * 256 - 1;
		}
        $rs = $db->insert('ip_in_big',$arrIn);
        if (! $rs){
		    Logger::w ( LL_ERROR, "insert fail:" . $long . $db->getLastError() );
		}
        $ttempa = $ttempa + 256 * 256;
        var_dump($arrIn);
		$i++;
    }}
    echo $ip1 . "---" . $ip2 . " " . $i . "\n";
    
}


?>
