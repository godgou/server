<?php
/*注意：json非数值数据的键和值必须用双引号*/

/*设置MySQL自动增长从某个指定的数开始 
use android_app;
alter table user AUTO_INCREMENT=1;
*/
/*富豪榜
select id,money, (SELECT count(distinct money) from user where money>t1.money)+1 as rank from user t1
*/
/*
SET @rank:=0,@preScore:=0 ;
UPDATE user,
(SELECT id, (IF( @preScore<>money,@rank:=@rank+1,@rank))money_pm, @preScore:=money FROM user ORDER BY money DESC )temp_user
SET user.money_pm =temp_user.money_pm WHERE user.id=temp_user.id;
*/
date_default_timezone_set('PRC');//把时间调到北京时间,php5默认为格林威治标准时间
header("Access-Control-Allow-Origin: *");//告诉接收数据的对象此页面允许任何域的访问
header('Content-type:text/json');//告诉接收数据的对象此页面输出的是json数据；

$type = $_POST['type'];//请求类型

$json="失败";

$datetime=date("Y-m-d H:i:s",time());//服务器当前时间

$sqlhost = 'localhost';$sqlname = 'root';$sqlpw = 'luqihongmysql';$sqldb = 'android_app';//sql凭证

$query='';//sql语句

$link = mysqli_connect($sqlhost, $sqlname, $sqlpw, $sqldb);// 面向过程创建sql连接
if (!$link) {// 检测sql连接
$json='{"result":"'.mysqli_connect_error().'"}';
    die($json);
}
//echo $json;//'连接成功

switch($type){
	case 'money_pm'://富豪榜
    $query= "SELECT id,(SELECT count(distinct money) from user where money>t1.money)+1 as rank from user t1";//虚拟一个排名表t1
	$result=mysqli_query($link,$query);$line=mysqli_num_rows($result);$x=0;
	//for ($i=1; $i<=$line; $i++) {
  //$query="UPDATE user SET money_pm='".$row['rank']." WHERE id='".$i."' limit 1";
//}
while($row = mysqli_fetch_array($result)){//通过循环读取数据内容
$query="UPDATE user SET money_pm='".$row['rank']."' WHERE id='".$row['id']."' limit 1";
mysqli_query($link,$query);
$x+=1;
}
$json='{"result":"更新'.$x.'条数据"}';
break;

default:
$json='{"result":"非法请求"}';
}

 

 
echo $json;//输出json数据
mysqli_close($link);//关闭sql
?> 