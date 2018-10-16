<?php
session_start();

$openid=$_SESSION['openid'];
$score=$_POST['score'];

$link=mysqli_connect(SAE_MYSQL_HOST_M, SAE_MYSQL_USER, SAE_MYSQL_PASS, SAE_MYSQL_DB, SAE_MYSQL_PORT);
if(mysqli_connect_errno($link)){
	var_dump(mysqli_connect_error($link));
}

$sql="SELECT score FROM personinfo WHERE openid='{$openid}'";
$res=mysqli_query($link, $sql);
$one=mysqli_fetch_assoc($res);
if(!$one['score']||$one['score']<$score){
	$sql="UPDATE personinfo SET score={$score} WHERE openid='{$openid}'";
	
	$res=mysqli_query($link, $sql);
	if($res&&mysqli_affected_rows($link)){
        $data['state']="success";
		$data['score']=$score;
	}else{
        
		$data['state']='false';
	}
}else{
    $data['state']="success";
	$data['score']=$one['score'];
}

$sql="select * from (select *,@score_rank := @score_rank + 1 As rank from personinfo a, ( select @score_rank := 0 ) b order By score desc) AS c where openid='{$openid}'";
$res=mysqli_query($link, $sql);
$one=mysqli_fetch_assoc($res);

$data['rank']=$one['rank'];

echo json_encode($data);
?>