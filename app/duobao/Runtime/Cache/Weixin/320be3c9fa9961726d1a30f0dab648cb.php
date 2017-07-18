<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>首页</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <link rel="stylesheet" href="/Public/Weixin/css/common.css">
    <link rel="stylesheet" type="text/css" href="/Public/Weixin/css/swiper.min.css"/>
    <link rel="stylesheet" href="/Public/Weixin/css/homePage.css">
    
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />    
</head>
<body>
<div class="mainall">

    <div class="bg">
    	<div class="swiper-container">
			<div class="swiper-wrapper">
                <?php if(is_array($data["indexImgs"])): $i = 0; $__LIST__ = $data["indexImgs"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="swiper-slide">
                    <?php if($vo['url'] != ''): ?><a href="<?php echo ($vo["url"]); ?>"><?php endif; ?>
                    <img src="<?php echo ($vo["path"]); ?>" />
                    <?php if($vo['url'] != ''): ?></a><?php endif; ?>
                </div><?php endforeach; endif; else: echo "" ;endif; ?>
			</div>

			<div class="swiper-pagination"></div>
		</div>
    </div>
    <div class="bgbottom fnTimeCountDown" data-end="<?php echo ($data["time_end"]); ?>">
        <img src="/Public/Weixin/images/laba_03.png" alt="">
        开战倒计时： <span class="hour">00</span>:<span class="mini">00</span>:<span class="sec">00</span>
        <!--:<span class="hm">000</span>-->
    </div>
    <p class="fnTimeCountDown1" data-end="<?php echo ($data["time_end"]); ?>" style="display: none">
        开战倒计时： <span class="mini">00</span>:<span class="sec">00</span>
        :<span class="hm">000</span>
    </p>
	
	<div class="tab-img">
		<div class="active">50元卡</div>
		<div>100元卡</div>
		<!--<div>某某卡</div>-->
	</div>
	<div class="tab-ul">
		<ul class="list-li active">
	        <?php if(is_array($data["list_50"])): $i = 0; $__LIST__ = $data["list_50"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
                    <a href="<?php echo U('Goods/detail?id='.$vo['id']);?>">
	                    <img src="<?php echo (get_cover($vo["cover_id"],'path')); ?>" alt="">
	                    <p>半价购买：¥ <span>28</span></p>
	                </a>
	            </li><?php endforeach; endif; else: echo "" ;endif; ?>
	    </ul>
    	<ul class="list-li">
	        <?php if(is_array($data["list_100"])): $i = 0; $__LIST__ = $data["list_100"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
                    <a href="<?php echo U('Goods/detail?id='.$vo['id']);?>">
	                    <img src="<?php echo (get_cover($vo["cover_id"],'path')); ?>" alt="">
	                    <p>半价购买：¥ <span>55</span></p>
	                </a>
	            </li><?php endforeach; endif; else: echo "" ;endif; ?>
	    </ul>

	</div>
    <!--<div class="tejia">-->
        <!--<a href="http://www.nike.com/cn/zh_cn/c/nike-plus"><img src="<?php echo ($data["indexGoods"]["path"]); ?>" alt=""></a>-->
    <!--</div>-->
    <div class="tab-index">
    	<div class="active">
    		最近中奖
    	</div>
    	<div onclick="location.href='/Weixin/Index/introduce'">玩法规则</div>
    </div>
    <div class="tab-con">
	    <ul class="lis">
	        <?php if(is_array($data["pk_list"])): $i = 0; $__LIST__ = $data["pk_list"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
	                <div class="lf">
	                    <img src="<?php echo ($vo["userinfo"]["headimgurl"]); ?>" alt="">
	                </div>
	                <div class="lm">
	                	<div class="lm-head">
	                		<p class="lmh" style="text-overflow: ellipsis;width: 2.8rem;"><?php echo ($vo["userinfo"]["nickname"]); ?></p>
	                		 <p class="lmt"><?php echo (date('Y/m/d H:i:s',$vo["buy_time"])); ?></p>
	                	</div>
	                    
	                    <p class="lm-gwk"><?php echo ($vo["goods_title"]); ?>  中奖<?php echo ($vo["buy_num"]); ?>单</p>
	                   
	                </div>
	                <!--<div class="lt">
	                    <p class="lt1"><span><?php echo ($vo["num"]); ?></span>单</p><p class="lt2">参与</p>
	                </div>-->
	            </li><?php endforeach; endif; else: echo "" ;endif; ?>
	    </ul>
		
        <!--<ul class="list">-->
        	<!--<li class="list-head">-->
        		<!--<p><a>开奖时间</a></p>-->
        		<!--<p><a>开奖号码</a></p>-->
        		<!--<p><a>50元区</a></p>-->
        		<!--<p><a>100元区</a></p>-->
        	<!--</li>-->
            <!--<?php if(is_array($data["code_list"])): $i = 0; $__LIST__ = $data["code_list"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>-->
                <!--<li>-->
                    <!--<div class="li1">-->
                        <!--<p class="p-1"><?php echo ($vo['create_time'][0]); ?></p>-->
                        <!--<p class="p-2"><?php echo ($vo['create_time'][1]); ?></p>-->
                    <!--</div>-->
                    <!--<div class="li2"><?php echo ($vo["code"]); ?></div>-->
                    <!--<div class="li3"><p><?php echo ($vo["code_56"]); ?>&nbsp;&nbsp;<?php echo ($vo["code_56_type"]); ?></p></div>-->
                    <!--<div class="li3 li4"><p><?php echo ($vo["code_110"]); ?>&nbsp;&nbsp;<?php echo ($vo["code_110_type"]); ?></p></div>-->
                <!--</li>-->
            <!--<?php endforeach; endif; else: echo "" ;endif; ?>-->
        <!--</ul>-->

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
<!--<script type="text/javascript" src="/Public/Weixin/js/jquery-1.11.1.min.js"></script>-->
<script type="text/javascript" src="/Public/Weixin/js/zepto.min.js"></script>
<script src="/Public/Weixin/js/swiper.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="/Public/Weixin/js/countdown.js"></script>
<script>
	var swiper = new Swiper('.swiper-container', {
		pagination: '.swiper-pagination',
		paginationClickable: true,
		autoplay: 3000
	});
	$(".tab-img div").tap(function(){
		var i=$(this).index();
		console.log(i);
		$(".tab-img div").eq(i).addClass('active').siblings().removeClass('active');
		$(".tab-ul ul").eq(i).addClass('active').siblings().removeClass('active');
	})
//	$(".tab-index div").tap(function(){
//		var i=$(this).index();
//		$(".tab-index div").eq(i).addClass('active').siblings().removeClass('active');
//		$(".tab-con ul").eq(i).show().siblings().hide();
//	})
    function tCDThml(element) {
        var second = 9;
        var time = setInterval(function(){
            second--;
            element.html("正在揭晓&nbsp;&nbsp;"+second);
            if (second == 0) {
                clearInterval(time);
            };
        },1000);
    };
    function mat(date) {
        var datetime = date.getFullYear()
                + "-"// "年"
                + ((date.getMonth() + 1) > 10 ? (date.getMonth() + 1) : "0"
                + (date.getMonth() + 1))
                + "-"// "月"
                + (date.getDate() < 10 ? "0" + date.getDate() : date
                        .getDate());
        return datetime;
    }

    Date.prototype.Format = function(fmt) { //author: meizz
        var o = {
            "M+" : this.getMonth() + 1, //月份
            "d+" : this.getDate(), //日
            "h+" : this.getHours(), //小时
            "m+" : this.getMinutes(), //分
            "s+" : this.getSeconds(), //秒
            "q+" : Math.floor((this.getMonth() + 3) / 3), //季度
            "S" : this.getMilliseconds()
            //毫秒
        };
        if (/(y+)/.test(fmt))
            fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "")
                    .substr(4 - RegExp.$1.length));
        for ( var k in o)
            if (new RegExp("(" + k + ")").test(fmt))
                fmt = fmt.replace(RegExp.$1,
                        (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k])
                                .substr(("" + o[k]).length)));
        return fmt;
    }

    $(function() {
        // 倒计时
        var date = new Date();
        var as=10;
        if((date.getHours()>=22||date.getHours()<1||(date.getHours()==1&&date.getMinutes()<=55))){
            as=5;
        }
        //开奖时间为10：00 --02：00
        if((date.getHours()>=2&&date.getHours()<10)||(date.getHours()==1&&date.getMinutes()>55)){
            $(".fnTimeCountDown").fnTimeCountDown(tCDThml);

            $(".fnTimeCountDown1").fnTimeCountDown(function() {
                location.href = location;
            });
        }else{
            if (date.getMinutes()%as==0&&date.getSeconds() < 8) {
                var second = 8 - date.getSeconds();
                console.log(second);
                var time = setInterval(function(){
                    second--;
                    $(".fnTimeCountDown").html("正在揭晓&nbsp;&nbsp;"+second);
                    if (second == 0) {
                        clearInterval(time);
                    };
                },1000);
                setTimeout(function() {
                    location.href = location;
                }, (8 - date.getSeconds()) * 1000);
            }else{
                $(".fnTimeCountDown").fnTimeCountDown(tCDThml);

                $(".fnTimeCountDown1").fnTimeCountDown(function() {
                    location.href = location;
                });
            }
        }


    })
</script>

</body>
</html>