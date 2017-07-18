// JavaScript Document



$(function(){
	

	viewRest();
	window.onresize = viewRest;

	function viewRest(){
		var fontWidth = $('body').width()/10 + 'px';
		$('html').css({
			fontSize	:	fontWidth	
		})
	}

	$(".zx p a").mouseover(function(){
   	$(this).addClass("hover").parents('p').siblings('p').children('a').removeClass("hover");
	var thishover=$(this);
 	$(".table .tab").eq($(".zx p a").index(thishover)).show().siblings().hide();
 	})

	 
	
	
	
	
	
	})