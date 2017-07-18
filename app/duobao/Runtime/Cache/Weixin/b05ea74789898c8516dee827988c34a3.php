<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>申请加盟</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">

    <link rel="stylesheet" href="/Public/Weixin/css/common.css">

    <link rel="stylesheet" href="/Public/Weixin/css/password.css">

</head>

<body>

<div class="mainall">

    <form role="form" action="/Weixin/My/join" method="post" name="formUser" id="formUser">

        <div class="set setipt">

            <input type="hidden" id="uid" value="<?php echo ($uid); ?>"   name="uid">

            <input type="text" id="company" value="<?php echo ($list["company"]); ?>"   name="company" placeholder="企业名称">

            <input type="text" id="name"  value="<?php echo ($list["name"]); ?>"  name="name" placeholder="联系人">

            <input type="text" id="mobile"  value="<?php echo ($list["mobile"]); ?>"  name="mobile" placeholder="联系电话">

            <input type="text" id="address"  value="<?php echo ($list["address"]); ?>"  name="address" placeholder="联系地址">
            
            <input type="text" id="kaihuhang"  value="<?php echo ($list["kaihuhang"]); ?>"  name="kaihuhang" placeholder="开户行">
            
            <input type="text" id="kahao"  value="<?php echo ($list["kahao"]); ?>"  name="kahao" placeholder="卡号">
            
            <input type="text" id="xingming"  value="<?php echo ($list["xingming"]); ?>"  name="xingming" placeholder="开户人姓名">

        </div>

    </form>

    <!--<p class="p1">忘记密码</p>-->

    <div class="btn">

        <button class="btn1" id="confirm" onclick="submitFormUser();">确认</button>

        <button class="btn2" onclick="reset();">重置</button>

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





<script>

    function reset(){

        $('#company').val('');

        $('#name').val('');

        $('#mobile').val('');

        $('#address').val('');

    }



    function submitFormUser(){

        var uid = $('#uid').val();

        var company = $('#company').val();

        var name = $('#name').val();

        var mobile = $('#mobile').val();

        var address = $('#address').val();

        var re = /^1\d{10}$/;

        if(uid == '' || company == ''  || name =='' || mobile =='' || address ==''){

            alert('数据不能为空');

            return false;

        }

        if(!re.test(mobile)){

            alert('请输入正确的手机号');

            return false;

        }

        $('#formUser').submit();//提交

    }

</script>

</body>

</html>