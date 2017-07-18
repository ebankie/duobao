<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>购买记录</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <link rel="stylesheet" href="/Public/Weixin/css/common.css">
    <link rel="stylesheet" href="/Public/Weixin/css/buyrecord.css">
</head>
<body>
<div class="mainall">
	<div class="foot">
        <p>当日参与：<?php echo ($order_num); ?>单   </p>
        <p>当日获奖：<?php echo ($order_success_num); ?>单 </p>
        <p>当日未获奖：<?php echo ($order_fail_num); ?>单</p>
    </div>
    <ul class="buylis">
        <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
                <div class="li1">
                    <div class="li-img"><img src="<?php echo (get_cover($vo["goods_detail"]["cover_id"],'path')); ?>" alt=""></div>
                    <!--<div class="li-p1">半价购买 : ¥ <span class="red"><?php echo ($vo["goods_type"]); ?></span> </div>-->
                    <?php if($vo['order_type'] == 1): ?><div class="li-p2">待开奖</div>
                    <?php elseif($vo['order_type'] == 2): ?>
                        <!--<div class="li-p3">恭喜获胜</div>-->
                        <div class="li-p2"><a href="<?php echo U('My/exchangeList');?>" style="color: #db4252;">获胜领奖</a></div>
                    <?php elseif($vo['order_type'] == 3): ?>
                        <div class="li-p2 li-p4">未中奖</div>
                    <?php elseif($vo['order_type'] == 4): ?>
                        <div class="li-p2"><a style="color: #db4252;">已兑奖</a></div><?php endif; ?>
                </div>
                <div class="li2">
                    <p class="li-tit"><?php echo ($vo["goods_detail"]["title"]); ?></p>
                    <!--<p class="">本期期数： <span class="red"><?php echo ($vo["period"]); ?></span></p>-->
                    <p>我的选择：<span class="red"><?php echo ($vo["num"]); ?>单 <?php echo ($vo["type"]); ?></span></p>
                    <p>参与时间：<?php echo (date('Y/m/d H:i:s', $vo["create_time"])); ?></p>
                    <p>开奖时间：<?php echo ($vo["lottery_time"]); ?></p>
                </div>
                <div class="qx"><a href="/Weixin/My/orderDetail/id/<?php echo ($vo["id"]); ?>" style="color: #5d6b7d;">查看详情</a></div>
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