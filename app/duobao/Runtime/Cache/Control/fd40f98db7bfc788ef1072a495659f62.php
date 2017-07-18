<?php if (!defined('THINK_PATH')) exit();?><!doctype html>

<html>
<head>
<meta charset="UTF-8">
<title><?php echo ($meta_title); ?>|微信系统管理平台</title>
<link href="/Public/favicon.ico" type="image/x-icon" rel="shortcut icon">
<link rel="stylesheet" type="text/css" href="/Public/Control/css/base.css" media="all">
<link rel="stylesheet" type="text/css" href="/Public/Control/css/common.css" media="all">
<link rel="stylesheet" type="text/css" href="/Public/Control/css/module.css">
<link rel="stylesheet" type="text/css" href="/Public/Control/css/style.css" media="all">
<link rel="stylesheet" type="text/css" href="/Public/Control/css/<?php echo (C("COLOR_STYLE")); ?>.css" media="all">

<!--[if lt IE 9]>

    <script type="text/javascript" src="/Public/static/jquery-1.10.2.min.js"></script>

    <![endif]--><!--[if gte IE 9]><!-->

<script type="text/javascript" src="/Public/static/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="/Public/Control/js/jquery.mousewheel.js"></script>
<script type="text/javascript" src="/Public/Control/js/highcharts.js"></script>
<script type="text/javascript" src="/Public/Control/js/exporting.js"></script>
<script type="text/javascript" src="/Public/Control/js/data.js"></script>

<!--<![endif]-->


</head>

<body>

<!-- 头部 -->

<div class="header"> 

