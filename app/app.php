<?php

$type=$_GET['type'];
switch($type){
	case 'ping_net'://确认服务器是否正常
	$html ='201704192055';//返回版本号
	break;
	
	case 'get_main_html'://app主页面
	$html = file_get_contents('GG.html');
	break;
	
default:
$html='505';
}

exit($html);
?> 