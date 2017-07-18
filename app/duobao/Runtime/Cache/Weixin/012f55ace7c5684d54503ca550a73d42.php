<?php if (!defined('THINK_PATH')) exit(); if(C('LAYOUT_ON')) { echo ''; } ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
<title>跳转提示</title>
<style type="text/css">
*{ padding: 0; margin: 0; }
body{ background: #fff; font-family: '微软雅黑'; color: #333; font-size: 16px; }
.system-message{  width:92%; padding:100px 2%; margin-left:2%; margin-right:2%; box-sizing:border-box; margin:0px  auto; background:#fff; box-shadow:0px 0px 3px  #999; position:absolute; top:50%; height:300px; margin-top:-150px; left:4%;}
.cls{ width:0px; height:0px; margin:0px; padding:0px; clear:both;}
.system_top{ text-align:center; color:#323232; font-size:18px; font-family:微软雅黑;}
.system_top h1{ display:inline-block; vertical-align:middle;}
.system_top p{ display:inline-block;}
.system_bot{ text-align:center; margin:10px 0px; color:#928d8d;}
.system_bot b{ color:#488bb9; display:inline-block; margin:0 2px;}
.system_footer{ text-align:center;}
.system_footer a{ display:inline-block; width:200px; height:40px; line-height:40px; text-decoration:none; background:#488bb9; color:#fff; border-radius:4px;}
.system_footer a:hover{ opacity:0.6;}
</style>
</head>
<body style="background:#ededed;">
<div class="system-message">
<div class="system_top">    
    <?php if(isset($message)) {?>
    <h1><img src="/ThinkPHP/img_yes.png" width="35" height="35" /></h1>
    <p class="success"><?php echo($message); ?></p>
    
    <?php }else{?>
    
    <h1><img src="/ThinkPHP/img_gantan.png" width="35" height="35" /></h1>
    <p class="error"><?php echo($error); ?></p>
	<?php }?>
    <div class="cls"></div>
</div>
<div class="system_bot">
    <p class="detail" style="display:none;"></p>
    <p class="jump">
        <b id="wait"><?php echo($waitSecond); ?></b>秒后自动跳转
    </p>
</div>
<div class="system_footer">
	<a id="href" href="<?php echo($jumpUrl); ?>">立即前往</a>
</div>
</div>
<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time <= 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script>
</body>
</html>