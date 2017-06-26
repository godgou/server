<?php
//php函数不能直接访问外部变量
header("Access-Control-Allow-Origin: *");//告诉接收数据的对象此页面允许任何域的访问
	date_default_timezone_set('Asia/Shanghai');
	
$time=$_POST["confirm"];//confirm的值为13位时间戳加上年份，如“14973213642902017”=“1497321364290”+“2017”
$ggtel=$_POST["ggtel"];
$ggcode=$_POST["ggcode"];
$Y=substr($time,13,4);
$time=substr($time,0,13);
if(date("Y",$time/1000)==$Y){//验证是否合法
sendSMS($ggtel,$ggcode);
}else{
	echo "godgou.com";
}

    function sendSMS ($ggtel,$ggcode)//$ggtel为手机号，$ggcode是要发送的验证码
{   
    include "TopSdk.php";
    $c = new TopClient;
    $c->appkey = "24260607";
    $c->secretKey = "bbc25e66d16cdfada488a10f619951ab";
	$c->format = "json";
    $req = new AlibabaAliqinFcSmsNumSendRequest;
	//$req->setExtend("123456"); //这个是用户名记录那个用户操作
    $req->setSmsType("normal");//这个不用改你短信的话就默认这个就好了
    $req->setSmsFreeSignName("神狗");//签名
    $req->setSmsParam("{number:'".$ggcode."'}");//短信模板变量,key的名字须和申请模板中的变量名一致 ,\"product\":\"神狗\"
    $req->setRecNum($ggtel); //这个是写手机号码
    $req->setSmsTemplateCode("SMS_70515507");//这个是模版ID 主要也是短信内容
    $resp = $c->execute($req);// 返回结果
    //echo $resp;
    //var_dump($resp);
    if($resp->result->success)
    {
        echo $ggtel;
    }
    else
    {
        return "godgou.com";
    }
}

?>