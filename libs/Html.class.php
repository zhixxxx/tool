<?php
/**
 * HTML功能集
 * @author michael
 *
 */
class HTML {
	/**
 	 * 万能正则匹配
 	 */    
    public static function getByPreg($preg,$str,$all=false) {
        $arrTemp = array ();
        if($all){
            preg_match_all ( $preg, $str, $arrTemp );
        }else{
            preg_match ( $preg, $str, $arrTemp );
        }
        return $arrTemp;
    }
	/**
 	 * 返回内容里的HTML标签的内容
 	 */
    public static function getHTML($s, $e, $str, $all=false) {
        $arrTemp = array ();
        $preg = "/{$s}(.*){$e}/is";
        if($all){
            preg_match_all ( $preg, $str, $arrTemp );
        }else{
            preg_match ( $preg, $str, $arrTemp );
        }
        return $arrTemp;
    }
	/**
 	 * 返回内容里的A标签属性
 	 */
    public static function getA($str,$all=true) {
        $arrTemp = array ();
        $preg = '/<a.*?(?: |\\t|\\r|\\n)?href=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>(.+?)<\/a.*?>/sim';
        if($all){
            preg_match_all ( $preg, $str, $arrTemp );
        }else{
            preg_match ( $preg, $str, $arrTemp );
        }
        return $arrTemp;
    }
	/**
 	 * 返回内容里的IMG标签属性
 	 */
    public static function getIMG($str,$all=true) {
        $arrTemp = array ();
        $preg = "/<img(.[^<]*)src=\"?'?(.[^<\"']*)\"?'?(.[^<]*)\/?>/is";
        if($all){
            preg_match_all ( $preg, $str, $arrTemp );
        }else{
            preg_match ( $preg, $str, $arrTemp );
        }
        return $arrTemp;
    }
    public static function expandlinks($links,$URI)
    {
    	preg_match("/^[^\?]+/",$URI,$match);
    	$match = preg_replace("|/[^\/\.]+\.[^\/\.]+$|","",$match[0]);
    	$match = preg_replace("|/$|","",$match);
    	$match_part = parse_url($match);
    	$match_root = $match_part["scheme"]."://".$match_part["host"];
    	$search = array(	"|^(\/)|i",
    							"|/\./|",
    						);
    	$replace = array(	$match_root."/",
    							$match."/",
    						);			
    	$expandedLinks = preg_replace($search,$replace,$links);
    	return $expandedLinks;
    }
    
    public static function cutstr_html($string)    
    {
         $string = strip_tags($string);
         $string = preg_replace ('/\n/is', '', $string);
         $string = preg_replace ('/ |　/is', '', $string);
         $string = preg_replace ('/&nbsp;/is', '', $string);
          
         preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $string, $t_string);   
         $string = join('', array_slice($t_string[0], 0, $sublen));
          
         return $string;
    }
    public function guolvLink($content,$guolvlink){
        $arrA = HTML::getA($content);
        if(empty($arrA[1])) return $content;
        foreach($arrA[1] as $key=>$val){
            $match = array();
            preg_match("/[".$guolvlink."]/",$val,$match);
            if(empty($match)) continue;
            $content = str_replace($arrA[0][$key],$arrA[2][$key],$content);    
        }
        return $content;
    }
}
?>