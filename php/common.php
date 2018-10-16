<?php
//1.初始化curl
//2.配置curl；（比较复杂）；
//3.执行curl
//4.关闭curl
function httpGet($url) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_TIMEOUT, 500);
	
	//检查证书
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);              // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);              // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); 
	
	// 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
	// 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
	// curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
	// curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
	curl_setopt($curl, CURLOPT_URL, $url);

	$res = curl_exec($curl);
	curl_close($curl);

	return $res;
}

//php里面请求post接口的函数
function httpPost($data, $url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$tmpInfo = curl_exec($ch);
	if (curl_errno($ch)) {
		return curl_error($ch);
	}
	curl_close($ch);
	return $tmpInfo;
}

$link=mysqli_connect(SAE_MYSQL_HOST_M, SAE_MYSQL_USER, SAE_MYSQL_PASS, SAE_MYSQL_DB, SAE_MYSQL_PORT);
if(mysqli_connect_errno($link)){
	var_dump(mysqli_connect_error($link));
}
function getAccessToken($link,$appid,$appsecret){
	//判断token是否存在；规定id为1的数据存放token
	$sql="SELECT * FROM token WHERE id=1";
	$result=mysqli_query($link, $sql);
	$one=mysqli_fetch_assoc($result);
	
	$url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}";
	
	if($one&& mysqli_affected_rows($link)){
		//查询到---存在token
		//判断token是否过期
		if(time()>$one['passtime']){
			//过期了
			$res=httpGet($url);
			$token=json_decode($res,true)['access_token'];
			//过期时间
			$passtime=time()+7000;
			$sql="UPDATE token SET datainfo='{$token}',passtime={$passtime} WHERE id=1";
			$res=mysqli_query($link, $sql);
			if($res&&mysqli_affected_rows($link)){
				return $token;
			}else{
				echo "更新失败";
			}
		}else{
			//没有过期
			return $one['datainfo'];
		}
	}else{
		//未查询到---不存在token；获取token；
		
		$res=httpGet($url);
		$token=json_decode($res,true)['access_token'];
		//过期时间
		$passtime=time()+7000;
		$sql="INSERT INTO token (id,datainfo,passtime) VALUES (1,'$token',$passtime)";
		$result=mysqli_query($link, $sql);
		if($result&&mysqli_insert_id($link)){
			return $token;
		}else{
			echo "添加失败";
		}
	}
}
?>