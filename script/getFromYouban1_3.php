<?php
include("../configs/story.inc.php");
include "../libs/Http.class.php";
$db = Mysql::getInstance();
define("FILENAME",APP_DIR.'data/source_cate_url.php');
define("URL","http://wap.baoruan.com");
define("TABLE","ip_in");
define("SLEEP_TIME",5);
define("CHANGE_IP",false);
define("IP_BLACK_SIGN","You've reached the maximum amount");
define("addCategory","http://localhost/phpcms/source/tool/addCategory.php?");
define("add","http://localhost/phpcms/source/tool/add.php?pw=phpcms18924193721");
define("CACHE_NEWID",'/youban/');
define("DATA_NEWID",DATA_DIR.'/story/');

define("DATA_NEWID_FILE",'youban_23.txt');//youban_12.txt
/*
$cate = '3-4岁';//'成语故事';//'睡前故事';//'格林童话';//'安徒生童话';//'幼儿故事';//'动画故事';//'神话故事';//'名人故事';//'寓言故事';//'成语故事';//test; 
$arrUrl[] = 'http://www.youban.com/book/pdcate.php?type=1&ageid=12&push=1&page=1';
$arrUrl[] = 'http://www.youban.com/book/pdcate.php?type=1&ageid=12&push=1&page=2';
$arrUrl[] = 'http://www.youban.com/book/pdcate.php?type=1&ageid=12&push=1&page=3';
$arrUrl[] = 'http://www.youban.com/book/pdcate.php?type=1&ageid=12&push=1&page=4';
*/

$cate = '5-6岁';
$arrUrl[] = 'http://www.youban.com/book/pdcate.php?type=1&ageid=48&push=1&page=1';
$arrUrl[] = 'http://www.youban.com/book/pdcate.php?type=1&ageid=48&push=1&page=2';
$arrUrl[] = 'http://www.youban.com/book/pdcate.php?type=1&ageid=48&push=1&page=3';
$arrUrl[] = 'http://www.youban.com/book/pdcate.php?type=1&ageid=48&push=1&page=4';
$arrUrl[] = 'http://www.youban.com/book/pdcate.php?type=1&ageid=48&push=1&page=5';
$arrUrl[] = 'http://www.youban.com/book/pdcate.php?type=1&ageid=48&push=1&page=6';
//$arrUrl[] = 'http://www.youban.com/mp3-t3203.html';
//$arrUrl[]='http://www.youban.com/mp3-t3206.html';
//$arrUrl[] = 'http://www.youban.com/mp3-t3665.html';
//$arrUrl[] = 'http://www.youban.com/mp3-t3205.html';
//$arrUrl[] = 'http://www.youban.com/mp3-t3204.html';

/*

*/


foreach($arrUrl as $url){
$arrA = getListWWW($url);
foreach($arrA[2] as $key=>$val){
	if(strpos($val,'img')==true || strpos($val,'span') == true){
		continue;
	}
	$tempArr[] = $val.'`'.$cate;	
}echo $url."\n";
}
//var_dump($tempArr);exit;
saveArr($tempArr);

exit;
function saveArr($data){
	$str = implode("\r\n",$data);
	file_put_contents(DATA_NEWID.DATA_NEWID_FILE,$str);
	
}


function getIMG($str,$all=true) {
        $arrTemp = array ();
        $preg = "/<img(.[^<]*) original=\"?'?(.[^<\"']*)\"?'?(.[^<]*)\/?>/is";
        if($all){
            preg_match_all ( $preg, $str, $arrTemp );
        }else{
            preg_match ( $preg, $str, $arrTemp );
        }
        return $arrTemp;
    }
    
//获取类别
#var_dump(getCateTXT());exit;
#$data = sourceUrl();
#var_dump($data);exit;
#$data2 = getCateWWW($data);
#二次获取
#$data = getCateWWW($data,$data2);
#var_dump($data);exit;
#saveCateTXT($data);
#updateCateTXT();
#updateCate2TXT();
#var_dump(getCateTXT());exit;
#insertCate();

