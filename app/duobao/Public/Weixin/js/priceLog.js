$(function () {

    var socket = io('http://auction.akng.net:2120');   // 连接服务端
    socket.on('disconnect' , function () {
        layer.open({content : '服务器链接失败，请刷新重试' , btn : ['确定']});
        socket.emit('login' , uid);
    });
    socket.on('new_msg' , function (msg) {  // 后端推送来消息时
        //console.log(msg);
        var msg = JSON.parse(msg);
        if (msg.code == 1) {   //出价成功
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

        }
    });

});

