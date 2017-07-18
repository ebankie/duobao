<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>兑奖记录</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <link rel="stylesheet" href="/Public/Weixin/css/common.css">
    <link rel="stylesheet" href="/Public/Weixin/css/duihuan.css">
</head>
<body>
<div class="mainall">
    <div class="tab">
        <div class="tabtitle">
            <p class="active">未兑奖</p>
            <p class="">已兑奖</p>
        </div>
        <div class="tabuls">
            <ul class="buylis">
                <?php if(is_array($noExchangeList)): $i = 0; $__LIST__ = $noExchangeList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
                        <div class="li1">
                            <div class="li-img"><img src="<?php echo (get_cover($vo["order"]["goods_detail"]["cover_id"],'path')); ?>" alt=""></div>
                            <!--<div class="li-p1">半价购买 : ¥ <span class="red"><?php echo ($vo["goods_type"]); ?></span> </div>-->
                        </div>
                        <div class="li2">
                            <p class="li-tit"><?php echo ($vo["order"]["goods_detail"]["title"]); ?></p>
                            <!--<p class="">本期期数： <span class="red"><?php echo ($vo["order"]["period"]); ?></span></p>-->
                            <p>我的选择：<span class="red"><?php echo ($vo["order"]["num"]); ?>单 <?php echo ($vo["type"]); ?></span></p>
                            <!--<p>获胜号码：<span class="red"><?php echo ($vo["win_code"]); ?></span></p>-->
                            <p>参与时间：<?php echo (date('Y/m/d H:i:s', $vo["order"]["create_time"])); ?></p>
                            <p>开奖时间：<?php echo ($vo["order"]["lottery_time"]); ?></p>
                        </div>
                        <div class="qx"><a href="/Weixin/My/getExchangeCode/id/<?php echo ($vo["id"]); ?>" >兑奖</a></div>
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
            <ul>
                <?php if(is_array($yesExchangeList)): $i = 0; $__LIST__ = $yesExchangeList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
                        <div class="li1">
                            <div class="li-img"><img src="<?php echo (get_cover($vo["order"]["goods_detail"]["cover_id"],'path')); ?>" alt=""></div>
                            <div class="li-p1">半价购买 : ¥ <span class="red"><?php echo ($vo["goods_type"]); ?></span> </div>
                        </div>
                        <div class="li2">
                            <p class="li-tit"><?php echo ($vo["order"]["goods_detail"]["title"]); ?></p>
                            <!--<p class="">本期期数： <span class="red"><?php echo ($vo["order"]["period"]); ?></span></p>-->
                            <p>我的选择：<span class="red"><?php echo ($vo["order"]["num"]); ?>单 <?php echo ($vo["type"]); ?></span></p>
                            <!--<p>获胜号码：<span class="red"><?php echo ($vo["win_code"]); ?></span></p>-->
                            <p>参与时间：<?php echo (date('Y/m/d H:i:s', $vo["order"]["create_time"])); ?></p>
                            <p>开奖时间：<?php echo ($vo["order"]["lottery_time"]); ?></p>
                        </div>
                        <!--<div class="qx">查看详情</div>-->
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
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
<script type="text/javascript">
    $(function(){
        $('.tabtitle p').tap(function(){
            var i=$(this).index();
            $(this).addClass('active').siblings().removeClass('active');
            $('.tabuls ul').eq(i).show().siblings().hide();
        })
    })
</script>
</body>
</html>