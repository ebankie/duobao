$(function () {

    var userId = parseInt($('#user_id').text()); //用户ID
    var isEnd  = parseInt($('#is_end').text());
    if (isEnd != 1) {
        updateEndTime();    //倒计时
    }

    $('.cj_button').click(function () { //点击提交
        var c = $('.cj_button').attr('data-c');
        if (c == 1) {   // 1 不能点击
            layer.open({content : '请输入价格' , btn : ['确定']});
            return false;
        }

        var myPrice = $('input[name=my_price]').val(); //获得价格

        if (myPrice == '' || myPrice == 0 || myPrice == '0') {
            layer.open({content : '请输入价格' , btn : ['确定']});
            return false;
        }

        if (isNaN(myPrice)) {
            layer.open({content : '价格必须为数字' , btn : ['确定']});
            return false;
        }
        var goodsId = parseInt($('#goods_id').text()); //商品ID
        var userId  = parseInt($('#user_id').text()); //用户ID
        if (myPrice == '') {
            layer.open({content : '请输入价格' , btn : ['确定']});
            return false;
        }
        if (isNaN(userId)) {
            layer.open({content : '用户ID异常' , btn : ['确定']});
            return false;
        }
        if (goodsId == '') {
            layer.open({content : '拍品ID异常' , btn : ['确定']});
            return false;
        }

        $('.cj_button').text('处理中...');
        $('.cj_button').attr('data-c' , '1');
        $.post("/Weixin/doBidding" , {uid : userId , goods_id : goodsId , price : myPrice} , function (data) {
            $('.cj_button').text('我要出价');
            $('.cj_button').attr('data-c' , '0');
        });

    });
    var socket = io('http://auction.akng.net:2120');   // 连接服务端
    uid        = userId;    // uid可以是自己网站的用户id，以便针对uid推送以及统计在线人数
    socket.on('connect' , function () { // socket连接后以uid登录
        socket.emit('login' , uid);
    });
    socket.on('disconnect' , function () {
        layer.open({content : '服务器链接失败，请刷新重试' , btn : ['确定']});
        socket.emit('login' , uid);
    });
    socket.on('new_msg' , function (msg) {  // 后端推送来消息时
        console.log(msg);
        var msg = JSON.parse(msg);
        if (msg.code == 4) {
            var goodsId = parseInt($('#goods_id').text()); //商品ID
            var userId  = parseInt($('#user_id').text()); //用户ID
            if (msg.data.goodsid == goodsId) {
                $('.timeTime').addClass('timeTimeOver');
                $('.timeTime img').attr('src' , '/Public/Weixin/images/over.png');
                $('.timeTime p t').text('已结束: 拍品已成交');
                $('.chujia').css('display' , 'none');
                $('.money_box').after('<div class="pd_20 winner mr_top_20"><p>拍品获得者</p><div>' + msg.data.mobile + '</div></div>');
            }

            if (msg.data.uid == userId) {   //当前用户弹窗
                $('#ouser_name').text(msg.data.ouser_name);
                $('#oprice').text(msg.data.max);
                $('#oname').text(msg.data.goods_name);
                $('#over').css('display' , 'block');
            }
        }
        if (msg.code == 0) {    //不能小于当前价格
            layer.open({content : msg.msg , btn : ['确定']});
        }
        if (msg.code == 3) {  //结束
            layer.open({content : msg.msg , btn : ['确定']});
        }
        if (msg.code == 33) {  //结束
            layer.open({content : msg.msg , btn : ['确定']});
        }
        if (msg.code == 1) {   //出价成功
            var goodsId = parseInt($('#goods_id').text()); //商品ID
            var userId  = parseInt($('#user_id').text()); //用户ID
            if (msg.goodsid == goodsId) {
                $('#nowPrice').html('当前价格: <span>￥' + msg.data.nowPrice + '</span>');   //更新价格
                var html = "";
                html += '<div class="recordTitle"><p>出价记录</p><div class="pd_l_20">' + msg.data.count + '条</div></div>';
                $.each(msg.data.biddingLog , function (n , value) {   //更新出价记录
                    if (n == 0) {
                        html += '<div class="box_rol_3 pd_tb_20 mr_rl_20 record_select_text_color">'
                    } else if ((n + 1) == msg.data.count) {
                        html += '<div class="box_rol_3 pd_tb_20 mr_rl_20 box_rol_3_bornone">'
                    } else {
                        html += '<div class="box_rol_3 pd_tb_20 mr_rl_20">'
                    }
                    html += '<div class="name">' + value.mobile + '</div><div class="time">' + value.create_at + '</div><div class="price">￥' + value.price + '</div>';
                    html += '</div>';
                });

                $('.record').html(html);
                if (msg.data.uid == userId) {   //当前用户弹窗
                    layer.open({content : msg.msg , btn : ['确定']});
                }

            }
        }
    });

    socket.on('update_online_count' , function (online_stat) {  // 后端推送来在线数据时
        //console.log(online_stat);
    });

});

