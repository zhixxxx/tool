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

define("DATA_NEWID_FILE",'youban_7.txt');//youban_7.txt
//$cate = '幼儿故事';//'幼儿故事';//'动画故事';//'神话故事';//'名人故事';//'寓言故事';//'成语故事';//test; 
//图片地址--绝对路径
define('PATH_IMG','/mnt/www/ppgt_admin/public/attachment/platform_icon/201001/');
//图片地址--相对路径
define('PATH_IMG_2','platform_icon/201001/');
//需要抓取的页面
for($i=1;$i<=77;$i++){
    $arrUrl[] = "https://www.p2peye.com/platform/z1/p{$i}/";
}
$k = 1;     //第几页
$q = '';    //失败页面
$errorInfo = '';    //失败页面条数
$c = 0;     //第几条
foreach($arrUrl as $url){
    $arrA = getListWWW($url);
    foreach ($arrA as $key => $value) {
        $c += 1;
        //echo $c.'------------'."\n";
        //echo "https://{$value}";die;
        //https://yirendai.p2peye.com/
        $data = getUrlInfo("https://{$value}");
        //添加更新数据
        $res = $db->getRow('fl_platform','*',"platform_name='{$data['platform_name']}'");
        if(!$res){
            $result = $db->insert('fl_platform',$data);
            $result_id = $db->getLastInsertId();
            if($result && $result_id){
                echo "第{$k}页--第{$c}条-------添加-----成功";
                //=========================平台分类关联表=================================
                $content_info = getUrlContent("https://{$value}");
                 //平台实力
                $strength = HTML::getHTML('class="ptsl_detail"','officialwebsiteTpl',$content_info);
                $strength = explode('</span>',$strength[0]);
                for($i=1;$i<count($strength);$i++){
                    $arr_com = explode('</div>',$strength[$i]);
                    $arr_com = explode('：',$arr_com[0]);
                    $res = $db->getRow('fl_platform_column','*',"column_name='".$arr_com[0]."'");
                    $result = $db->getRow('fl_platform_column','*',"column_id={$res['column_id']} and platform_id={$result_id}");
                    if($res && !$result){
                        $db->insert('fl_platform_column_relation',array('column_id'=>$res['column_id'],'platform_id'=>$result_id));
                    }
                }
                //======================================================================
                //==============================保障模式=================================
                $guarantee = HTML::getHTML('platform-list','\/tbody',$content_info);
                $guarantee = HTML::getHTML('cell-inner','<\/div>',$guarantee[1]);
                $guarantee = explode('cell-inner">',$guarantee[1]);
                $guarantee = $guarantee[count($guarantee)-1];
                $guarantee = explode('|',$guarantee);
                for($i=0;$i<count($guarantee);$i++){
                    $res = $db->getRow('fl_platform_column','*',"column_name='".$guarantee[$i]."'");
                    $result = $db->getRow('fl_platform_column','*',"column_id={$res['column_id']} and platform_id={$result_id}");
                    if($res && !$result){
                        $db->insert('fl_platform_column_relation',array('column_id'=>$res['column_id'],'platform_id'=>$result_id));
                    }
                }
                //======================================================================
            }else{
                echo "第{$k}页--第{$c}条-------添加-----失败";
                $errorInfo .= "第{$k}页-第{$c}条-添加失败---";
            }
        }
    }
    $c = 0;
    if(!empty($arrA)){
        echo "第{$k}页-----------------------------成功\n";
    }else{
        echo "第{$k}页-----------------------------失败\n";
        $q .=$k.',';
    }
    $k += 1;
}
$q = rtrim($q,',');
echo "==========抓取失败页面有:({$q})==========\n";
echo "==========失败详细({$errorInfo})========\n";
echo "=======================分割线========================\n";
//var_dump($data);
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