//获取内容数据
insertContent();
//http://cloud.51cto.com/col/386/list_386_100.htm
function insertContent(){
    $arrCate = getCateTXT();
    $sort = getStatus('51cto-sort');
    $conTotal = getStatus('51cto-total');
    $status = false;
    foreach($arrCate as $key=>$val){
        if($sort != '' && !$status){
            if($key == $sort){
                $status = true;
            }else{
                continue;
            }
        }
        $arr = explode("`",$val);
        if(count($arr)<=2)continue;
        $arrTemp = explode(":",$arr[0]);
        $catid = $arrTemp[count($arrTemp)-1];
        $arrTemp = explode("/",$key);
        $otherid = $arrTemp[count($arrTemp)-2];
        $conCount = 0;
        for($page=1;$page<=50;$page++){
            //list地址组合
            $url = $key."list_".$otherid."_".$page.".htm";
            $arrList = getListWWW($url);
            if($arrList === 1){
                $list404++;
                if($list404 == 5){
                    $list404 = 0;
                    break;
                }
            }
            if(!empty($arrList[1])){
                foreach($arrList[1] as $key2=>$val2){
                    $arrData = getContentWWW($val2);
                    $arrData['cid'] = $catid;
                    $arrData['title'] = $arrList[2][$key2];
                    $url = add;
                    $content = Http::file_get_contents($url,$arrData);
                    
                    if(strpos($content,'ok')!==false){
                        $conTotal++;
                    }
                    echo $content."\n".$conTotal."\n\n";
                }
            }
            #exit;
        }
        updateStatus("51cto-sort",$key);
        updateStatus("51cto-total",$conTotal);
    }
}