t = 0;

//倒计时函数
function updateEndTime() {
    var date = new Date();
    var time = date.getTime();  //当前时间距1970年1月1日之间的毫秒数
    $(".end").each(function (i) {

        var endDate  = this.getAttribute("endTime"); //结束时间字符串
        var endDate1 = eval('new Date(' + endDate.replace(/\d+(?=-[^-]+$)/ , function (a) { return parseInt(a , 10) - 1; }).match(/\d+/g) + ')');   //转换为时间日期类型
        var endTime  = endDate1.getTime(); //结束时间毫秒数

        var startDate  = this.getAttribute("startTime"); //结束时间字符串
        var startDate1 = eval('new Date(' + startDate.replace(/\d+(?=-[^-]+$)/ , function (a) { return parseInt(a , 10) - 1; }).match(/\d+/g) + ')');   //转换为时间日期类型
        var startTime  = startDate1.getTime(); //开始时间的时间戳

        if (time <= startTime) {
            var lag    = (startTime - time) / 1000; //当前时间和结束时间之间的秒数
            var second = Math.floor(lag % 60);
            var minite = Math.floor((lag / 60) % 60);
            var hour   = Math.floor((lag / 3600) % 24);
            var day    = Math.floor((lag / 3600) / 24);
            $(this).html('距开拍: ' + day + "天" + hour + "小时" + minite + "分" + second + "秒");
        } else {
            var lag = (endTime - time) / 1000; //当前时间和结束时间之间的秒数
            if (lag > 0) {
                var second = Math.floor(lag % 60);
                var minite = Math.floor((lag / 60) % 60);
                var hour   = Math.floor((lag / 3600) % 24);
                var day    = Math.floor((lag / 3600) / 24);
                $(this).html('距结束: ' + day + "天" + hour + "小时" + minite + "分" + second + "秒");
            } else {
                t++;
                $('.money_one span').addClass('color');
                $('.timeTime').addClass('timeTimeOver');
                $('.timeTime img').attr('src' , '/Public/Weixin/img/over.png');
                if (t == 1) {   //执行成交操作
                    if ($('.winner').length >= 1) {
                        $(this).html("<t>已结束: 拍品已成交</t>");
                        var html = $('#nowPrice').html();
                        $('#nowPrice').html(html.replace(/当前价格/ , "成交价格"));
                    } else {
                        $(this).html("<t>已结束</t>");
                    }
                    var goodsId = parseInt($('#goods_id').text()); //商品ID
                    $.post("/Weixin/dofinish/" , {goods_id : goodsId});
                }
            }
        }

    });
    setTimeout("updateEndTime()" , 1000);
}

