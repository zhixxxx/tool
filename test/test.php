<?php
include "../libs/snoopy/Snoopy.class.php";
$snoopy = new Snoopy ();

#$snoopy->proxy_host = "http://myip.ms/info/whois/1.22.0.0";
#$snoopy->proxy_port = "80";
// set browser and referer:
$snoopy->agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)";
$snoopy->referer = "http://myip.ms/info/whois/1.22.0.0";
// set some cookies:
$snoopy->cookies ["PHPSESSID"] = '4dri7dough7a797gkuko55qo51';
$snoopy->cookies ["s2_uLang"] = "en";
$snoopy->cookies ["s2_theme_ui2"] = "red";
$snoopy->cookies ["__utma"] = "126509969.66698761.1348814114.1348899794.1348908325.4";
$snoopy->cookies ["__utmz"] = "126509969.1348908325.4.2.utmcsr=botvisit.myip.ms|utmccn=(referral)|utmcmd=referral|utmcct=/bing___WUBDSgdvbCswJT98OCs=___2012-09-29_09-44.html";
$snoopy->cookies ["__unam"] = "81039ec-13a0b961c15-7598818c-70";
$snoopy->cookies ["s2_csrf_cookie_name"] = "d3fc71389c4980497d1934f4ccbcc7ce";
$snoopy->cookies ["__utmc"] = "126509969";
$snoopy->cookies ["__utmb"] = "126509969.24.10.1348908325";
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
if ($snoopy->fetch ( "http://myip.ms/info/whois/1.22.0.0" )) 
{
    echo $snoopy->results; 
} 
else 
{
    print "Snoopy: error while fetching document: " . $snoopy->error . "\n";

}

?>
