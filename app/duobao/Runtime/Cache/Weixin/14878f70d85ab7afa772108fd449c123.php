<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>登录</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="/Public/Weixin/css/common.css">
	<link rel="stylesheet" href="/Public/Weixin/css/homePage.css">
	<link rel="stylesheet" href="/Public/Weixin/css/homePage2.css">
	<script src="http://lib.sinaapp.com/js/jquery/1.9.1/jquery-1.9.1.min.js"></script>
</head>
<body>
<div class="alert">
	<div class="content">
		<!--<div class="chead">手机注册</div>-->
		<form role="form" action="/Weixin/User/register" method="post" id="formUser">
			<input type="hidden" name="">
			<div class="main">
				<div class="tel">
					<input name="mobile" id="mobile" onblur="ismobile_registered();"  placeholder="手机号">
					<img src="/Public/Weixin/images/phone_03.jpg" alt="" class="phone">
					<span class="check_tips error_tip" id="dmobile"></span>

				</div>
				<div class="message">
					<input type="text" name="miss" id="miss" onblur="checkphone();" placeholder="验证码">
					<img src="/Public/Weixin/images/phone2_03.jpg" alt="" class="mm">
					<span class="check_tips error_tip" id="dmiss" style="right: 2.5rem;"></span>
					<p class="yzm"  onclick="sendphone();">
						<span id="sendma" name="sendma">获取验证码</span>
					</p>
				</div>
				<div id="confirm" onclick="register();">
					确定
				</div>
			</div>
		</form>
	</div>
</div>

<script type="text/javascript" src="/Public/Weixin/js/zepto.min.js"></script>
<script type="text/javascript">
	$(function() {
		var mo=function(e){e.preventDefault();};
		function stop(){
			document.body.style.overflow='hidden';
			document.addEventListener("touchmove",mo,false);//禁止页面滑动
		}
		function move(){
			document.body.style.overflow='';//出现滚动条
			document.removeEventListener("touchmove",mo,false);
		}

	})

	var secs = 60;
	function backcount(){
		$(".yzm").attr("onClick",'');
		for(i=1;i<=secs;i++) {
			window.setTimeout("update1(" + i + ")", i * 1000);
		}
	}
	function update1(num) {
		if(num == secs) {
			$("#sendma").html('获取验证码');
			$(".yzm").attr("onClick",'sendphone();');
		}else {
			printnr = secs-num;
			$("#sendma").html("(" + printnr +")秒后重发");
		}
	}
	function err_msg(str, id) {
		document.getElementById('d'+id).style.display = "inline-block";
		document.getElementById('d'+id).innerHTML=str;
	}

	function ismobile_registered(){
		var mobile=$("#mobile").val();
		if(mobile ==''){
			err_msg('请填写手机号码', 'mobile');
		}else{
			if(!(/^1[34578]\d{9}$/.test(mobile))){
				err_msg('不正确的手机号码', 'mobile');
			}else{
				err_msg('', 'mobile');
//				$.ajax({
//					type:'post', //传送的方式,get/post
//					url:'<?php echo U("User/ismobile_registered");?>', //发送数据的地址
//					data:{mobile:mobile},
//					dataType: "json",
//					success:function(data)
//					{
//						err_msg(data.info, 'mobile');
//					}
//				})
			}
		}
	}


	function sendphone(){
		var phone=$("#mobile").val();
		if(!(/^1[34578]\d{9}$/.test(phone))){
			err_msg('不正确的手机号码', 'mobile');
			return false;
		}
		if($('#dmobile').html()=="手机号已经被使用" || $('#dmobile').html()=="短信已发送") {
			return false;
		}
		$.ajax({
			type:'post', //传送的方式,get/post
			url:'<?php echo U("User/sendphone");?>', //发送数据的地址
			data:{phone:phone},
			dataType: "json",
			success:function(data){
				err_msg(data.info, 'mobile');
			}
		})
		backcount();
	}

	function checkphone(){
		var miss=$("#miss").val();
		if(miss ==''){
			err_msg('请填写验证码', 'miss');
		}else{
			$.ajax({
				type:'post', //传送的方式,get/post
				url:'<?php echo U("User/checkphone");?>', //发送数据的地址
				data:{miss:miss},
				dataType: "json",
				success:function(data)
				{
					err_msg(data.info, 'miss');
				}
			})
		}
	}

	function register(){
		var mobile=$("#mobile").val();
		if(mobile ==''){
			err_msg('请填写手机号码', 'mobile');
			return false;
		}
		if($('#dmobile').html() != "" && $('#dmobile').html() != "短信已发送") {
			return false;
		}
		var miss=$("#miss").val();
		if(miss ==''){
			err_msg('请填写验证码', 'miss');
			return false;
		}
		if($('#dmiss').html() != "") {
			err_msg('验证码不正确', 'miss');
			return false;
		}
		$('#formUser').submit();//提交
	}
</script>
</body>
</html>