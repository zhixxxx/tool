<?php
include "../libs/Snoopy.class.php";
$snoopy = new Snoopy ();
$snoopy->fetchform("http://localhost/tool/test/editor/Blogfunction.txt");
print_r($snoopy->results);
$snoopy->fetchform("http://localhost/tool/test/editor/Datefunctions.txt");
print_r($snoopy->results);
$snoopy->fetchform("http://localhost/tool/test/editor/Forumchat.txt");
print_r($snoopy->results);
$snoopy->fetchform("http://localhost/tool/test/editor/Functionsformoderators.txt");
print_r($snoopy->results);
$snoopy->fetchform("http://localhost/tool/test/editor/Loginregistration.txt");
print_r($snoopy->results);
$snoopy->fetchform("http://localhost/tool/test/editor/Other.txt");
print_r($snoopy->results);
$snoopy->fetchform("http://localhost/tool/test/editor/Privatemessagesfriends.txt");
print_r($snoopy->results);

?>