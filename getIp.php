<?php 
//处理数组元素
$arr=array('1','2','4)');
// array_walk($arr, function(&$value,$key){
// 	$value=(int)$value;
// });
$arr1=array_map('intval', $arr);
var_dump($arr1);

//获取客户端IP
// function getIp(){
// 	return $_SERVER['REMOTE_ADDR'];
// }
// var_dump(getIp());
// echo "<a href='?ip=".getIp()."'>提交IP</a><br>";
// var_dump($_SERVER);

function getIp(){
   if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
       $ip = getenv("HTTP_CLIENT_IP");
   else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
       $ip = getenv("HTTP_X_FORWARDED_FOR");
  else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
       $ip = getenv("REMOTE_ADDR");
  else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
           $ip = $_SERVER['REMOTE_ADDR'];
       else
           $ip = "unknown";
       return($ip);
}
var_dump(phpinfo());
$str='测试修改git status and git log';
 ?>