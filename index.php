<?php
	session_start();
	$openid=$_SESSION['openid'];
	$link=mysqli_connect(SAE_MYSQL_HOST_M, SAE_MYSQL_USER, SAE_MYSQL_PASS, SAE_MYSQL_DB, SAE_MYSQL_PORT);
	if(mysqli_connect_errno($link)){
		var_dump(mysqli_connect_error($link));
	}
	$sql="SELECT * FROM personinfo WHERE openid='{$openid}'";
	$res=mysqli_query($link, $sql);
	$one=mysqli_fetch_assoc($res);
	if($one){
		$canstart=1;
	}else{
		$canstart=0;
	}
	
	
	require_once "php/jssdk.php";
	$appid="wxb64b4b379d9efb78";
	$appsecret="5026d6f05e4baf8cce2fe7cce27f2bb6";
	$links=mysqli_connect(SAE_MYSQL_HOST_M, SAE_MYSQL_USER, SAE_MYSQL_PASS, SAE_MYSQL_DB, SAE_MYSQL_PORT);
	$jssdk = new JSSDK($appid, $appsecret,$links);
	$signPackage = $jssdk->GetSignPackage();
	
//	$sql="SELECT * FROM personinfo ORDER BY score DESC";
	$sql="SELECT u.nickname,u.headimgurl,p.score FROM users AS u JOIN personinfo AS p ON u.openid=p.openid ORDER BY p.score DESC";
	$res=mysqli_query($link, $sql);
	while($arr=mysqli_fetch_assoc($res)){
	    $users[]=$arr;
	}
