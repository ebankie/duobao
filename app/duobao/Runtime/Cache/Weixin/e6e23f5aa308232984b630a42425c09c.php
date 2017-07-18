<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>分佣记录</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">

    <link rel="stylesheet" href="/Public/Weixin/css/common.css">

    <link rel="stylesheet" href="/Public/Weixin/css/buyrecord.css">

</head>

<body>

<div class="mainall">

    <div class="foot">
        当前用户总收益：<?php echo ($zong); ?> 元     &nbsp;&nbsp;&nbsp;&nbsp; 今日收益：<?php echo ($tdzong); ?> 元
    </div>

    <ul class="buylis">

        <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li style="height: 90px;">

                <div class="li2">
                    <p class="li-tit">佣金来源:<?php echo ($vo["nickname"]); ?></p>
                    <p>金额：<span class="red"><?php echo ($vo["money_p"]); ?></span></p>
                    <p>获得时间：<?php echo (date('Y/m/d H:i:s', $vo["create_time"])); ?></p>
                </div>

            </li><?php endforeach; endif; else: echo "" ;endif; ?>

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

<script type="text/javascript"></script>

</body>

</html>