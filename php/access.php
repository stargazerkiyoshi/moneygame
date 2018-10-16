<?php
include "common.php";
session_start();
$code=$_GET['code'];
$appid="wxb64b4b379d9efb78";
$appsecret="5026d6f05e4baf8cce2fe7cce27f2bb6";

$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$appsecret}&code={$code}&grant_type=authorization_code";

$res=httpGet($url);

$access_token=json_decode($res,true)['access_token'];
$openid=json_decode($res,true)['openid'];

$url="https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN";

$userinfo=httpGet($url);

$userinfoArr=json_decode($userinfo,true);
$openid=$userinfoArr['openid'];
$nickname=$userinfoArr['nickname'];
$headimgurl=$userinfoArr['headimgurl'];
$city=$userinfoArr['city'];
$sex=$userinfoArr['sex'];


$link=mysqli_connect(SAE_MYSQL_HOST_M, SAE_MYSQL_USER, SAE_MYSQL_PASS, SAE_MYSQL_DB, SAE_MYSQL_PORT);
if(mysqli_connect_errno($link)){
	var_dump(mysqli_connect_error($link));
}

$sql="SELECT * FROM users WHERE openid='{$openid}'";

$res=mysqli_query($link, $sql);

$one=mysqli_fetch_assoc($res);

if(!$one){
	$sql="INSERT INTO users(openid,nickname,headimgurl,city,sex) VALUES('{$openid}','{$nickname}','{$headimgurl}','{$city}',{$sex})";
	$res=mysqli_query($link, $sql);
	if($res&&mysqli_insert_id($link)){
		//添加成功
//		echo "<script>window.location.href='../index.html'</script>";
		$_SESSION['openid']=$openid;
		echo "<script>window.location.href='../index.php'</script>";
		
	}else{
		echo "添加失败";
	}
}else{
	//已经有了
//  echo "<script>window.location.href='../index.html'</script>";
	$_SESSION['openid']=$openid;
	echo "<script>window.location.href='../index.php'</script>";
	
}

?>