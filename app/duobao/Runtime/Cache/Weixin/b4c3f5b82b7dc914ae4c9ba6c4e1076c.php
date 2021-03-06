<?php if (!defined('THINK_PATH')) exit();?>﻿<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>玩法规则</title>
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <link rel="stylesheet" href="/Public/Weixin/css/common.css">
    <style>
        .z-header{
            width: 100%;
            height: 1.5rem;
            line-height: 1.5rem;
            text-align: center;
            font-size: 0.5rem;
            color: #db4352;
        }
        .tr-p{
            width: 100%;
            height: 0.8rem;
            background-color:rgba(219,66,82,1);
            padding: 0 0.3rem;
            line-height: 0.8rem;
            font-size: 0.34rem;
            color: #fff;
        }
        .tr-img{
            width: 90%;
            height: auto;
            margin-left: 5%;
            margin-top: 0.2rem;
            margin-bottom: 0.4rem;
        }
    </style>
</head>
<body>
<div class="z-header">PK规则</div>
<p class="tr-p">本期号码÷本期商品所消耗的微币数量所得余数加上1</p>
<img class="tr-img" src="/Public/Weixin/images/rule.png"/>
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