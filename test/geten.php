<?php
include("../configs/common.inc.php");
include "../libs/Http.class.php";
$arrUrl[] = "http://localhost/tool/test/editor/Blogfunction.txt";
#$arrUrl[] = "http://localhost/tool/test/editor/Datefunctions.txt";
$arrUrl[] = "http://localhost/tool/test/editor/Forumchat.txt";
$arrUrl[] = "http://localhost/tool/test/editor/Functionsformoderators.txt";
$arrUrl[] = "http://localhost/tool/test/editor/Loginregistration.txt";
$arrUrl[] = "http://localhost/tool/test/editor/Other.txt";
$arrUrl[] = "http://localhost/tool/test/editor/Privatemessagesfriends.txt";
foreach($arrUrl as $url){
$content = Http::file_get_contents($url);
$arrA = HTML::getHTML('action=\"wapka_transl.xhtml\">','<br\/><input type=\"submit\"',$content);
#var_dump($arrA);
$arrA = @explode("<br/>",$arrA[1]);
#var_dump($arrA);
foreach($arrA as $key=>$val){
    if($i==1){
        $arr = @explode("lnt[",$val);
        $arr = @explode("]",$arr[1]);
        $arrNew[$arr[0]] = $name;
        $name = '';
        $i = 0;
    }else{
        $i = 1;
        $name = HTML::cutstr_html($val);
        $name = str_replace(array("!",":","&lt;b&gt;","&lt;/b&gt;"),"",$name);
        $name = htmlspecialchars_decode($name);
    }
}

}
ksort($arrNew);
#var_dump($arrNew);
foreach($arrNew as $key=>$val){
    #echo "{$key}	=	{$val}\r\n";
    echo "insert into translate (text,id,lang,typ) values('{$val}',{$key},'en','i');\n";
}
?>