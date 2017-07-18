// JavaScript Document
jQuery(function(){
	
	
	
		jQuery('.xl_p_pre').click(function(){
					var xl_shuziinput = parseInt(jQuery(this).siblings('.xl_p_input').val());
					if(xl_shuziinput>1)
					{
 					jQuery(this).siblings('.xl_p_input').val(xl_shuziinput-1);	
					}
					 
				})
				
				jQuery('.xl_p_next').click(function(){
					var xl_shuziinput = parseInt(jQuery(this).siblings('.xl_p_input').val());
 					jQuery(this).siblings('.xl_p_input').val(xl_shuziinput+1);	
 				})
	
	
	











































})





















