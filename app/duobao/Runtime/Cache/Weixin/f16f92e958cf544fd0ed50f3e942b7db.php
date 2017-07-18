<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>分销会员</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">

    <link rel="stylesheet" href="/Public/Weixin/css/common.css">

    <link rel="stylesheet" href="/Public/Weixin/css/buyrecord.css">
    
    <link rel="stylesheet" type="text/css" href="/Public/Weixin/css/swiper.min.css"/>
    <link rel="stylesheet" href="/Public/Weixin/css/homePage.css">    

</head>

<body>
<div class="mainall">
    <div class="foot">
        【<?php echo ($username); ?>】共发展<?php echo ($nums); ?>个会员 &nbsp;&nbsp;&nbsp;&nbsp; 总收益：<?php echo ($zong); ?> 元 &nbsp;&nbsp;&nbsp;&nbsp; 今日收益：<?php echo ($tdzong); ?> 元
    </div>
</div>
<div class="tab-con">
    <ul class="lis">
        <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
                <div class="lf">
                    <img src="<?php echo ($vo["headimgurl"]); ?>" alt="">
                </div>
                <div class="lm">
                    <div class="lm-head">
                        <p class="lmh" style="text-overflow: ellipsis;width: 3.8rem;"><?php echo ($vo["nickname"]); ?></p>
                        <p class="lmt"  style="font-size:10px">手机:<?php echo ($vo["mobile"]); ?></p>
                    </div>
                    <div class="lm-head">
                        <p class="lmh" style="text-overflow: ellipsis;width: 4.8rem;font-size:9px">总收益:<?php echo ($vo["zong"]); ?>元&nbsp;&nbsp;今日收益:<?php echo ($vo["tdzong"]); ?>元</p>
                        <p class="lmt"><a href="<?php echo U('My/fxuser?uid='.$vo['uid']);?>" style="color:#C30">下级会员</a></p>
                    </div>                    
                    <!--<p class="lm-gwk">注册时间：<?php echo (date('Y/m/d H:i:s', $vo["reg_time"])); ?>&nbsp;&nbsp;&nbsp;&nbsp;</p>-->
                   
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