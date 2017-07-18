	viewRest();
	window.onresize = viewRest;

		function viewRest(){
    var oHTML = document.getElementsByTagName("html")[0];
    oHTML.style.fontSize = document.documentElement.clientWidth/10 + "px";}
	
	
	
	$(function(){
		$('.button-label p input:checked').parents('label').addClass('hover');	
		//赋给被选中 input 父级label 一个类hover
		
		$('.button-label p input').click(function(){
			if($(this).is(':checked'))
			{
				$(this).parents('label').addClass('hover');	
			}
			else
			{
				$(this).parents('label').removeClass('hover');	
			}	
		})
		
	})
