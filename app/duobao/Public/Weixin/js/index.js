
(function(w){
	
	var $ = w.$ = function (str,parent){
		var doc = parent || document;
		return doc.querySelectorAll(str);
	}
	w.menu = function(){
		M.click($("#menuButon")[0],function(){
			if($("#article")[0].offsetLeft == 0){
				$("#article")[0].style.left = "-80%";
				$("#article")[0].style.webkitTransition = "left 0.5s";
				$("#mains")[0].style.left = "0%";
				$("#mains")[0].style.webkitTransition = "left 0.5s";
				$("body")[0].style.overflow = "auto";
				$("html")[0].ontouchmove = function(){
					return true;
				}
			}else{
				$("#article")[0].style.left = "0%";
				$("#article")[0].style.webkitTransition = "all 0.5s";
				$("#mains")[0].style.left = "80%";
				$("#mains")[0].style.webkitTransition = "left 0.5s";
				$("body")[0].style.overflow = "hidden";
				$("html")[0].ontouchmove = function(){
					return false;
				}
			}
		}).click(document,function(){
			if($("#article")[0].offsetLeft == 0){
				$("#article")[0].style.left = "-80%";
				$("#article")[0].style.webkitTransition = "left 0.5s";
				$("#mains")[0].style.left = "0%";
				$("#mains")[0].style.webkitTransition = "left 0.5s";
				$("body")[0].style.overflow = "auto";
				$("html")[0].ontouchmove = function(){
					return true;
				}
			}
		})
	}
	w.tab = function(){
		var divs = document.getElementsByClassName("tab")[0].getElementsByTagName("div");
		for(var i = 0; i < divs.length; i++){
			divs[i].index = i;
			divs[i].onclick = function(divs){
				var divs = document.getElementsByClassName("tab")[0].getElementsByTagName("div");
				var page = document.getElementsByClassName("page");
				for(var x = 0; x < divs.length; x++){	
					divs[x].className = "tabDiv_border";
					divs[x].getElementsByTagName("p")[0].className = "tabP";
					page[x].style.display = "none";
				}
				this.className = "tabP_select_border";
				this.getElementsByTagName("p")[0].className = "tabP tabP_select";
				divs[0].getElementsByTagName("p")[0].className += " border_none";
				page[this.index].style.display = "block";
			}
		}
	}
	w.M = {
		load : function(fn){
			document.addEventListener("DOMContentLoaded",fn,false);
			return this;
		},
		back : function(){
			History.back();
		},
		click : function(dom,fns){
			dom.onclick = function(){
				fns(dom);
			}
			return this;
		},
		touchStart : function(dom,fns){
			dom.ontouchstart = function(){
				fns(dom);
			}
			return this;
		},
		getStyle : function(dom,str){
			if(dom.currentStyle){
				return dom.currentStyle[str];
			}else{
				return window.getComputedStyle(dom,null)[str];
			}
			
		},
		lunbo : {
			dom : null,
			eles : [],
			fn : null,
			init : function(str,parent,fn){
				if(!str || !parent || typeof parent != "object")return false;
				var fn = fn || function(){};
				this.fn = fn;
				this.dom = parent;
				var imgs = $(str,parent);
				var w = document.body.width;
				this.eles = imgs;
				for(var i = 0; i < imgs.length; i++){
					imgs[i].style.width = w + "px";
				}
				parent.style.width = w * 3 + "px";
				this.start();
			},
			num : 1,
			timer : null,
			start : function(dom){
				clearInterval(this.timer);
				this.timer = setInterval(this.lunbo,5000);
			},
			asb : true,
			lunbo : function(num){
				_this = w.M.lunbo;
				var num = _this.num;
				var dom = _this.dom;
				var len = _this.eles.length;
				_this.fn(num);
				dom.style.marginLeft = "-" + (num*100) + "%";
				dom.style.webkitTransition = "all 2s";
				if(_this.asb){
					_this.num++;
					if(_this.num > len - 1){
						_this.asb = false;
						_this.num = (len - 1);
					}
				}else{
					_this.num--;
					if(_this.num < 0){
						_this.asb = true;
						_this.num = 0;
					}
				}
			},
			stop : function(){
				clearInterval(this.timer);
			}
		}
		
		
		
		
	}
	
	
	
	
	
	
	
	
})(window)

