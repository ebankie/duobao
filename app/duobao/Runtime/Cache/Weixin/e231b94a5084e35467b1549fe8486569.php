<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>联系客服兑奖</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <link rel="stylesheet" href="/Public/Weixin/css/common.css">
    <link rel="stylesheet" href="/Public/Weixin/css/password.css">
</head>
<body>
<div class="mainall">
    <div class="phone">兑奖码：<?php echo ($data["exchange_number"]); ?></div>
    <div class="addwx">
    	<img src="/Public/Weixin/images/addewm.png" alt="" />
    </div>
    <div class="wx-ewm">
    	<img src="/Public/Weixin/images/ewm_img.jpg" alt="" />
    </div>
    <p class="longtap">（长按二维码，添加客服微信兑奖）</p>
    <!--<div class="btn">
        <button class="btn1" id="confirm"  style="width: 6.666rem;">联系兑奖客服</button>
    </div>-->
</div>

<!--弹窗-->
<div class="dh-alert" style="display: none;">
    <div class="dh-quit">×</div>
    <p class="dh-1">添加客服兑奖</p>
    <div class="dh-con">
        <p class="dh-con-head">常按二维码，添加客服微信兑奖</p>
        <img class="dh-ewm" src="/Public/Weixin/images/ewm.jpeg" alt="" />
    </div>

</div>

<div class="footer">
    <div class="f1">
        <a href="<?php echo U('Inedex/index');?>">
            <img src="/Public/Weixin/images/f1_03.png" alt="" />
            <p>首页</p>
        </a>
    </div>
    <div class="f2">
    	<a href="<?php echo U('Openprize/index');?>">
    		<img src="/Public/Weixin/images/f2_03.png" alt=""  />
            <p>开奖号码</p>
    	</a>
    </div>
    <div class="f3">
        <a href="<?php echo U('My/index');?>"  class="active">
            <img src="/Public/Weixin/images/f3a_03.png" alt=""/>
            <p>个人中心</p>
        </a>
    </div>
</div>

<script type="text/javascript" src="/Public/Weixin/js/zepto.min.js"></script>
<script>
    $('#confirm').click(function(){
        $('.dh-alert').show();
    });

    //点击弹窗消失
    $(".dh-quit").tap(function(){
        $(".dh-alert").hide();
    })
</script>
</body>
</html>