exit;
function getStatus($key){
    $filename = '../data/status.php';
    $data = include($filename);
    if(!$data)$data = array();
    return $data[$key];
}
function updateStatus($key,$val){
    $filename = '../data/status.php';
    $data = include($filename);
    if(!$data)$data = array();
    $data[$key] = $val;
    $str = "<?php\r\nreturn array(\r\n";
    foreach($data as $key=>$val){
	    $str .= "'$key' => '$val',\r\n";
    }
    $str .= ");\r\n?>";
	file_put_contents($filename,$str); 
}
function sourceUrl(){
    //提取无法识别的url
    $arrTemp = getCateTXT();
    foreach($arrTemp as $key=>$val){
        if($val == '无法识别'){
            $arr[$key] = $key; 
        }
    }
    
    $arrTemp = array(
        'http://developer.51cto.com/web/',
        'http://sysapp.51cto.com/',
        'http://developer.51cto.com/col/1422/',    
    );
    foreach($arrTemp as $key=>$val){
        $arr[$val] = $val; 
    }
    
    $str = '<ul>
        <li><a href="http://network.51cto.com" target="_blank">网络</a></li>
        <li><a href="http://netsecurity.51cto.com" target="_blank">安全</a></li>
        <li><a href="http://developer.51cto.com" target="_blank">开发</a></li>
        <li><a href="http://database.51cto.com" target="_blank">数据库</a></li>
        <li><a href="http://server.51cto.com" target="_blank">服务器</a></li>
        <li><a href="http://os.51cto.com" target="_blank">系统</a></li>
        <li><a href="http://virtual.51cto.com" target="_blank">虚拟化</a></li>
        <li><a href="http://cloud.51cto.com" target="_blank">云计算</a></li>
        
        <li><a href="http://developer.51cto.com/embed/" target="_blank">嵌入式</a></li>
        <li><a href="http://mobile.51cto.com/" target="_blank">移动开发</a></li>
        </ul>
    ';
    $a = HTML::getA($str);
    $arrTemp = $a[1];
    foreach($arrTemp as $key=>$val){
        $arr[$val] = $val; 
    }
    return $arr;
}
function updateCateTXT(){
    echo "updateCate start...\n";
    $arrData = getCateTXT();
    $arrTemp = array();
    foreach($arrData as $key=>$val){
        $arr = explode("`",$val);
        if(!empty($arr[1])){
            if(count($arr) == 2){
                $arrTemp[$key] = $val;
            }else{
                $tempData[$key] = $val;
            }
            continue;
        }
        $parent = getParentWWW($key);
        if(empty($parent)){
            $tempData[$key] = '无法识别';
            continue;
        }
        if($parent == 1){
            $tempData[$key] = $val;
            continue;
        }
        
        $tempData[$key] = 'root`'.$parent[0][0].'`'.$parent[1][0].'`'.$parent[2][0].'`'.$parent[3][0];
        
        if(!in_array('root`'.$parent[0][0],$arrData)){
            $arrTemp[$parent[0][1]] = 'root`'.$parent[0][0];
        }
    }
    asort($tempData);
    asort($arrTemp);
    #var_dump($arrTemp);exit;
    $arrData = $arrTemp + $tempData;
    #var_dump($arrData);exit;
    #var_dump($arrData);
    saveCatePHP($arrData);
    #$content = json_encode($arrData);
    #file_put_contents(FILENAME,$content);
    echo "updateCate end...\n";
}
function updateCate2TXT(){
    echo "updateCate2 start...\n";
    $arrData = getCateTXT();
    $arrTemp = array();
    foreach($arrData as $key=>$val){
        if(strpos($val,'活动专区') !== false || strpos($val,'回收站') !== false || strpos($val,'更多') !== false){
            continue;
        }
        if(strpos($key,'.htm') !== false || strpos($key,'book') !== false){
            continue;
        }
        if(strpos($key,'.com/col/') && substr($key, -1)!='/'){
            $key .='/';
        }
        $tempData[$key] = $val;
        
    }
    $arrData = $tempData;
    #var_dump($arrData);exit;
    saveCatePHP($arrData);
    #$content = json_encode($arrData);
    #file_put_contents(FILENAME,$content);
    echo "updateCate2 end...\n";
}
function getListWWW($url){
    $content = getUrlContent($url);
    if(!$content){return 1;}
    //$content = iconv('GB2312', 'UTF-8', $content);
    $arrA = HTML::getHTML('SecListbody','pages',$content);
	$arrA = HTML::getA($arrA[0]);
	return $arrA;	
}
function getContentWWW($url){
    $content = getUrlContent($url);
    if(!$content){return 1;}
    $content = iconv('GB2312', 'UTF-8', $content);
    $arrA = HTML::getHTML('<div class=\"brieftext\">','<p class=\"ad\">',$content);
    $arrB['description'] = substr(HTML::guolvLink($arrA[1],GUOLVLINK),0,250);
    $arrA = HTML::getHTML('<div id=\"content\">','<p class=\"blank10\"><\/p> ',$content);
    $arrB['content'] = HTML::guolvLink($arrA[1],GUOLVLINK);
	return $arrB;	
}