function getUrlInfo($url){
    //首页
    $content = getUrlContent($url);
    $arrA = HTML::getHTML('class="lo"','ui-contact-case',$content);
    $company_xin = HTML::getHTML('公司规模','注册资本',$content);
    //1-规模  3-债权转让
    $company_xin = explode('cell-inner">',$company_xin[1]);
    $company_g = explode('</div>',$company_xin[1]);//$company_g[0];
    $company_z = explode('</div>',$company_xin[3]);//$company_z[0];
    //省份
    $shengfen = HTML::getHTML('ui-contact-case','ui-contact ui-contact-tel fn-contact',$content);
    $shengfen = HTML::getHTML('ui-contact-inner">','<\/span>',$shengfen[0]);
    $shengfen = explode('</span>',$shengfen[1]);
    $shengfen = explode('省',$shengfen[0]);
    if($shengfen[1] == ''){
        $shengfen = explode('市',$shengfen[0]);
        $shengfen = $shengfen[0].'市';
    }else{
        $shengfen = $shengfen[0].'省';
    }
    //备案
    $contentBeian = getUrlContent($url.'/beian/');
    $arrB = HTML::getHTML('class="kvs"','class="detail"',$contentBeian);
    //平台图标
    $img = HTML::getIMG($arrA[0]);
    if(!file_exists(PATH_IMG)){
        mkdir(PATH_IMG,0777,true);
    }
    $img_path = 'https:'.$img[2][0];
    //获取图片
    $img_info = file_get_contents($img_path);
    //截取图片名字和后缀
    $img_name = explode('/',$img_path);
    //图片保存到本地---绝对路径
    $images = PATH_IMG.$img_name[count($img_name)-1];
    //相对路径
    $img = PATH_IMG_2.$img_name[count($img_name)-1];
    //保存图片
    file_put_contents($images,$img_info);
    //平台名称
    $name = HTML::getHTML('alt="','">',$arrA[0]);
    //平台简介
    $intro = HTML::getHTML('bd active ">','id="tocomment"',$content);
    $intro = explode('</div>',$intro[1]);
    //等级$score[1][0]--排名$score[1][1]--评分$score[1][2]
    $score = HTML::getHTML('bonus','hd-more',$content);
    $score = HTML::getHTML('<strong>','<\/strong>',$score[0]);
    preg_match_all('/<strong>(.*)<\/strong>/',$score[0],$score,true);
    //0-平台实力 1-股东结构图 2-工商信息 3-股东信息 4-主要人员 5-备案信息
    $arrB = explode('<div class="detail">',$arrB[0]);
    //工商信息--------------------------2
    $info_gs = HTML::getHTML('class="v"',"<\/div>",$arrB[2]);
    $info_gs = explode('<div class="v"',$info_gs[0]);
    for($i=0;$i<17;$i++){
        if($i==9 || $i==10 || $i==12 || $i==16){
            continue;
        }
        $company_name = HTML::getHTML('">',"<\/div>",$info_gs[$i]);
        $company = explode("</div>",$company_name[1]);
        $company_info[] = $company[0];
    }
    //公司成立时间
    $company_time = explode('</div>',$info_gs[9]);
    $company_time = ltrim($company_time[0],'>');
    //营业期限
    $time = explode('</div>',$info_gs[10]);
    $time = ltrim($time[0],'>');
    //上线下线时间
    $start_time = time();
    $end_time = strtotime("2030-10-01 00:00:00");
    //备案信息--------------------------5
    $info_ba = HTML::getHTML('class="v"',"<\/div>",$arrB[5]);
    $info_ba = explode('<div class="v"',$info_ba[0]);
    //备案域名
    $record_domain = HTML::getHTML('link">','<\/span>',$info_ba[0]);
    //备案单位名称
    $record_campany = HTML::getHTML('">','<\/div>',$info_ba[2]);
    $record_campany = explode('</div>',$record_campany[1]);
    $record_campany = ltrim($record_campany[0],' ');
    //备案单位性质
    $record_campany_xz = HTML::getHTML('>','<\/div>',$info_ba[3]);
    $record_campany_xz = explode('</div>',$record_campany_xz[1]);
    $record_campany_xz = ltrim($record_campany_xz[0],' ');
    //域名备案时间
    $record_time = HTML::getHTML('>','<\/div>',$info_ba[1]);
    $record_time = explode('</div>',$record_time[1]);
    //ICP号
    $record_icp = HTML::getHTML('">','<\/div>',$info_ba[4]);
    $record_icp = explode('</div>',$record_icp[1]);
    //ICP许可证编号
    $record_icp_pass = HTML::getHTML('>','<\/div>',$info_ba[5]);
    $record_icp_pass = explode('</div>',$record_icp_pass[1]);
    //核准日期
    $company_check_time = ltrim($info_gs[12],'>');
    $company_check_time = explode('</div>',$company_check_time);
    //经营范围
    $company_business_field = ltrim($info_gs[16],'>');
    $company_business_field = explode('</div>',$company_business_field);
    //首字母
    $firstchar_ord=ord(strtoupper($name[1])); 
    $s=iconv("UTF-8","gb2312", $name[1]); 
    $asc=ord($s{0})*256+ord($s{1})-65536; 
    if($asc>=-20319 and $asc<=-20284)$first_letter = "A"; 
    if($asc>=-20283 and $asc<=-19776)$first_letter = "B"; 
    if($asc>=-19775 and $asc<=-19219)$first_letter = "C"; 
    if($asc>=-19218 and $asc<=-18711)$first_letter = "D"; 
    if($asc>=-18710 and $asc<=-18527)$first_letter = "E"; 
    if($asc>=-18526 and $asc<=-18240)$first_letter = "F"; 
    if($asc>=-18239 and $asc<=-17923)$first_letter = "G"; 
    if($asc>=-17922 and $asc<=-17418)$first_letter = "H"; 
    if($asc>=-17417 and $asc<=-16475)$first_letter = "J"; 
    if($asc>=-16474 and $asc<=-16213)$first_letter = "K"; 
    if($asc>=-16212 and $asc<=-15641)$first_letter = "L"; 
    if($asc>=-15640 and $asc<=-15166)$first_letter = "M"; 
    if($asc>=-15165 and $asc<=-14923)$first_letter = "N"; 
    if($asc>=-14922 and $asc<=-14915)$first_letter = "O"; 
    if($asc>=-14914 and $asc<=-14631)$first_letter = "P"; 
    if($asc>=-14630 and $asc<=-14150)$first_letter = "Q"; 
    if($asc>=-14149 and $asc<=-14091)$first_letter = "R"; 
    if($asc>=-14090 and $asc<=-13319)$first_letter = "S"; 
    if($asc>=-13318 and $asc<=-12839)$first_letter = "T"; 
    if($asc>=-12838 and $asc<=-12557)$first_letter = "W"; 
    if($asc>=-12556 and $asc<=-11848)$first_letter = "X"; 
    if($asc>=-11847 and $asc<=-11056)$first_letter = "Y"; 
    if($asc>=-11055 and $asc<=-10247)$first_letter = "Z"; 
    //-----------------------------||数据整合||-----------------------------
    //|||----------------------------------------------------------------|||
    //平台图标
    $data['platform_icon'] = $img;
    //平台英文名
    $data['platform_name_en'] = '';
    //推荐理由
    $data['platform_recom_reason'] ='';
    //平台名称
    $data['platform_name'] = $name[1];
    //平台简介
    $data['platform_intro'] = addslashes(strip_tags($intro[0]));
    //上线时间
    $data['time_online'] = $start_time;
    //下线时间
    $data['time_downline'] = $end_time;
    //综合评级
    $data['platform_score'] = $score[1][0];
    //公司名称
    $data['company_name'] = $company_info[0];
    //公司地址
    $data['company_address'] = $company_info[12];
    //营业执照号
    $data['company_num'] = $company_info[3];
    //公司类型
    $data['company_type'] = $company_info[8];
    //公司注册时间
    $data['company_time'] = strtotime($company_time);
    //注册资本
    $data['company_capital'] = $company_info[7];
    //实缴资本
    $data['company_shijiao'] = '';
    //登记机关
    $data['compay_authority'] = $company_info[9];
    //组织机构代码
    $data['org_num'] = $company_info[4];
    //税务登记号
    $data['compay_tax'] = '';
    //备案域名
    $data['record_domain'] = $record_domain[1];
    //备案单位
    $data['record_campany'] = $record_campany;
    //备案单位性质
    $data['record_campany_xz'] = $record_campany_xz;
    //备案时间
    $data['record_time'] = strtotime($record_time[0]);
    //ICP号
    $data['record_icp'] = $record_icp[0];
    //ICP许可证编号
    $data['record_icp_pass'] = $record_icp_pass[0];
    //账号管理费
    $data['cost_admin'] = '';
    //转让费
    $data['cost_transf'] = '';
    //充值理费
    $data['cost_cz'] = '';
    //提现费用
    $data['cost_trade'] = '';
    //VIP费用
    $data['cost_vip'] = '';
    //重仓排序
    $data['storage_index'] = 1;
    //首字母
    $data['first_letter'] = $first_letter;
    //省份
    $data['province'] = $shengfen;
    //经营状态
    $data['operation_state_temporary'] = $company_info[5];
    //公司规模
    $data['company_size'] = $company_g[0];
    //债权转让
    $data['company_rights'] = $company_z[0];
    //公司法人
    $data['company_legal'] = $company_info[6];
    //公司电话
    $data['company_phone'] = $company_info[1];
    //统一社会信用代号
    $data['company_credit_code'] = $company_info[2];
    //营业期限
    $data['company_business_time'] = $time;
    //核准日期
    $data['company_check_time'] = strtotime($company_check_time[0]);
    //所属行业
    $data['company_industry'] = $company_info[10];
    //经营范围
    $data['company_business_field'] = $company_business_field[0];
    //是否为爬虫录入
    $data['pachong'] = 1;
    //|||----------------------------------------------------------------|||
    //-----------------------------||数据整合||-----------------------------
    return $data;
}

function getListWWW($url){
    $content = getUrlContent($url);

    if(!$content){return 1;}
    $content = iconv('GB2312', 'UTF-8', $content);
    $arrA = HTML::getHTML('ui-result clearfix','c-page',$content);
    $data = explode('ui-result-item',$arrA[0]);
    foreach ($data as $key => $value) {
        $arr = HTML::getHTML("a href=\"\/\/","ui-result-pname",$value);
        $arr = explode("\"",$arr[1]);
        //var_dump($arr);
        if(!empty($arr[0])){
            //echo $arr[1]."\n";
            $arrData[] = rtrim($arr[0],'\"');
        }
    }
    //var_dump($arrData);
	return $arrData;	
}
function getContentWWW($url){
    $content = getUrlContent($url);
    if(!$content){return 1;}
    $content = iconv('GB2312', 'UTF-8', $content);
    $arrA = HTML::getHTML('<div class=\"index-header\">','<p class=\"navsparent\">',$content);
    var_dump($arrA);exit;
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



?>