?>
 <!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title></title>
		<script src="http://g.tbcdn.cn/mtb/lib-flexible/0.3.4/??flexible_css.js,flexible.js"></script>
		<script src="js/swiper.js" type="text/javascript" charset="utf-8"></script>
		<link rel="stylesheet" type="text/css" href="css/animate.css"/>
		<link rel="stylesheet" type="text/css" href="css/swiper.css"/>
		<link rel="stylesheet" type="text/css" href="css/index.css"/>
	</head>
	<body>
		<div class="page1">
			<img id="tiaozhan" src="img/tiaozhan.png" alt="" />
			<img id="shuqian" src="img/shuqian.png" alt="" />
			<img id="start" src="img/start.png" alt="" />
			<div class="btn btn1">
				<img src="img/text1.png"/>
			</div>
			<div class="btn btn2">
				<img src="img/text2.png"/>
			</div>
			<div class="btn btn3">
				<img src="img/text3.png"/>
			</div>
			<div class="btn btn4">
				<img src="img/text4.png"/>
			</div>
			<div id="paihang">
				<div class="close">
					X
				</div>
                <div class="paihang">
				<?php foreach($users as $k=>$v){?>
				<div class="item">
					<span><?php echo $k+1;?></span>
                    <img src=<?php echo $v['headimgurl']?> />
					<span><?php echo $v['nickname'];?></span>
					<span><?php echo $v['score']?>分</span>
				</div>
                </div>
				<?php }?>
			</div>
			<div id="guize">
				<div class="close">
					X
				</div>
			</div>
			<div id="jiangpin">
				<div class="close">
					X
				</div>
			</div>
			<div id="shuoming">
				<div class="close">
					X
				</div>
			</div>
			<div id="biaodan">
				<div class="close">
					X
				</div>
				<form action="php/record.php" method="post">
					<input class="forminput" type="text" name="name" id="name" placeholder="姓名" />
					<input class="forminput" type="text" name="tel" id="tel" placeholder="电话" />
					<input class="forminput" type="text" name="addr" id="addr" placeholder="地址" />
					<input id="submit" type="submit" value="提交并开始游戏"/>
				</form>
			</div>
		</div>
	
		<div class="page2">
			<div class="swiper-container">
			    <div class="swiper-wrapper">
				    <div class="swiper-slide">
				    	<img src="img/lunbo.png" alt="" />
				    </div>
				    <div class="swiper-slide">
				    	<img src="img/lunbo1.png"/>
				    </div>
				    <!--<div class="swiper-slide">slider3</div>-->
			    </div>
			</div>
			<!--<img class="maoyeye" src="img/maoyeye.png"/>-->
			
			<span id="score_bai">
				0
			</span>
			<span id="score_shi">
				0
			</span>
			<span id="score_ge">
				0
			</span>
			<span id="time">
				10
			</span>
			<img id="shou" src="img/shou.png"/>
			<img id="shitou" src="img/shitou.png"/>
		</div>
		<div class="page3">
			<span id="scoretext">
				￥888
			</span>
			<div id="scoreinfo">
				我的辉煌战绩:￥<span id="maxscore">999</span>  当前排名:<span id="rank">62</span>位
			</div>
			<div class="circlebtn" id="againbtn">
				再来一次
			</div>
			<div class="circlebtn" id="sharebtn">
				分享朋友圈
			</div>
            
		</div>
	</body>
	<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
	<script src="http://res.wx.qq.com/open/js/jweixin-1.4.0.js"></script>
	<script type="text/javascript">
		wx.config({
		    //debug: true,//默认调试弹框有关
		    appId: '<?php echo $signPackage["appId"];?>',
		    timestamp: <?php echo $signPackage["timestamp"];?>,
		    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
		    signature: '<?php echo $signPackage["signature"];?>',
		    jsApiList: [
		      // 所有要调用的 API 都要加到这个列表中
		      "onMenuShareTimeline",
		      "onMenuShareAppMessage",
		      "updateTimelineShareData"
		    ]
		});
		wx.ready(function () {
		    wx.onMenuShareTimeline({
			    title: '一起来数钱', // 分享标题
			    link: 'http://stargazer.applinzi.com/moneygame/guide.html', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
			    imgUrl: 'img/maoyeye.png', // 分享图标
			    success: function () {
			    // 用户点击了分享后执行的回调函数
			    	alert("分享成功");
				}
			});
			wx.onMenuShareAppMessage({
				title: '一起来数钱', // 分享标题
				desc: '数钱数到手抽筋', // 分享描述
				link: 'http://stargazer.applinzi.com/moneygame/guide.html', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
				imgUrl: 'img/maoyeye.png', // 分享图标
				type: '', // 分享类型,music、video或link，不填默认为link
				dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
				success: function () {
				// 用户点击了分享后执行的回调函数
					alert("分享给朋友成功");
				}
			});
           
		});
		var btns=document.getElementsByClassName("btn");
		var paihang=document.getElementById("paihang")
		var guize=document.getElementById("guize");
		var jiangpin=document.getElementById("jiangpin");
		var shuoming=document.getElementById("shuoming");
		var biaodan=document.getElementById("biaodan");
		var closes=document.getElementsByClassName("close");
		var startBtn=document.getElementById("start");