function fdate(format , timestamp) {
    var a , jsdate   = ((timestamp) ? new Date(timestamp * 1000) : new Date());
    var pad          = function (n , c) {
        if ((n = n + "").length < c) {
            return new Array(++c - n.length).join("0") + n;
        } else {
            return n;
        }
    };
    var txt_weekdays = ["Sunday" , "Monday" , "Tuesday" , "Wednesday" , "Thursday" , "Friday" , "Saturday"];
    var txt_ordin    = {1 : "st" , 2 : "nd" , 3 : "rd" , 21 : "st" , 22 : "nd" , 23 : "rd" , 31 : "st"};
    var txt_months   = [
        "" , "January" , "February" , "March" , "April" , "May" , "June" , "July" , "August" , "September" , "October" , "November" , "December"
    ];
    var f            = {
        // Day
        d : function () {return pad(f.j() , 2)} ,
        D : function () {return f.l().substr(0 , 3)} ,
        j : function () {return jsdate.getDate()} ,
        l : function () {return txt_weekdays[f.w()]} ,
        N : function () {return f.w() + 1} ,
        S : function () {return txt_ordin[f.j()] ? txt_ordin[f.j()] : 'th'} ,
        w : function () {return jsdate.getDay()} ,
        z : function () {return (jsdate - new Date(jsdate.getFullYear() + "/1/1")) / 864e5 >> 0} ,

        // Week
        W : function () {
            var a        = f.z() , b = 364 + f.L() - a;
            var nd2 , nd = (new Date(jsdate.getFullYear() + "/1/1").getDay() || 7) - 1;
            if (b <= 2 && ((jsdate.getDay() || 7) - 1) <= 2 - b) {
                return 1;
            } else {
                if (a <= 2 && nd >= 4 && a >= (6 - nd)) {
                    nd2 = new Date(jsdate.getFullYear() - 1 + "/12/31");
                    return date("W" , Math.round(nd2.getTime() / 1000));
                } else {
                    return (1 + (nd <= 3 ? ((a + nd) / 7) : (a - (7 - nd)) / 7) >> 0);
                }
            }
        } ,

        // Month
        F : function () {return txt_months[f.n()]} ,
        m : function () {return pad(f.n() , 2)} ,
        M : function () {return f.F().substr(0 , 3)} ,
        n : function () {return jsdate.getMonth() + 1} ,
        t : function () {
            var n;
            if ((n = jsdate.getMonth() + 1) == 2) {
                return 28 + f.L();
            } else {
                if (n & 1 && n < 8 || !(n & 1) && n > 7) {
                    return 31;
                } else {
                    return 30;
                }
            }
        } ,

        // Year
        L : function () {
            var y = f.Y();
            return (!(y & 3) && (y % 1e2 || !(y % 4e2))) ? 1 : 0
        } , //o not supported yet
        Y : function () {return jsdate.getFullYear()} ,
        y : function () {return (jsdate.getFullYear() + "").slice(2)} ,

        // Time
        a : function () {return jsdate.getHours() > 11 ? "pm" : "am"} ,
        A : function () {return f.a().toUpperCase()} ,
        B : function () {
            // peter paul koch:
            var off        = (jsdate.getTimezoneOffset() + 60) * 60;
            var theSeconds = (jsdate.getHours() * 3600) + (jsdate.getMinutes() * 60) + jsdate.getSeconds() + off;
            var beat       = Math.floor(theSeconds / 86.4);
            if (beat > 1000) beat -= 1000;
            if (beat < 0) beat += 1000;
            if ((String(beat)).length == 1) beat = "00" + beat;
            if ((String(beat)).length == 2) beat = "0" + beat;
            return beat;
        } ,
        g : function () {return jsdate.getHours() % 12 || 12} ,
        G : function () {return jsdate.getHours()} ,
        h : function () {return pad(f.g() , 2)} ,
        H : function () {return pad(jsdate.getHours() , 2)} ,
        i : function () {return pad(jsdate.getMinutes() , 2)} ,
        s : function () {return pad(jsdate.getSeconds() , 2)} , //u not supported yet

        // Timezone
        //e not supported yet
        //I not supported yet
        O : function () {
            var t = pad(Math.abs(jsdate.getTimezoneOffset() / 60 * 100) , 4);
            if (jsdate.getTimezoneOffset() > 0) t = "-" + t; else t = "+" + t;
            return t;
        } ,
        P : function () {
            var O = f.O();
            return (O.substr(0 , 3) + ":" + O.substr(3 , 2))
        } , //T not supported yet
        //Z not supported yet

        // Full Date/Time
        c : function () {return f.Y() + "-" + f.m() + "-" + f.d() + "T" + f.h() + ":" + f.i() + ":" + f.s() + f.P()} , //r not supported yet
        U : function () {return Math.round(jsdate.getTime() / 1000)}
    };

    return format.replace(/[\\]?([a-zA-Z])/g , function (t , s) {
        if (t != s) {
            // escaped
            ret = s;
        } else if (f[s]) {
            // a date function exists
            ret = f[s]();
        } else {
            // nothing special
            ret = s;
        }
        return ret;
    });
}