<div class="header_cen">
  
  <!-- Logo --> 
  
  <span class="logo"><img src="/Public/Control/images/logo.png"></span>
  
  <!-- /Logo --> 
  
  <!-- 主导航 -->
  
  <ul class="main-nav">
    <?php if(is_array($__MENU__["main"])): $i = 0; $__LIST__ = $__MENU__["main"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li class="<?php echo ((isset($menu["class"]) && ($menu["class"] !== ""))?($menu["class"]):''); ?>"><a href="<?php echo (get_nav_url($menu["url"])); ?>"><?php echo ($menu["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
<!--     <li><a href="<?php echo get_index_url();?>" target="_blank">网站首页</a></li> -->
  </ul>
  
  <!-- /主导航 --> 
  
  <!-- 用户栏 -->
  
  <div class="user-bar"> <a href="javascript:;" class="user-entrance"><i class="icon-user"></i></a>
    <ul class="nav-list user-menu hidden">
      <li class="manager">你好，<em title="<?php echo session('user_auth.username');?>"><?php echo session('user_auth.username');?></em></li>
      <li><a href="<?php echo U('Cache/delcache');?>">清除缓存</a></li>
      <li><a href="<?php echo U('Admin/updatePassword');?>">修改密码</a></li>
      <li><a href="<?php echo U('Admin/updateNickname');?>">修改昵称</a></li>
      <li><a href="<?php echo U('Public/logout');?>">退出</a></li>
    </ul>
  </div>
  <div class="cls"></div>
</div>  
  
</div>

<!-- /头部 --> 

<!-- 边栏 -->

<div class="sidebar"> 
  
  <!-- 子导航 -->
  
  
    <div id="subnav" class="subnav">
      <?php if(!empty($_extra_menu)): ?>
        
        <?php echo extra_menu($_extra_menu,$__MENU__); endif; ?>
      <?php if(is_array($__MENU__["child"])): $i = 0; $__LIST__ = $__MENU__["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub_menu): $mod = ($i % 2 );++$i;?><!-- 子导航 -->
        
        <?php if(!empty($sub_menu)): if(!empty($key)): ?><h3><i class="icon icon-unfold"></i><?php echo ($key); ?></h3><?php endif; ?>
          <ul class="side-sub-menu">
            <?php if(is_array($sub_menu)): $i = 0; $__LIST__ = $sub_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li> <a class="item" href="<?php echo (U($menu["url"])); ?>"><?php echo ($menu["title"]); ?></a> </li><?php endforeach; endif; else: echo "" ;endif; ?>
          </ul><?php endif; ?>
        
        <!-- /子导航 --><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
  
  
  <!-- /子导航 --> 
  
</div>

<!-- /边栏 --> 

<!-- 内容区 -->

<div id="main-content">
  <div id="top-alert" class="fixed alert alert-error" style="display: none;">
    <button class="close fixed" style="margin-top: 4px;">&times;</button>
    <div class="alert-content">这是内容</div>
  </div>
  <div id="main" class="main">
     
      
      <!-- nav -->
      
      <?php if(!empty($_show_nav)): ?><div class="breadcrumb"> <span>您的位置:</span>
          <?php $i = '1'; ?>
          <?php if(is_array($_nav)): foreach($_nav as $k=>$v): if($i == count($_nav)): ?><span><?php echo ($v); ?></span>
              <?php else: ?>
              <span><a href="<?php echo ($k); ?>"><?php echo ($v); ?></a>&gt;</span><?php endif; ?>
            <?php $i = $i+1; endforeach; endif; ?>
        </div><?php endif; ?>
      
      <!-- nav --> 
      
    
    

	<div class="main-title">

		<h2>微信配置</h2>

	</div>

	<form action="<?php echo U();?>" method="post" class="form-horizontal">
		<div class="form-item">
			<label class="item-label">微信公众号名称<span class="check-tips"></span></label>
			<div class="controls">
				<input type="text" class="text input-large" name="name" value="<?php echo ((isset($info["name"]) && ($info["name"] !== ""))?($info["name"]):''); ?>">
			</div>
		</div>
		<div class="form-item">
			<label class="item-label">URL(服务器地址)<span class="check-tips">（填写至微信平台服务器配置）</span></label>
			<div class="controls">
				<?php echo ($info["url"]); ?>
			</div>
		</div>
		<div class="form-item">
			<label class="item-label">Token(令牌)<span class="check-tips">（可自定义设置，填写至微信平台服务器配置）</span></label>
			<div class="controls">
            	<input type="text" class="text input-large" name="token" value="<?php echo ((isset($info["token"]) && ($info["token"] !== ""))?($info["token"]):''); ?>">
			</div>
		</div>   
		<div class="form-item">
			<label class="item-label">AppID(应用ID)<span class="check-tips"></span></label>
			<div class="controls">
				<input type="text" class="text input-large" name="appid" value="<?php echo ((isset($info["appid"]) && ($info["appid"] !== ""))?($info["appid"]):''); ?>">
			</div>
		</div>
		<div class="form-item">
			<label class="item-label">AppSecret(密钥)<span class="check-tips"></span></label>
			<div class="controls">
				<input type="text" class="text input-large" name="appsecret" value="<?php echo ((isset($info["appsecret"]) && ($info["appsecret"] !== ""))?($info["appsecret"]):''); ?>">
			</div>
		</div>
		
			<div class="form-item">
			<label class="item-label">微信支付商户号<span class="check-tips"></span></label>
			<div class="controls">
				<input type="text" class="text input-large" name="mchid" value="<?php echo ((isset($info["mchid"]) && ($info["mchid"] !== ""))?($info["mchid"]):''); ?>">
			</div>
		</div>
			<div class="form-item">
			<label class="item-label">微信商户支付Key<span class="check-tips"></span></label>
			<div class="controls">
				<input type="text" class="text input-large" name="key" value="<?php echo ((isset($info["key"]) && ($info["key"] !== ""))?($info["key"]):''); ?>">
			</div>
		</div>

		<div class="form-item">
			<label class="item-label">网页授权域名设置<span class="check-tips">(不带http://如：www.xxxx.com;与微信平台授权回调页面域名一致，一般情况不做修改)</span></label>
			<div class="controls">
				<input type="text" class="text input-large" name="authurl" value="<?php echo ((isset($info["authurl"]) && ($info["authurl"] !== ""))?($info["authurl"]):''); ?>">
			</div>
		</div>     
        
		<div class="form-item">

			<input type="hidden" name="id" value="<?php echo ((isset($info["id"]) && ($info["id"] !== ""))?($info["id"]):''); ?>">

			<button class="btn submit-btn ajax-post" id="submit" type="submit" target-form="form-horizontal">确 定</button>

			<button class="btn btn-return" onclick="javascript:history.back(-1);return false;">返 回</button>

		</div>

	</form>


  </div>
  <div class="cont-ft">
    <div class="copyright">
      <div class="fl">感谢使用微信管理系统</div>
      <div class="fr">V<?php echo (ONETHINK_VERSION); ?></div>
    </div>
  </div>
</div>

<!-- /内容区 --> 

<script type="text/javascript">

    (function(){

        var ThinkPHP = window.Think = {

            "ROOT"   : "", //当前网站地址

            "APP"    : "/index.php?s=", //当前项目地址

            "PUBLIC" : "/Public", //项目公共目录地址

            "DEEP"   : "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符

            "MODEL"  : ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],

            "VAR"    : ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]

        }

    })();

    </script> 
<script type="text/javascript" src="/Public/static/think.js"></script> 
<script type="text/javascript" src="/Public/Control/js/common.js"></script> 
<script type="text/javascript">

        +function(){

            var $window = $(window), $subnav = $("#subnav"), url;

            $window.resize(function(){

                $("#main").css("min-height", $window.height() - 130);

            }).resize();



            /* 左边菜单高亮 */

            url = window.location.pathname + window.location.search;

            url = url.replace(/(\/(p)\/\d+)|(&p=\d+)|(\/(id)\/\d+)|(&id=\d+)|(\/(group)\/\d+)|(&group=\d+)/, "");

			

            $subnav.find("a[href='" + url + "']").parent().addClass("current") ;



            /* 左边菜单显示收起 */

            $("#subnav").on("click", "h3", function(){

                var $this = $(this);

                $this.find(".icon").toggleClass("icon-fold");

                $this.next().slideToggle("fast").siblings(".side-sub-menu:visible").

                      prev("h3").find("i").addClass("icon-fold").end().end().hide();

            });



            $("#subnav h3 a").click(function(e){e.stopPropagation()});



            /* 头部管理员菜单 */

            $(".user-bar").mouseenter(function(){

                var userMenu = $(this).children(".user-menu ");

                userMenu.removeClass("hidden");

                clearTimeout(userMenu.data("timeout"));

            }).mouseleave(function(){

                var userMenu = $(this).children(".user-menu");

                userMenu.data("timeout") && clearTimeout(userMenu.data("timeout"));

                userMenu.data("timeout", setTimeout(function(){userMenu.addClass("hidden")}, 100));

            });



	        /* 表单获取焦点变色 */

	        $("form").on("focus", "input", function(){

		        $(this).addClass('focus');

	        }).on("blur","input",function(){

				        $(this).removeClass('focus');

			        });

		    $("form").on("focus", "textarea", function(){

			    $(this).closest('label').addClass('focus');

		    }).on("blur","textarea",function(){

			    $(this).closest('label').removeClass('focus');

		    });



            // 导航栏超出窗口高度后的模拟滚动条

            var sHeight = $(".sidebar").height();

            var subHeight  = $(".subnav").height();

            var diff = subHeight - sHeight; //250

            var sub = $(".subnav");

            if(diff > 0){

                $(window).mousewheel(function(event, delta){

                    if(delta>0){

                        if(parseInt(sub.css('marginTop'))>-10){

                            sub.css('marginTop','0px');

                        }else{

                            sub.css('marginTop','+='+10);

                        }

                    }else{

                        if(parseInt(sub.css('marginTop'))<'-'+(diff-10)){

                            sub.css('marginTop','-'+(diff-10));

                        }else{

                            sub.css('marginTop','-='+10);

                        }

                    }

                });

            }

        }();

    </script>


<script type="text/javascript">

$(function(){

	//搜索功能

	$("#search").click(function(){

		var url = $(this).attr('url');

        var query  = $('.search-form').find('input').serialize();

        query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g,'');

        query = query.replace(/^&/g,'');

        if( url.indexOf('?')>0 ){

            url += '&' + query;

        }else{

            url += '?' + query;

        }

		window.location.href = url;

	});

	//回车搜索

	$(".search-input").keyup(function(e){

		if(e.keyCode === 13){

			$("#search").click();

			return false;

		}

	});

	//点击排序

	$('.list_sort').click(function(){

		var url = $(this).attr('url');

		var ids = $('.ids:checked');

		var param = '';

		if(ids.length > 0){

			var str = new Array();

			ids.each(function(){

				str.push($(this).val());

			});

			param = str.join(',');

		}



		if(url != undefined && url != ''){

			window.location.href = url + '/ids/' + param;

		}

	});

});

</script>


</body>
</html>