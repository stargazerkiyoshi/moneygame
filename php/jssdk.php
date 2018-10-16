<?php
class JSSDK {
  private $appId;
  private $appSecret;

  public function __construct($appId, $appSecret,$links) {
    $this->appId = $appId;
    $this->appSecret = $appSecret;
		$this->links=$links;
  }

  public function getSignPackage() {
    $jsapiTicket = $this->getJsApiTicket();

    // 注意 URL 一定要动态获取，不能 hardcode.
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $timestamp = time();
    $nonceStr = $this->createNonceStr();

    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

    $signature = sha1($string);

    $signPackage = array(
      "appId"     => $this->appId,
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string
    );
    return $signPackage; 
  }

  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

private function getJsApiTicket() {
   
     $accessToken=$this->getAccessToken();
    $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
    //$ticket=$this->httpGet($url);
		//echo $ticket;
    //给ticket做本地缓存
    //
    //判断ticket是否存在；规定id为2的数据存放ticket
		$sql="SELECT * FROM token WHERE id=2";
		$result=mysqli_query($this->links, $sql);
		$one=mysqli_fetch_assoc($result);

		if($one&& mysqli_affected_rows($this->links)){
			//查询到---存在ticket
			//判断ticket是否过期
			if(time()>$one['passtime']){
				//过期了
				$res=$this->httpGet($url);
				$ticket=json_decode($res,true)['ticket'];
				//过期时间
				$passtime=time()+7000;
				$sql="UPDATE token SET datainfo='{$ticket}',passtime={$passtime} WHERE id=2";
				$res=mysqli_query($this->links, $sql);
				if($res&&mysqli_affected_rows($this->links)){
					return $ticket;
				}else{
					echo "更新失败";
				}
			}else{
				//没有过期
				return $one['datainfo'];
			}
		}else{
			//未查询到---不存在ticket；获取ticket；
			
			$res=$this->httpGet($url);
			$ticket=json_decode($res,true)['ticket'];
			//过期时间
			$passtime=time()+7000;
			$sql="INSERT INTO token (id,datainfo,passtime) VALUES (2,'$ticket',$passtime)";
			$result=mysqli_query($this->links, $sql);
			if($result&&mysqli_insert_id($this->links)){
				return $ticket;
			}else{
				echo "添加失败";
			}
		}
  }

	function getAccessToken(){
    
		//判断token是否存在；规定id为1的数据存放token
		$sql="SELECT * FROM token WHERE id=1";
		$result=mysqli_query($this->links, $sql);
		$one=mysqli_fetch_assoc($result);
		
		$url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appId}&secret={$this->appSecret}";
		
		if($one&& mysqli_affected_rows($this->links)){
			//查询到---存在token
			//判断token是否过期
			if(time()>$one['passtime']){
				//过期了
				$res=$this->httpGet($url);
				$token=json_decode($res,true)['access_token'];
				//过期时间
				$passtime=time()+7000;
				$sql="UPDATE token SET datainfo='{$token}',passtime={$passtime} WHERE id=1";
				$res=mysqli_query($this->links, $sql);
				if($res&&mysqli_affected_rows($this->links)){
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
			
			$res=$this->httpGet($url);
			$token=json_decode($res,true)['access_token'];
			//过期时间
			$passtime=time()+7000;
			$sql="INSERT INTO token (id,datainfo,passtime) VALUES (1,'$token',$passtime)";
			$result=mysqli_query($this->links, $sql);
			if($result&&mysqli_insert_id($this->links)){
				return $token;
			}else{
				echo "添加失败";
			}
		}
	}

  private function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
    // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
  }
}

?>