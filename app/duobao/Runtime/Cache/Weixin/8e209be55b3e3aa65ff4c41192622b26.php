<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>个人中心</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <link rel="stylesheet" href="/Public/Weixin/css/common.css">
    <link rel="stylesheet" href="/Public/Weixin/css/center.css">
</head>
<body>
<div class="mainall">
    <div class="bg bg-center">
    	<!--<img class="bg_img" src="/Public/Weixin/images/bg3_02.png" alt="" />-->
        <div class="tx">
            <div class="tximg"><img src="<?php echo ($data["headimgurl"]); ?>" alt=""></div>
        </div>
        
            <p class="tx-name"><?php echo ($data["nickname"]); ?>【<?php echo ($usertype); ?>】</p>
    </div>
    <ul class="centerlis">
    	<li class="centerlis-1">
    		<em></em>
    		<p>常用功能</p>
    	</li>
        <a href="<?php echo U('My/buyLog');?>"><li>
            <p class="li-img"><img src="/Public/Weixin/images/c1.png" alt=""></p>
            <p class="li-p">购买记录</p>
            <p class="li-r"><img src="/Public/Weixin/images/c-right_03.png" alt="" /></p>
        </li></a>
        <a href="<?php echo U('My/exchangeList');?>"><li>
            <p class="li-img"><img src="/Public/Weixin/images/c2.png" alt=""></p>
            <p class="li-p">兑奖记录</p>
            <p class="li-r"><img src="/Public/Weixin/images/c-right_03.png" alt="" /></p>
        </li></a>
        <!--
        <a href="<?php echo U('My/account');?>"><li>
            <p class="li-img"><img src="/Public/Weixin/images/money.jpg" alt=""></p>
            <p class="li-p">个人账户</p>
            <p class="li-r"><img src="/Public/Weixin/images/c-right_03.png" alt="" /></p>
        </li></a>
        -->
        <a href="<?php echo U('My/join');?>" class="atop"><li>
            <p class="li-img"><img src="/Public/Weixin/images/join.jpg" alt=""></p>
            <p class="li-p">申请加盟</p>
            <p class="li-r"><img src="/Public/Weixin/images/c-right_03.png" alt="" /></p>
        </li></a>        
        <?php if($isjoin == true): ?><a href="<?php echo U('My/mylink');?>"><li>
            <p class="li-img"><img src="/Public/Weixin/images/fenxiao.jpg" alt=""></p>
            <p class="li-p">分销链接</p>
            <p class="li-r"><img src="/Public/Weixin/images/c-right_03.png" alt="" /></p>
        </li></a>
        <a href="<?php echo U('My/yongjinlog');?>"><li>
            <p class="li-img"><img src="/Public/Weixin/images/money.jpg" alt=""></p>
            <p class="li-p">佣金明细</p>
            <p class="li-r"><img src="/Public/Weixin/images/c-right_03.png" alt="" /></p>
        </li></a>  
        <a href="<?php echo U('My/fxuser');?>"><li>
            <p class="li-img"><img src="/Public/Weixin/images/money.jpg" alt=""></p>
            <p class="li-p">分销会员</p>
            <p class="li-r"><img src="/Public/Weixin/images/c-right_03.png" alt="" /></p>
        </li></a><?php endif; ?>       

        <a href="<?php echo U('Index/introduce');?>" class="atop"><li>
            <p class="li-img"><img src="/Public/Weixin/images/c3.png" alt=""></p>
            <p class="li-p">玩法介绍</p>
            <p class="li-r"><img src="/Public/Weixin/images/c-right_03.png" alt="" /></p>
        </li></a>


        <a href="<?php echo U('User/logout');?>"><li>
            <p class="li-img"><img src="/Public/Weixin/images/c4.png" alt=""></p>
            <p class="li-p">用户注销</p>
            <p class="li-r"><img src="/Public/Weixin/images/c-right_03.png" alt="" /></p>
        </li></a>


    </ul>
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
</body>
</html>