<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>产品详情页</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <link rel="stylesheet" href="/Public/Weixin/css/common.css">
    <link rel="stylesheet" href="/Public/Weixin/css/chanpin.css">
    <link rel="stylesheet" href="/Public/Weixin/css/chanpinbuy.css">
    <link rel="stylesheet" href="/Public/Weixin/css/dingdan.css">
    <link rel="stylesheet" href="/Public/Weixin/css/dingdan2.css">

<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />

</head>
<body>

<div class="mainall">
    <div class="bg">
        <?php if($data['goodsDetail']['pics'] != ''): ?><img src="<?php echo ($data['goodsDetail']['pics'][0]['path']); ?>"/>
            <?php else: ?>
            <img src="<?php echo ($data['goodsDetail']['cover_img']); ?>"/><?php endif; ?>
        <div class="bgp">
            <p class="bgp1"><?php echo ($data["goodsDetail"]["title"]); ?></p>
            <p class="bgp2">上期：<?php echo ($data["period_last_type"]); ?></p>
            <p class="bgp3 fnTimeCountDown" data-end="<?php echo ($data["time_end"]); ?>">
                开战倒计时： <span class="hour">00</span>:<span class="mini">00</span>:<span class="sec">00</span>
                <!--:<span class="hm">000</span>-->

            </p>
            <p class="fnTimeCountDown1" data-end="<?php echo ($data["time_end"]); ?>" style="display: none">
                开战倒计时： <span class="mini">00</span>:<span class="sec">00</span>
                :<span class="hm">000</span>
            </p>
        </div>
    </div>

    <!--支付后-->
    <div class="vs">
        <div class="vsl">
            <img src="<?php echo ($data["memberData"]["headimgurl"]); ?>" alt="">
            <p class="red"><?php echo ($data["memberData"]["nickname"]); ?></p>
            <p class="vsl3"><?php echo ($data["order_detail"]["type_val"]); ?> <?php echo ($data["order_detail"]["num"]); ?>单</p>
        </div>
        <div class="vsm">
            <img src="/Public/Weixin/images/vs_03.png" alt="">
            <!--<div class="vsmp">-->
            <!--<div class="vsmp1">-->
            <!--<p><?php echo ($data["period_now"]); ?></p>期-->
            <!--</div>-->
            <!--</div>-->
        </div>

        <div class="vsr">
            <div class="vsrd">
                <?php if(is_array($data["info_pk"]["list"])): $i = 0; $__LIST__ = $data["info_pk"]["list"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div>
                        <img src="<?php echo ($vo["headimgurl"]); ?>" alt="">
                        <p class="gray"><?php echo ($vo["nickname"]); ?></p>
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
            <p class="vsl3"><?php echo ($data["info_pk"]["type_val"]); ?> <?php echo ($data["order_detail"]["num"]); ?>单</p>
        </div>

    </div>
<!--
    <div class="descr">恭喜您已成功购买【<?php echo ($data["goodsDetail"]["title"]); ?>】。请耐心等待开奖结果。<span id="time">10</span> 秒之后自动跳转至个人中心-购买记录。</div>
-->
    <div class="buy">
        <a href="/Weixin/Goods/detail/id/<?php echo ($data["order_detail"]["goods_id"]); ?>"> <button style="color: #fff;">继续购买</button></a>
    </div>
    <div class="buy">
        <a href="/Weixin/My/buyLog"> <button style="color: #fff;">查看购买记录</button></a>
    </div>

</div>
<div class="footer">
    <div class="f1">
        <a href="<?php echo U('Inedex/index');?>" class="active">
            <img src="/Public/Weixin/images/f1a_03.png" alt="" />
            <p>首页</p>
        </a>
    </div>
    <div class="f2">
    	<a href="<?php echo U('Openprize/index');?>">
    		<img src="/Public/Weixin/images/f2_03.png" alt="" />
            <p>开奖号码</p>
    	</a>
    </div>
    <div class="f3">
        <a href="<?php echo U('My/index');?>">
            <img src="/Public/Weixin/images/f3_03.png" alt="" />
            <p>个人中心</p>
        </a>
    </div>
</div>

<script type="text/javascript" src="/Public/Weixin/js/zepto.min.js"></script>

<script type="text/javascript">
//    $(function(){
//        var djs=document.getElementById("time");
//        var i=10;
//        var timer1=null;
//        timer1=setInterval(function(){
//           i--;
//            if(i<=0){
//                clearInterval(timer1);
//                i=0;
//                window.location.href="<?php echo U('My/buyLog');?>";
//            }
//            djs.innerHTML=i;
//        },1000);
//    })
</script>
</body>
</html>