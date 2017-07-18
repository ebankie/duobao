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
            <p class="bgp3 fnTimeCountDown" data-end="<?php echo ($data["time_end"]); ?>" >
                开战倒计时： <span class="hour">00</span>:<span class="mini">00</span>:<span class="sec">00</span>
                <!--:<span class="hm">000</span>-->

            </p>
            <p class="fnTimeCountDown1" data-end="<?php echo ($data["time_end"]); ?>" style="display: none">
                开战倒计时： <span class="mini">00</span>:<span class="sec">00</span>
                :<span class="hm">000</span>
            </p>
        </div>
    </div>
    <div class="con">
        <div class="con1">
            <div class="con1l"><img src="<?php echo ($data["memberData"]["headimgurl"]); ?>" alt=""></div>
            <div class="con1m">
                <p class="conp1"><?php echo ($data["memberData"]["nickname"]); ?></p>
                <p class="conp2">[<?php echo ($data["memberData"]["ip"]); ?>]</p>
            </div>
            <div class="con1r">
                第<?php echo ($data["period_now"]); ?>期
            </div>
        </div>
        <div class="con2">
            <div class="con2x" onclick="buy(1);">买小（<?php echo ($data["code_word1"]); ?>）</div>
            <div class="con2d" onclick="buy(2);">买大（<?php echo ($data["code_word2"]); ?>）</div>
        </div>
    </div>

    <div class="tab">
        <div class="tabtitle">
            <p class=" active">最近中奖</p>
            <p >算法规则</p>
        </div>
        <div class="tablis">
            <ul>
                <?php if(is_array($data["pk_list"])): $i = 0; $__LIST__ = $data["pk_list"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
                        <div class="li1"><img src="<?php echo ($vo["userinfo"]["headimgurl"]); ?>" alt=""></div>
                        <div class="li2">
                            <p>
                                <span class="li2l"><?php echo ($vo["userinfo"]["nickname"]); ?></span>
                                <span class="li2r"js><?php echo (date('Y/m/d H:i:s',$vo["buy_time"])); ?></span>
                            </p>

                            <!--<p>刚刚参与<span><?php echo ($vo["num"]); ?></span> 单,-&#45;&#45;<?php echo ($vo["ip_info"]); ?></p>-->
                            <p><?php echo ($data["goodsDetail"]["title"]); ?>  中奖<span><?php echo ($vo["buy_num"]); ?></span>单 [<?php echo ($vo["city"]); ?>]</p>
                        </div>
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
            <ul class="tablis-rule">
                <li>
                    <p class="tr-p">本期号码÷本期商品所消耗的微币数量所得余数加上1</p>
                    <img class="tr-img" src="/Public/Weixin/images/new_rule_way.png"/>
                </li>

            </ul>
        </div>
    </div>

    <!--支付弹框-->
    <div class="alert" style="display: none;">
        <div class="content">
            <div class="cont"><?php echo ($data["goodsDetail"]["title"]); ?></div>
            <div class="conbd">
                <p class="cbdt">参与单数</p>
                <div class="btn-group">
                    <button class="btn1">－</button>
                    <input type="number" class="btn2" id="btn2" value="1" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">
                    <button class="btn3">＋</button>
                </div>
                <ul class="number">
                    <?php if($data['goodsDetail']['price'] == 1): ?><li>1</li>
                        <li>10</li>
                        <li>20</li>
                        <li>30</li>
                        <li>50</li>
                        <li>60</li>
                        <li>80</li>
                        <li>100</li>
                    <?php else: ?>
                        <li>1</li>
                        <li>5</li>
                        <li>10</li>
                        <li>15</li>
                        <li>20</li>
                        <li>30</li>
                        <li>40</li>
                        <li>50</li><?php endif; ?>

                    <li class="max-sum">单次最多可购买<span><?php if($data['goodsDetail']['price'] == 1): ?>100<?php else: ?>50<?php endif; ?></span>单</li>
                </ul>
                <input type="hidden" id="goods_type" value="<?php echo ($data["goodsDetail"]["price"]); ?>">
                <input type="hidden" id="pay_type">
                <div class="sum">总需：<span class="sumn"><?php echo ($data["price_half_1"]); ?></span> 微币<!--(当前拥有<?php echo ($price); ?>微币)--></div>
                <!--<div class="pay" onclick="callpay();">立即支付</div>-->
                <!--<div class="pay" onclick="judge();">立即支付</div>-->
                <div class="pay" onclick="getpay();">立即支付</div>
            </div>
            <div class="line"></div>
            <div class="close"></div>
        </div>
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
<script type="text/javascript" src="/Public/Weixin/js/countdown.js"></script>

<script type="text/javascript">
    $(function(){
        $('.tabtitle p').tap(function(){
            var i=$(this).index();
            $(this).addClass('active').siblings().removeClass('active');
            $('.tablis ul').eq(i).show().siblings().hide();
        })

        $('.close').tap(function () {
            $(".alert").hide();
        })
        document.getElementById('btn2').oninput=function(){
            var goods_type = $('#goods_type').val();
            if(goods_type == 2){
                var max = 50;
            }else{
                var max = 100;
            }
            if($("#btn2").val()>max){
                $(".btn2").val(max);
                alert('单次最多可购买'+max+'单');

                $(".sumn").html($(".btn2").val()*<?php echo ($data["price_half"]); ?>);
                return false;
            }

            $(".sumn").html($(".btn2").val()*<?php echo ($data["price_half"]); ?>);
        }
        $(".btn1").tap(function(){
            var x=$(".btn2").val();
            console.log(x);
            if(x <= 1){
                x = 1;
            }else{
                x--;
            }
            var goods_type = $('#goods_type').val();
            if(goods_type == 2){
                var max = 50;
            }else{
                var max = 100;
            }

            if(x>max){
                $(".btn2").val(max);
                alert('单次最多可购买'+max+'单');
                return false;
            }
            $(".btn2").val(x);
            $(".sumn").html($(".btn2").val()*<?php echo ($data["price_half"]); ?>);
        })
        $(".btn3").tap(function(){
            var x=$(".btn2").val();
            x++;
            var goods_type = $('#goods_type').val();
            if(goods_type == 2){
                var max = 50;
            }else{
                var max = 100;
            }

            if(x>max){
                $(".btn2").val(max);
                alert('单次最多可购买'+max+'单');
                return false;
            }
            $(".btn2").val(x);
            $(".sumn").html($(".btn2").val()*<?php echo ($data["price_half"]); ?>);
        })
        $(".number li").tap(function(){
            var n=$(this).html();
            $(".btn2").val(n);
            $(".sumn").html($(".btn2").val()*<?php echo ($data["price_half"]); ?>);
        })
    })

    //下单
    function buy(type){
        var date = new Date();
        if((date.getHours()>=2 && date.getHours()<10) || (date.getHours()==1 && date.getMinutes()>55)){
            alert('请等待开战时间');
            return false;
        }

        if(type == 2){
            $('.cont').css('background','rgba(243,141,49,1)');
            $('.pay').css('background','rgba(243,141,49,1)');
        }else{
            $('.cont').css('background','rgba(219,66,82,1)');
            $('.pay').css('background','rgba(219,66,82,1)');
        }
        $('#pay_type').val(type);
        $(".alert").show();
    }

</script>

<script>
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
<script>
    //    function gopay(){
    //        var total_fee = $('.sumn').html();
    //        var type = $('#pay_type').val();
    //        var num = $(".btn2").val();
    //        var lottery_time = $(".fnTimeCountDown").data('end');
    //        var goods_id = <?php echo ($data["goods_id"]); ?>;
    //        if(total_fee == ''  || type =='' || num =='' || lottery_time =='' || goods_id ==''){
    //            alert('操作异常');
    //            return false;
    //        }
    //        $.ajax({
    //            type: 'post',
    //            url: '/Weixin/Goods/getQfpayUrl',
    //            data: {total_fee:total_fee,type:type,num:num,lottery_time:lottery_time,goods_id:goods_id},
    //            dataType: "json",
    //            success: function (data) {
    //                if(data.direct_url){
    //                    window.location.href = data.direct_url;
    //                }else{
    //                    alert('error');
    //                }
    //            },
    //        })
    //    }


    //    // 调用微信JS api 支付
    //    function jsApiCall(){
    //        var total_fee = $('.sumn').html();
    //        var type = $('#pay_type').val();
    //        var num = $(".btn2").val();
    //        var goods_type = $('#goods_type').val();
    //        var lottery_time = $(".fnTimeCountDown").data('end');
    //        var goods_id = '<?php echo I("get.id");?>';
    //        if(total_fee == ''  || type =='' || num =='' || lottery_time =='' || goods_id =='' || goods_type == ''){
    //            alert('操作异常');
    //            return false;
    //        }
    //
    //        $.ajax({
    //            type: 'post', //传送的方式,get/post
    //            url: '/Weixin/Goods/getJsApiParametersNo', //获取jsApiParameters
    //            data: {total_fee:total_fee,type:type,num:num,goods_type:goods_type,lottery_time:lottery_time,goods_id:goods_id},
    //            dataType: "json",
    //            success: function (data) {
    //
    //                if(data.jsApiParameters){
    //                    WeixinJSBridge.invoke('getBrandWCPayRequest',data.jsApiParameters,function(res){
    ////                        WeixinJSBridge.log(res.err_msg);
    ////                        alert(JSON.stringify(<?php echo ($jsApiParameters); ?>));
    ////                        alert(res.err_desc+res.err_msg);
    //                        var out_trade_no = data.out_trade_no;
    //                        if(res.err_msg.indexOf('ok')>0){
    //                            //支付成功后的操作
    //                            $.ajax({
    //                                type: 'post', //传送的方式,get/post
    //                                url: '/Weixin/Goods/doSthByPayment', //获取jsApiParameters
    //                                data:  {out_trade_no:out_trade_no},
    //                                dataType: "json",
    //                                success: function (result) {
    //                                    if(result.status == 1){
    //                                        //自动跳转
    //                                        var jump_url = '/Weixin/Goods/doByPay/id/' + result.order_id;
    //                                        window.location.href = jump_url;
    //                                    }else{
    //                                        alert('操纵异常');
    //                                    }
    //                                }
    //                            })
    //                        }
    //                    });
    //                }
    //            }
    //        })
    //    }
    //
    //    function callpay(){
    //        if (typeof WeixinJSBridge == "undefined"){
    //            return false;
    //            if( document.addEventListener ){
    //                document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
    //            }else if (document.attachEvent){
    //                document.attachEvent('WeixinJSBridgeReady', jsApiCall);
    //                document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
    //            }
    //        }else{
    //            jsApiCall();
    //        }
    //    }


    var flag = 0;

    function judge(){
        var myunix = "<?php echo ($data["myunix"]); ?>";
        var myjsunix = nowunix = Date.parse( new Date())/1000;

        if((myunix - myjsunix) < 30){
            alert('即将开奖，敬请期待下一期');
            return false;
        }

        var total_fee = $('.sumn').html();
        var account = "<?php echo ($price); ?>";
        console.log('account'+account);
        console.log('total_fee'+total_fee);

        var cha = parseInt(account - total_fee);
        console.log(cha);
        if(cha >= 0){
            if(flag == 1){
                alert('处理中...');
            }else{
                flag = 1;
                console.log(flag);

                var total_fee = $('.sumn').html();
                var type = $('#pay_type').val();
                var num = $(".btn2").val();
                var goods_type = $('#goods_type').val();
                var lottery_time = $(".fnTimeCountDown").data('end');
                var goods_id = '<?php echo I("get.id");?>';
                if(total_fee == ''  || type =='' || num =='' || lottery_time =='' || goods_id ==''){
                    alert('操作异常');
                    return false;
                }
                $.ajax({

                    type: 'post', //传送的方式,get/post
                    url: '/Weixin/Goods/makeOrder',
                    data: {total_fee:total_fee,type:type,num:num,goods_type:goods_type,lottery_time:lottery_time,goods_id:goods_id},
                    dataType: "json",
                    success: function (data) {
                        flag = 0;
                        if(data.status == 1){
                            alert('购买成功');
                            window.location.href = '/Weixin/Goods/doByPay/id/' + data.out_trade_no;

                        }else{
                            alert('操纵异常');
                        }
                    }
                });
            }
        }else{
            getpay();
        }

    }

    function getpay(){
        var myunix = "<?php echo ($data["myunix"]); ?>";
        var myjsunix = nowunix = Date.parse( new Date())/1000;

        if((myunix - myjsunix) < 30){
            alert('即将开奖，敬请期待下一期');
            return false;
        }		
		
        if (typeof WeixinJSBridge == "undefined"){
            if( document.addEventListener ){
                document.addEventListener('WeixinJSBridgeReady', topay, false);
            }else if (document.attachEvent){
                document.attachEvent('WeixinJSBridgeReady', topay);
                document.attachEvent('onWeixinJSBridgeReady', topay);
            }
        }else{
            topay();
        }
    }



    function topay(){
        var total_fee = $('.sumn').html();
        var type = $('#pay_type').val();
        var num = $(".btn2").val();
        var goods_type = $('#goods_type').val();
        var lottery_time = $(".fnTimeCountDown").data('end');
        var goods_id = '<?php echo I("get.id");?>';
        if(total_fee == ''  || type =='' || num =='' || lottery_time =='' || goods_id ==''){
            alert('操作异常,请选择购买数量！');
            return false;
        }
        $.ajax({
            type: 'post', //传送的方式,get/post
            url: '/Weixin/Request/submitOrderInfo',
            data: {total_fee:total_fee,type:type,num:num,goods_type:goods_type,lottery_time:lottery_time,goods_id:goods_id},
//            dataType: "json",
            success: function (res) {
                if(typeof(res) === 'string'){
                    res = JSON.parse(res);
                    var payInfo = JSON.parse(res.pay_info);
                    var orderid = JSON.parse(res.orderid);
                }

                WeixinJSBridge.invoke(
                        'getBrandWCPayRequest',{
                            "appId" : payInfo.appId, //公众号名称，由商户传入
                            "timeStamp": payInfo.timeStamp, //戳，自1970 年以来的秒数
                            "nonceStr" : payInfo.nonceStr, //随机串
                            "package" : payInfo.package,
                            "signType" : payInfo.signType, //微信签名方式:
                            "paySign" : payInfo.paySign  //微信签名,
                        },function(data){
                            //alert(data.err_desc+data.err_msg);
                            if(data.err_msg.indexOf('ok')>0){
                                // 此处可以使用此方式判断前端返回,微信团队郑重提示：res.err_msg 将在用户支付成功后返回ok，但并不保证它绝对可靠。
                                alert('购买成功');
                                var jump_url = '/Weixin/Goods/doByPay/id/' + orderid;
                                window.location.href = jump_url;
                            }
                        }
                );
            }
        });

    }

</script>

</body>
</html>