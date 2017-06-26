<?php
/*注意：json非数值数据的键和值必须用双引号*/

/*设置MySQL自动增长从某个指定的数开始 
use android_app;
alter table user AUTO_INCREMENT=1;
*/
date_default_timezone_set('PRC');//把时间调到北京时间,php5默认为格林威治标准时间
header("Access-Control-Allow-Origin: *");//告诉接收数据的对象此页面允许任何域的访问
header('Content-type:text/json');//告诉接收数据的对象此页面输出的是json数据；

$type = $_POST['type'];//请求类型
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

$query="";//sql语句

$json='{"ok":"200"}';$echo_info_1='';$echo_info_2='';$echo_info_3='';$echo_info_4='';//输出信息变量

//echo $json;//如果提交的请求数据无误则输出200 ok，否则请求失败

$link = mysqli_connect($sqlhost, $sqlname, $sqlpw, $sqldb);// 面向过程创建sql连接
if (!$link) {// 检测sql连接
$json='{"连接失败":"'.mysqli_connect_error().'"}';
    die($json);
}
//echo '连接成功:' . mysqli_get_host_info($link);

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

case 'loading_id'://用id来登陆
    $id=$_POST['id'];$pw=$_POST['pw'];$load_loca=$_POST['load_loca'];
    $query="SELECT * FROM user WHERE id=".$id." and pw='".$pw."' limit 1";
    $result=mysqli_query($link,$query);//返回一行
	if(mysqli_num_rows($result)>0){//如果id或密码正确
	$query="UPDATE user SET load_time='".$datetime."', load_loca='".$load_loca."' WHERE id=".$id." limit 1";
	if(mysqli_query($link,$query)){//如果更新数据成功
	$row = mysqli_fetch_assoc($result);//以一个关联数组抓取一行结果
	$json='{"result":"ok","imei":"'.$row["imei"].'","name":"'.$row["name"].'","sex":"'.$row["sex"].'","money":"'.$row["money"].'","mood":"'.$row["mood"].'"}';
	}else{//如果获取数据失败
	$json='{"result":"id and password ok,but update data failed"}';
	}
	}else{//如果id或密码不正确
	$json='{"result":"id or password error"}';
	}
	
mysqli_free_result($result);// 释放结果集
break;

/*case 'loading_tel'://用手机号来登陆
    $tel=$_POST['tel'];$pw=$_POST['pw'];$load_loca=$_POST['load_loca'];
    $query="SELECT * FROM user WHERE tel=".$tel." and pw='".$pw."' limit 1";
	$result=mysqli_query($link,$query);//如果tel及密码正确返回一行
	if(mysqli_num_rows($result)>0){//行数大于0说明tel及密码正确
	$query="UPDATE user SET load_time='".$datetime."', load_loca='".$load_loca."' WHERE id=".$id." limit 1";
	if(mysqli_query($link,$query)){//如果更新数据成功
	$row = mysqli_fetch_assoc($result);//以一个关联数组抓取一行结果
	$json='{"result":"ok","id":"'.$row["id"].'","name":"'.$row["name"].'","sex":"'.$row["sex"].'","money":"'.$row["money"].'","mood":"'.$row["mood"].'"}';
	}else{//如果更新数据失败
	$json='{"result":"tel and password ok,but update data failed"}';
	}
	}else{
	$json='{"result":"tel or password error"}';
	}
	
mysqli_free_result($result);// 释放结果集
break;*/
case 'change_pw';//更改密码//找回密码
	$id = $_POST['id'];$s_key=$_POST['s_key'];$s_val=$_POST['s_val'];$npw=$_POST['npw'];
	$query= "SELECT id FROM user where id=".$id." and s_key=".$s_key." and s_val='".$s_val."' limit 1";
	$result=mysqli_query($link,$query);//正确返回一行
	if(mysqli_num_rows($result)>0){//行数大于0说明正确
	$query="UPDATE user SET pw='".$npw."' WHERE id=".$id." limit 1";
	if(mysqli_query($link,$query)){//如果更新数据成功
	$json='{"result":"密码更改成功!请牢记您的新密码:'.$npw.'"}';
	}else{//如果更新数据失败
	$json='{"result":"tel and 密保 ok,but change password failed"}';
	}
	}else{
	$json='{"result":"id或密保不正确"}';
	}
break;
case 'change_s';//更改密保
	$id = $_POST['id'];$pw = $_POST['pw'];$s_key=$_POST['s_key'];$s_val=$_POST['s_val'];$ns_key=$_POST['ns_key'];$ns_val=$_POST['ns_val'];
	$query= "SELECT id FROM user where id=".$id." and s_key=".$s_key." and s_val='".$s_val."' and pw='".$pw."' limit 1";
	$result=mysqli_query($link,$query);//正确返回一行
	if(mysqli_num_rows($result)>0){//行数大于0说明正确
	$query="UPDATE user SET s_key=".$ns_key.", s_val='".$ns_val."' WHERE id=".$id." limit 1";
	if(mysqli_query($link,$query)){//如果更新数据成功
	$json='{"result":"密保更改成功!请牢记您的新密保答案:'.$ns_val.'"}';
	}else{//如果更新数据失败
	$json='{"result":"tel and密保and密码 ok,but change 密保 failed"}';
	}
	}else{
	$json='{"result":"手机号码or密保or密码不正确"}';
	}
break;
case 'change_imei';//改绑手机
	$id = $_POST['id'];$pw = $_POST['pw'];$s_key=$_POST['s_key'];$s_val=$_POST['s_val'];$n_imei = $_POST['n_imei'];
	$query= "SELECT imei FROM user where imei='".$n_imei."' limit 1";//sql语句：如果查询到一条匹配数据就不再往下查询
	if(mysqli_num_rows(mysqli_query($link,$query))>0){//如果此手机已存在
		$json='{"result":"此手机已注册,请用其他手机或改绑此手机后重试"}';
	}else{//如果手机不存在
	$query= "SELECT imei FROM user where id=".$id." and s_key=".$s_key." and s_val='".$s_val."' limit 1";
	$result=mysqli_query($link,$query);//正确返回一行
	if(mysqli_num_rows($result)>0){//行数大于0说明正确
	$row = mysqli_fetch_assoc($result);//以一个关联数组抓取imei结果
	$query="UPDATE user SET imei=".$n_imei." WHERE id=".$id." limit 1";
	if(mysqli_query($link,$query)){//如果更新数据成功
	$json='{"result":"手机更改成功!您绑定的旧手机:'.$row["imei"].'已更改为:'.$n_imei.'"}';
	}else{//如果更新数据失败
	$json='{"result":"tel and密保and id and password ok,but change tel failed"}';
	}
	}else{
	$json='{"result":"id or password or密保不正确"}';
	}
	}
break;
	
default:
$json='{"result":"type类型不存在"}';
}

 

 
echo $json;//输出json数据
mysqli_close($link);//关闭sql
?> 