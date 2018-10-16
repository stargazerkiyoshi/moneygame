<?php
session_start();
$name=$_POST['name'];
$tel=$_POST['tel'];
$addr=$_POST['addr'];
$openid=$_SESSION['openid'];
$link=mysqli_connect(SAE_MYSQL_HOST_M, SAE_MYSQL_USER, SAE_MYSQL_PASS, SAE_MYSQL_DB, SAE_MYSQL_PORT);
if(mysqli_connect_errno($link)){
	var_dump(mysqli_connect_error($link));
}

$sql="INSERT INTO personinfo(name,tel,addr,openid) VALUES('{$name}','{$tel}','{$addr}','{$openid}')";

$res=mysqli_query($link, $sql);

if($res&&mysqli_affected_rows($link)){
	echo "<script>window.location.href='../index.php'</script>";
}else{
	echo "添加失败";
}
?>