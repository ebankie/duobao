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
        <h2>加盟列表</h2>
    </div>



    <link href="/Public/static/datetimepicker/css/datetimepicker.css" rel="stylesheet" type="text/css">

    <?php if(C('COLOR_STYLE')=='blue_color') echo '<link href="/Public/static/datetimepicker/css/datetimepicker_blue.css" rel="stylesheet" type="text/css">'; ?>

    <link href="/Public/static/datetimepicker/css/dropdown.css" rel="stylesheet" type="text/css">

    <script type="text/javascript" src="/Public/static/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>

    <script type="text/javascript" src="/Public/static/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>



    <div class="cf">

        <!--<a class="btn" href="<?php echo U('Order/add');?>">新 增</a>-->

        <button class="btn ajax-post confirm" url="<?php echo U('del');?>" target-form="ids">删 除</button>

        <div class="search-form fr cf">

            <div class="sleft">

                <input type="hidden" name="status" value="<?php echo I('get.status');?>"/>

                <input type="text" name="title" id="title" class="search-input" value="<?php echo I('title');?>" placeholder="联系人姓名">

                <a class="sch-btn" href="javascript:;" id="search" url="<?php echo U('Join/index');?>"><i class="btn-search"></i></a>

            </div>

        </div>

    </div>









    <div class="data-table table-striped">



        <table>



            <thead>



            <tr>



                <th class="row-selected">



                    <input class="checkbox check-all" type="checkbox">



                </th>



                <th>ID</th>

                <th>企业名称</th>

                <th>联系人</th>

                <th>联系电话</th>

                <th>联系地址</th>

                <th>用户</th>

                <th>申请时间</th>

                <th>

                    <select name="status">

                        <option value="" <?php if(empty($_GET['status'])): ?>selected<?php endif; ?> >审核状态</option>

                        <option value="0" <?php if(($_GET['status']) == "0"): ?>selected<?php endif; ?> >待审核</option>

                        <option value="1" <?php if(($_GET['status']) == "1"): ?>selected<?php endif; ?> >审核通过</option>

                        <option value="2" <?php if(($_GET['status']) == "2"): ?>selected<?php endif; ?> >审核不通过</option>

                    </select>

                </th>



                <th>操作</th>

            </tr>



            </thead>



            <tbody>



            <?php if(!empty($list)): if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>



                        <td><input class="ids row-selected" type="checkbox" name="id[]" value="<?php echo ($vo["id"]); ?>"></td>



                        <td><?php echo ($vo["id"]); ?></td>



                        <!--<td><a title="编辑" href="<?php echo U('edit?id='.$order['id'].'&pid='.$pid);?>"><?php echo ($order["orderid"]); ?></a></td>-->

                        <td><?php echo ($vo["company"]); ?></td>

                        <td><?php echo ($vo["name"]); ?></td>

                        <td><?php echo ($vo["mobile"]); ?></td>

                        <td><?php echo ($vo["address"]); ?></td>

                        <td><?php echo ($vo["nickname"]); ?></td>

                        <td><?php echo (date('Y/m/d H:i:s',$vo["create_time"])); ?></td>



                        <td><?php if(($vo["status"] == 0)): ?>待审核

                            <?php elseif(($vo["status"] == 1)): ?>审核通过

                            <?php elseif(($vo["status"] == 2)): ?>审核不通过<?php endif; ?></td>





                        <td>



                            <a title="查看" href="<?php echo U('edit?id='.$vo['id']);?>">查看</a>



                            <a class="confirm ajax-get" title="删除" href="<?php echo U('del?id='.$vo['id']);?>">删除</a>



                        </td>



                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>



                <?php else: ?>



                <td colspan="6" class="text-center"> aOh! 暂时还没有内容! </td><?php endif; ?>



            </tbody>



        </table>







        <!-- 分页 -->



        <div class="page">



            <?php echo ($_page); ?>



        </div>



    </div>




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



        $('select[name=status]').change(function (){

            $('input[name=status]').val($(this).val());

            $("#search").click();

        })





        $(function(){

            $('#time-start').datetimepicker({

                format: 'yyyy-mm-dd',

                language:"zh-CN",

                minView:2,

                autoclose:true

            });

            $('#time-end').datetimepicker({

                format: 'yyyy-mm-dd',

                language:"zh-CN",

                minView:2,

                autoclose:true

            });

        })



        $(function() {



            //搜索功能



            $("#search").click(function() {



                var url = $(this).attr('url');



                var query = $('.search-form').find('input').serialize();



                query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g, '');



                query = query.replace(/^&/g, '');



                if (url.indexOf('?') > 0) {



                    url += '&' + query;



                } else {



                    url += '?' + query;



                }



                window.location.href = url;



            });



            //回车搜索



            $(".search-input").keyup(function(e) {



                if (e.keyCode === 13) {



                    $("#search").click();



                    return false;



                }



            });



            //导航高亮



            highlight_subnav('<?php echo U("Join/index");?>');



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



        $('#export').click(function(){

            var ids = $('.ids:checked');

            var title = $('#title').val();

            var start = $('#time-start').val();

            var end = $('#time-end').val();

            var url = '/control.php?s=/order/orderexport.html';

            if(ids.length > 0){

                var str = new Array();



                ids.each(function(){



                    str.push($(this).val());



                });



                param = str.join(',');

                url += '&ids='+param;

            }

            if(title){

                url += '&title='+title;

            }

            url += '&type=<?php echo ($hover); ?>';

            window.location.href= url;



        })



    </script>




</body>
</html>