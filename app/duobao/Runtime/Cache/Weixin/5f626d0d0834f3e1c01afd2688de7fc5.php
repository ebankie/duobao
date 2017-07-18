<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>订单详情</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <link rel="stylesheet" href="/Public/Weixin/css/common.css">
    <link rel="stylesheet" href="/Public/Weixin/css/dingdan3.css">
    <link rel="stylesheet" href="/Public/Weixin/css/dingdan2.css">
</head>
<body>
<div class="mainall">
    <div class="bg">
        <?php if($info['goods_detail']['pics'] != ''): ?><img src="<?php echo ($info['goods_detail']['pics'][0]['path']); ?>"/>
            <?php else: ?>
            <img src="<?php echo ($info['goods_detail']['cover_img']); ?>"/><?php endif; ?>
        <div class="bgp">
            <p class="bgp1"><?php echo ($info["goods_detail"]["title"]); ?></p>
            <p class="bgp2">本期：第<?php echo ($info["period"]); ?>期</p>
        </div>
    </div>
    <div class="vs">
        <div class="vsl">
            <img src="<?php echo ($info["member_info"]["headimgurl"]); ?>" alt="">
            <p class="red"><?php echo ($info["member_info"]["nickname"]); ?></p>
            <!--<p class="vsl4"><?php echo (date('Y/m/d H:i:s', $info["create_time"])); ?></p>-->
            <p class="vsl3"><?php echo ($info["type_val"]); ?> <?php echo ($info["num"]); ?>单</p>
            <?php if(($info["is_open"]) == "1"): if(!empty($info["is_win"])): ?><div class="win"><img src="/Public/Weixin/images/win_03.png" alt=""></div><?php endif; endif; ?>
        </div>
        <div class="vsm">
            <img src="/Public/Weixin/images/vs_03.png" alt="">
            <!--<div class="vsmp">-->
                <!--<div class="vsmp1">-->
                    <!--<p><?php echo ($info["period"]); ?></p>期-->
                <!--</div>-->
            <!--</div>-->
        </div>
        <?php if(!empty($info_pk)): ?><div class="vsr">
                <div class="vsrd">
                    <?php if(is_array($info_pk["list"])): $i = 0; $__LIST__ = $info_pk["list"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div>
                            <img src="<?php echo ($vo["headimgurl"]); ?>" >
                            <p class="gray"><?php echo ($vo["nickname"]); ?></p>
                        </div><?php endforeach; endif; else: echo "" ;endif; ?>
                </div>
                <p class="vsl3"><?php if($info['type'] == 1): ?>大<?php else: ?>小<?php endif; ?> <?php echo ($info["num"]); ?>单</p>
                <?php if(($info["is_open"]) == "1"): if(empty($info["is_win"])): ?><div class="win win2"><img src="/Public/Weixin/images/win_03.png" alt=""></div><?php endif; endif; ?>
            </div><?php endif; ?>

    </div>
    <div class="foot">
        <p>开奖号码：<span class="red"><?php echo ($info["win_code"]["code"]); ?></span></p>
        <p>开奖时间：<span class="red"><?php echo ($info["lottery_time"]); ?></span></p>
        <p>开奖结果：<span class="red"><?php echo ($info["win_code_val"]); ?> <?php echo ($info["win_code_val_type"]); ?></span> </p>
        <div class="gz"><a href="<?php echo U('Index/gameIntroduce');?>" style="color:#8296b1;">算法规则</a></div>
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

</div>
<script src="/Public/Weixin/js/jquery-1.11.1.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
    console.log($('.vsrd>div').length);
    var len = $(' .vsrd>div ').length;
    if(len == 1){
        $('.win').addClass('win_center');
    }
</script>
</body>
</html>