function getParentWWW($url){
    $content = getUrlContent($url);
 
    if(!$content){return 1;}
    $content = iconv('GB2312', 'UTF-8', $content);
    $arrA = HTML::getHTML('<div class=\"m_l_menu\">','<div class=\"m_l_subMenu\">',$content);
	$arrA = HTML::getA($arrA[0]);
	$arrA[1] = HTML::expandlinks($arrA[1],'');
    foreach($arrA[1] as $key=>$val){
        if($key == 0)continue;
        $arrA[2][$key] = HTML::cutstr_html($arrA[2][$key]);
	    $arrB[] = array($arrA[2][$key],$val);
	}
	return $arrB;	
}
function saveCateTXT($data){
    if(empty($data))return;
    $arr = getCateTXT();
    
    foreach($data as $key=>$val){
        if(empty($arr) || !in_array($key,$arr)){
            $arr[$key] = $val;
        }
    }
    saveCatePHP($arr);
}
function getCateTXT(){
    $arr = include(FILENAME);
    if(!$arr)$arr = array();
    return $arr;
}
function getCateWWW($data,$data2 = array()){
    if(!is_array($data))
    $data = array($data);
    if(!empty($data2)){
        foreach($data2 as $key=>$val){
            $data[] = $key;
        }
    }
    
    foreach($data as $url){
        if($url == '')continue;
        $content = getUrlContent($url);
        $content = iconv('GB2312', 'UTF-8', $content);
    	$arrA = HTML::getA($content);
    	$arrA[1] = HTML::expandlinks($arrA[1],URL);
    	foreach($arrA[1] as $key=>$val){
    	    if(strpos($val,'51cto.com/col/') !==false && strpos($val,'.htm') === false){
    	        $arrB[$val] = HTML::cutstr_html($arrA[2][$key]);
    	    }
    	}
    }
	return $arrB;
}
function insertCate(){
    $arrData = getCateTXT();
    #var_dump($arrData);exit;
    foreach($arrData as $key=>$val){
        $catname = $parentname = '';
        $arr = explode("`",$val);
        if(count($arr) < 2)continue;
        $root = $arr[0];
        $catid = explode(":",$root);
        if(!$catid[1]){
            if(count($arr) == 2){
                $catname = $arr[1];
                $parentname = '';
            }
            if(count($arr)>=3 && $arr[2] != ''){
                $catname = $arr[2];
                $parentname = $arr[1];
            }
            if(count($arr)>=4 && $arr[3] != ''){
                $catname = $arr[3];
                $parentname = $arr[1];
            }
            if(count($arr)>=5 && $arr[4] != ''){
                $catname = $arr[4];
                $parentname = $arr[1];
            }
            echo $url = addCategory.'catname='.$catname.'&parentname='.$parentname;
            $content = Http::file_get_contents($url);
            echo "\n".$content."\n";
            $catid = explode("````",$content);
            $arrData[$key] = str_replace('root`','root-'.$arr[1].':'.$catid[1].'`',$val);
        }
        
    }
    saveCatePHP($arrData);
}

function getUrlContent($url,$creat=false){
	if($url == ''){
		return;
	}
    $content = '';
    $dir = CACHE_DIR.CACHE_NEWID."/".substr(md5($url),0,3)."/";
    if(!is_dir($dir)){
        mkdir($dir,0777,true);
    }
    $filePath = $dir.md5($url);
    if(is_file(CACHE_DIR.CACHE_NEWID.md5($url))){
        rename(CACHE_DIR.CACHE_NEWID.md5($url),$filePath);
    }
    
    echo $url."\n".$filePath."\n";
    if(is_file($filePath)){
        $content = @file_get_contents($filePath);
    }
    if(!$content || $creat == true){
        echo "need get\n";
        #sleep(1);
        $content = Http::file_get_contents($url);
        if($content != ''){
            file_put_contents($filePath,$content);
        }
    }
    return $content;
}

function saveCatePHP($data){
    $str = "<?php\r\nreturn array(\r\n";
    foreach($data as $key=>$val){
	    $str .= "'$key' => '$val',\r\n";
    }
    $str .= ");\r\n?>";
	file_put_contents(FILENAME,$str);  
}
//更新目录
function moveFile($url,$creat=false){
    $dir = CACHE_DIR.CACHE_NEWID."/".substr(md5($url),0,2)."/";
    if(!is_dir($dir)){
        mkdir(CACHE_DIR.CACHE_NEWID,0777,true);
    }
    
    $filePath = $dir.md5($url);
    echo $url."\n".$filePath."\n";
    $content = @file_get_contents($filePath);
    if(!$content || $creat == true){
        echo "need get\n";
        sleep(1);
        $content = Http::file_get_contents($url);
        if($content != ''){
            file_put_contents($filePath,$content);
        }
    }
    return $content;
}
?>
