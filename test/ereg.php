<?php
$match = '';
$str = 'A community is a place where communication and understanding happens';
preg_match_all ( '/t[^aeiou]/', $str, $match );
echo "<br />匹配没有属性的HTML标签中的内容：";
print_r ( $match );
    
    $str = '<b>bold font</b>> <p>paragraph text</p>';
    preg_match_all( '/(?<=<(\w{1})>).*(?=<\/\1>)/', $str, $match );
    echo "<br />匹配没有属性的HTML标签中的内容：";
    print_r ( $match );
?>