//		var maoyeye=document.getElementByClassName("maoyeye");
		var shou=document.getElementById("shou");
		var page1=document.getElementsByClassName("page1")[0];
		var page2=document.getElementsByClassName("page2")[0];
		var page3=document.getElementsByClassName('page3')[0];
		var aniID;
		var lastshow=paihang;
		var scoreBai=document.getElementById("score_bai");
		var scoreShi=document.getElementById("score_shi");
		var scoreGe=document.getElementById("score_ge");
		var time=document.getElementById("time");
		
		var scoreText=document.getElementById("scoretext");
		var maxScore=document.getElementById("maxscore");
		var againBtn=document.getElementById('againbtn');
		var shareBtn=document.getElementById("sharebtn");
		var rank=document.getElementById("rank");
        
		var score=0;
		var start=false;
		var end=false;
		var timer;
		var mySwiper;
		startBtn.onclick=function(){
			var canStart='<?php echo $canstart?>';
			if(canStart=='1'){
				page1.style.display="none";
				page2.style.display="block";
	
				mySwiper = new Swiper('.swiper-container', {
					direction:"vertical",
					loop:true,
					autoplay: true,//可选选项，自动滑动
		
				})   //轮播图
				game();
			}else{
				biaodan.style.display="block";
			}
			
		}
		function Money(){
			this.img=new Image();
			this.img.src="img/maoyeye.png";
			this.img.className="maoyeye";
			this.isSliding=false;
			this.speed=50;
		}
		Money.prototype.show=function(dom){
			page2.insertBefore(this.img,dom);
		}
		Money.prototype.slide=function(){
			this.img.style.top=this.img.offsetTop-this.speed+"px";
			this.img.style.width=this.img.offsetWidth-10+"px";
			this.img.style.height=this.img.offsetHeight-5+"px";
		}
		
		
		btns[0].onclick=function(){

			lastshow.style.display="none";
			lastshow=paihang;
		
			paihang.style.display="block";
			
		}
		btns[1].onclick=function(){
			
			lastshow.style.display="none";
			lastshow=guize;
			guize.style.display="block";
			
		}
		btns[2].onclick=function(){
			
			lastshow.style.display="none";
			lastshow=jiangpin;
			jiangpin.style.display="block";
		
		}
		btns[3].onclick=function(){
			lastshow.style.display="none";
			lastshow=shuoming;
			shuoming.style.display="block";
		}
		
		closes[0].onclick=function(){
			paihang.style.display="none";
		}
		closes[1].onclick=function(){
			guize.style.display="none";
		}
		closes[2].onclick=function(){
			jiangpin.style.display="none";
		}
		closes[3].onclick=function(){
			shuoming.style.display="none";	
		}
		closes[4].onclick=function(){
			biaodan.style.display="none";
		}
		
		againBtn.onclick=function(){
			page3.style.display="none";
			page2.style.display="block";
			game();
		}
        
		shareBtn.onclick=function(){
			 wx.updateTimelineShareData({
			    title: '一起来数钱', // 分享标题
			    link: 'http://stargazer.applinzi.com/moneygame/guide.html', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
			    imgUrl: 'img/maoyeye.png', // 分享图标
			   
			},function () {
			    // 用户点击了分享后执行的回调函数
			    	alert("分享成功");
			});
		}
		
		function game(){
			var score=0;
			var start=false;
			var end=false;
            
			var newMoney=new Money();
			newMoney.show(shou);
            time.innerText=10;
			scoreBai.innerText=0;
			scoreShi.innerText=0;
			scoreGe.innerText=0;
            
			addEvent(newMoney);
			function addEvent(money){
				money.img.addEventListener('touchstart',function(event){
					if(!end){
						var relativePosX=event.targetTouches[0].clientX-money.img.offsetLeft;
						var relativePosY=event.targetTouches[0].clientY-money.img.offsetTop;
						shou.style.display="none";
						
						money.img.addEventListener('touchmove', function(event) {
							
					     // 如果这个元素的位置内只有一个手指的话
						    if (event.targetTouches.length == 1) {
						　　　　	event.preventDefault();// 阻止浏览器默认事件，重要 
						        var touch = event.targetTouches[0];
						        // 把元素放在手指所在的位置
						        money.img.style.top = touch.clientY-relativePosY + 'px';
						        if(money.img.offsetTop<650&&!money.isSliding){
						        	score+=1;
						        	showScore(score);
						        	money.isSliding=true;
						        	requestAnimationFrame(slideOut);
						        }
					        }
						}, false);
						
						if(!start){
							start=true;
							timer=setInterval(function(){
								
								if(time.innerText==0){
									clearInterval(timer);
									alert("游戏结束");
									end=true;
									page2.style.display="none";
									page3.style.display="block";
									scoreText.innerText="￥"+score;
									$.ajax({
										type:"post",
										url:"php/recordscore.php",
										async:true,
                                        dataType:"json",
										data:{
											score
										},
										success:function(res){
                                            
                                            //console.log(res);
											maxScore.innerText=res.score;
                                            rank.innerText=res.rank;
										}
									});
									return;
								}
								time.innerText-=1;
							},1000);
						}
						var money1=new Money();
						money1.show(money.img);				
						addEvent(money1);
					}
				});
				
				
				function slideOut(){
					money.slide();
					aniID=requestAnimationFrame(slideOut);
					if(money.img.offsetTop<=-1000){
		        		cancelAnimationFrame(aniID);
		        		money.isSliding=false;
		        	}
				}
			}
		}
		function showScore(score){
			scoreGe.innerText=score%10;
			scoreShi.innerText=(score-score%10)/10%10;
			scoreBai.innerText=parseInt(score/100);
		}
		
	</script>
</html>
