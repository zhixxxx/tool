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
    while($a<=$b){
        $long = long2ip($a);
        $rs = $db->insert('ip_list',array('ips'=>$a,'ip1'=>$long));
        if (! $rs){
		    Logger::w ( LL_ERROR, "insert fail:" . $long . $db->getLastError() );
		}
        $a = $a + 256;
        #echo $long."\n";
        $i++;
    }
    echo $ip1 . "---" . $ip2 . " " . $i . "\n";
    
}


?>
