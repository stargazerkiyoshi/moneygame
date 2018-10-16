<?php
$link=mysqli_connect(SAE_MYSQL_HOST_M, SAE_MYSQL_USER, SAE_MYSQL_PASS, SAE_MYSQL_DB, SAE_MYSQL_PORT);
if(mysqli_connect_errno($link)){
	var_dump(mysqli_connect_error($link));
}

$sql="select * from (select *,@score_rank := @score_rank + 1 As rank from personinfo a, ( select @score_rank := 0 ) b order By score desc) AS c where openid='oKrKc5olWk0AaaQn68WprqPW49lg'";

$res=mysqli_query($link, $sql);
$one=mysqli_fetch_assoc($res);
print_r($one);
echo $one['rank']
?>