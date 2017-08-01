<?php
/*注意：json非数值数据的键和值必须用双引号*/

/*设置MySQL自动增长从某个指定的数开始 
use android_app;
alter table user AUTO_INCREMENT=1;
*/
/*富豪榜
select id,money, (SELECT count(distinct money) from user where money>t1.money)+1 as rank from user t1
*/
date_default_timezone_set('PRC');//把时间调到北京时间,php5默认为格林威治标准时间
header("Access-Control-Allow-Origin: *");//告诉接收数据的对象此页面允许任何域的访问
header('Content-type:text/json');//告诉接收数据的对象此页面输出的是json数据；

$type = $_POST['type'];//请求类型
$idortel='';//uid或手机号码类型
$account='';//uid或手机号码
$id = '';//用户唯一uid
$tel='';//手机号码
$brand='';//手机品牌型号
$imei ='';//手机唯一标识码
$name = '';//昵称
$pw = '';//密码
$reg_loca = '';//注册地址
$load_loca='';//最后登陆地点

$datetime=date("Y-m-d H:i:s",time());//服务器当前时间

$sqlhost = 'localhost';$sqlname = 'root';$sqlpw = 'luqihongmysql';$sqldb = 'android_app';//sql凭证

$query='';//sql语句

$json='{"result":"200"}';//输出信息变量

//echo $json;//如果提交的请求数据无误则输出200 ok，否则请求失败

$link = mysqli_connect($sqlhost, $sqlname, $sqlpw, $sqldb);// 面向过程创建sql连接
if (!$link) {// 检测sql连接
$json='{"result":"'.mysqli_connect_error().'"}';
    die($json);
}
//echo $json;//'连接成功

switch($type){
	case 'querytel'://查询手机号码是否已存在
	$tel = $_POST['tel'];
	$query= "SELECT tel FROM user where tel='".$tel."' limit 1";//sql语句：如果查询到一条匹配数据就不再往下查询
	if(mysqli_num_rows(mysqli_query($link,$query))>0){//如果手机号码已存在
		$json='{"result":"此手机号码已注册"}';
	}else{//如果手机号不存在
$json='{"result":"ok","tel":"'.$tel.'"}';//输出ok
	}
break;
	
	case 'register'://注册
	$tel = $_POST['tel'];
	$query= "SELECT tel FROM user where tel='".$tel."' limit 1";//sql语句：如果查询到一条匹配数据就不再往下查询
	if(mysqli_num_rows(mysqli_query($link,$query))>0){//如果手机号已存在
		$json='{"result":"此手机号已注册"}';
	}else{//如果手机号不存在
		$imei=$_POST['imei'];$name=$_POST['name'];$pw=$_POST['pw'];$reg_loca=$_POST['reg_loca'];$brand=$_POST['brand'];
$query = "INSERT INTO user (tel,imei,brand,name,pw,reg_time,reg_loca) VALUES ('".$tel."','".$imei."','".$brand."','".$name."','".$pw."','".$datetime."','".$reg_loca."')";
if(mysqli_query($link, $query)){//如果成功插入数据
$json='{"result":"ok","id":"'.mysqli_insert_id($link).'"}';//输出uid
}else{
	$json='{"result":"插入数据时出错,请稍后重试"}';
}
	}
break;

case 'login'://登陆
    $idortel=$_POST['idortel'];$pw=$_POST['pw'];$load_loca=$_POST['load_loca'];$imei=$_POST['imei'];$brand=$_POST['brand'];
    if($idortel=="id"){$account=$_POST['id'];//uid
	}else{$account=$_POST['tel'];//手机号码
	}
	$query="SELECT * FROM user WHERE ".$idortel."='".$account."' and imei='".$imei."' and brand='".$brand."' limit 1";
    $result=mysqli_query($link,$query);//返回一行
	if(mysqli_num_rows($result)>0){//如果账号与设备是绑定关系
    $query="SELECT * FROM user WHERE ".$idortel."='".$account."' and pw='".$pw."' limit 1";
	$result=mysqli_query($link,$query);//如果tel及密码正确返回一行
	if(mysqli_num_rows($result)>0){//行数大于0说明tel及密码正确
	$query="UPDATE user SET load_time='".$datetime."', load_loca='".$load_loca."' WHERE ".$idortel."='".$account."' limit 1";
	if(mysqli_query($link,$query)){//如果更新数据成功
	$row = mysqli_fetch_assoc($result);//以一个关联数组抓取一行结果
	$json='{"result":"ok","id":"'.$row["id"].'","tel":"'.$row["tel"].'","imei":"'.$row["imei"].'","brand":"'.$row["brand"].'","name":"'.$row["name"].'","sex":"'.$row["sex"].'","money":"'.$row["money"].'","money_pm":"'.$row["money_pm"].'","mood":"'.$row["mood"].'","reg_time":"'.$row["reg_time"].'","load_loca":"'.$row["load_loca"].'","load_time":"'.$row["load_time"].'"}';
	}else{//如果更新数据失败
	$json='{"result":"tel and password ok,but update data failed"}';
	}
	}else{
	$json='{"result":"账号或密码错误"}';
	}
	}else{//如果账号和设备不匹配
	$json='{"result":"binding"}';	
	}

mysqli_free_result($result);// 释放结果集
break;

case 'binding'://改绑手机
    $idortel=$_POST['idortel'];$pw=$_POST['pw'];$load_loca=$_POST['load_loca'];$imei=$_POST['imei'];$brand=$_POST['brand'];
    if($idortel=="id"){$account=$_POST['id'];//uid
	}else{$account=$_POST['tel'];//手机号码
	}
	$query="SELECT * FROM user WHERE ".$idortel."='".$account."' and pw='".$pw."' limit 1";
	$result=mysqli_query($link,$query);//如果账号及密码正确返回一行
	if(mysqli_num_rows($result)>0){//行数大于0说明账号及密码正确
	$query="UPDATE user SET imei='".$imei."', brand='".$brand."' load_time='".$datetime."', load_loca='".$load_loca."' WHERE ".$idortel."='".$account."' limit 1";
	if(mysqli_query($link,$query)){//如果更新数据成功
	$row = mysqli_fetch_assoc($result);//以一个关联数组抓取一行结果
	$json='{"result":"ok","id":"'.$row["id"].'","tel":"'.$row["tel"].'","imei":"'.$row["imei"].'","brand":"'.$row["brand"].'","name":"'.$row["name"].'","sex":"'.$row["sex"].'","money":"'.$row["money"].'","mood":"'.$row["mood"].'"}';
	}else{//如果更新数据失败
	$json='{"result":"account and password ok,but update data failed"}';
	}
	}else{
	$json='{"result":"账号或密码错误"}';
	}

break;

case 'change_pw'://更改密码//找回密码
	$tel= $_POST['tel'];$pw=$_POST['pw'];
	$query= "SELECT * FROM user where tel='".$tel."' limit 1";
	$result=mysqli_query($link,$query);//正确返回一行
	if(mysqli_num_rows($result)>0){//行数大于0说明正确
	$query="UPDATE user SET pw='".$pw."' WHERE tel='".$tel."' limit 1";
	if(mysqli_query($link,$query)){//如果更新数据成功
	$json='{"result":"ok","pw":"'.$pw.'"}';
	}else{//如果更新数据失败
	$json='{"result":"tel ok,but change password failed"}';
	}
	}else{
	$json='{"result":"不存在此账号"}';
	}
break;
	
default:
$json='{"result":"非法请求"}';
}

 

 
echo $json;//输出json数据
mysqli_close($link);//关闭sql
?> 