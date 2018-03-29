<?php
restartTPLINK();
function restartTPLINK($sleepTime=0){
	if(CHANGE_IP == false)return;
	stopTPLINK();
	sleep($sleepTime);
	startTPLINK();
}
function stopTPLINK(){
	$action = "http://192.168.1.1/userRpm/StatusRpm.htm?Disconnect=%B6%CF%20%CF%DF&wan=1";
	doTPLINK($action);
}
function startTPLINK(){
	$action = "http://192.168.1.1/userRpm/StatusRpm.htm?Connect=%C1%AC%20%BD%D3&wan=1";
	doTPLINK($action);
}
function doTPLINK($action){
	$cookie_file = dirname(__FILE__).'/cookie.txt';
	//login
	$url = $action;
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_COOKIEJAR, $cookie_file);
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);//http认证
	curl_setopt($ch,CURLOPT_USERPWD,"admin:admin");// http认证
	$data=curl_exec($ch);
	curl_close($ch);
	#echo $data;
}
?>
