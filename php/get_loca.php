<?php
date_default_timezone_set('PRC');//把时间调到北京时间,php5默认为格林威治标准时间
header("Access-Control-Allow-Origin: *");//告诉接收数据的对象此页面允许任何域的访问
header('Content-type:text/json');//告诉接收数据的对象此页面输出的是json数据；
if(getIP()!="Unknow"){
$url='http://ip.taobao.com/service/getIpInfo.php?ip='.getIP();//http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=.getIP();
$jsonStr = file_get_contents($url);
$obj = json_decode($jsonStr);
if($obj->code==0){//如果请求成功
	$obj=$obj->data;//相关数据
	$str0=$obj->country;//国家
	$str1=$obj->area;//地区
	$str2=$obj->region;//省份
	$str3=$obj->city;//城市
	$str4=$obj->county;//县
	$str5=$obj->isp;//网络运营商
	$json='{"result":"0","loca":"'.$str0.$str1.$str2.$str3.$str4.$str5.'"}';
	echo $json;
	return;
}
}
echo '{,"result":"1"}';
// 定义一个函数getIP() 
function getIP() 
{ 
global $ip; 
if (getenv("HTTP_CLIENT_IP")) 
$ip = getenv("HTTP_CLIENT_IP"); 
else if(getenv("HTTP_X_FORWARDED_FOR")) 
$ip = getenv("HTTP_X_FORWARDED_FOR"); 
else if(getenv("REMOTE_ADDR")) 
$ip = getenv("REMOTE_ADDR"); 
else 
$ip = "Unknow"; 

return $ip; 
}
?> 