<?php
/**
 * 支付接口调测例子
 * ================================================================
 * index 进入口，方法中转
 * submitOrderInfo 提交订单信息
 * queryOrder 查询订单
 *
 * ================================================================
 */
namespace Weixin\Controller;
use Think\Controller;

Class RequestController extends HomeController{
    //$url = 'http://192.168.1.185:9000/pay/gateway';

    private $resHandler = null;
    private $reqHandler = null;
    private $pay = null;
    private $mynum ;

    public function _initialize(){
        header("Content-type: text/html; charset=utf-8");
        require_once('ThinkPHP/Library/Vendor/payInterface_jsapi_wx/class/ClientResponseHandler.class.php');
        require_once ('ThinkPHP/Library/Vendor/payInterface_jsapi_wx/class/RequestHandler.class.php');
        require_once ('ThinkPHP/Library/Vendor/payInterface_jsapi_wx/class/PayHttpClient.class.php');
        require_once ('ThinkPHP/Library/Vendor/payInterface_jsapi_wx/Utils.class.php');
        require_once ('ThinkPHP/Library/Vendor/payInterface_jsapi_wx/config/config.php');

//        $this->resHandler = new \ClientResponseHandler();

//        vendor('payInterface_jsapi_wx.Utils');
//        vendor('payInterface_jsapi_wx.class.RequestHandler');
//        vendor('payInterface_jsapi_wx.class.ClientResponseHandler');
//        vendor('payInterface_jsapi_wx.class.PayHttpClient');
        $this->Request();
    }

    public function Request(){
        $this->resHandler = new \ClientResponseHandler();
        $this->reqHandler = new \RequestHandler();
        $this->pay = new \PayHttpClient();
        $this->cfg = new \Config();
        if($_GET['t']){
            $this->cfg->num = $_GET['t'];
        }else{
            $num = substr($this->getPeriod($_POST['lottery_time']),-1)%5;//开奖期数

            $this->cfg->num = $this->mynum = $num;
        }

        $this->reqHandler->setGateUrl($this->cfg->C('url'));
        $this->reqHandler->setKey($this->cfg->C('key'));
    }

//    public function index(){
//        $method = isset($_REQUEST['method'])?$_REQUEST['method']:'submitOrderInfo';
//        switch($method){
//            case 'submitOrderInfo'://提交订单
//                $this->submitOrderInfo();
//                break;
//            case 'queryOrder'://查询订单
//                $this->queryOrder();
//                break;
//            case 'submitRefund'://提交退款
//                $this->submitRefund();
//                break;
//            case 'queryRefund'://查询退款
//                $this->queryRefund();
//                break;
//            case 'callback':
//                $this->callback();
//                break;
//        
//    }

    /**
     * 提交订单信息
     */
    public function submitOrderInfo(){
        //支付处理
//        $total_fee = $_POST['total_fee'];//总费用
        $type      = $_POST['type'];//1：小号码段   2：大号码段
        $num       = $_POST['num'];//购买数量
        $goods_type = $_POST['goods_type'];//商品类型 1：50元卡  2:100元卡
        $goods_id = $_POST['goods_id'];//商品id
        $lottery_time = $_POST['lottery_time'];//开奖时间

        if($goods_type == 1){
            $price  =  28;
        }elseif($goods_type == 2){
            $price  =  55;
        }
        $total_fee = $num*$price;
        //获取预支付信息
        //$paymentInfo     = R('Wxpay/getPayment_to' , array ($total_fee , $type));
        //dump($paymentInfo);die();
        //$jsApiParameters = $paymentInfo['jsApiParameters'];
        if($total_fee < 28){
            die('支付金额不正确!');
        }
        if(empty($type)){
            die('支付类型不能为空!');
        }

        // 1,获取openid
        $openid = $_SESSION['openid'];
        //$openid = 'oPlawwN7QTh_2Nqt8Gl7UmedjXaM';
        if($openid){
            $uid = M('Member')->where(array('openid'=>$openid))->getField('uid');
            $out_trade_no = 'FD-'.date('YmjHis').sprintf("%07d", $uid).$type.rand(1000,9999);//商户订单号
        }else{
            die('openid不能为空!');
        }

        //创建订单
        $data['uid'] = D('Member')->uid();
        $data['goods_id'] = $goods_id;
        $data['num'] = $num;
        $data['type'] = $type;
        $data['create_time'] = time();
        $data['order_number'] = $out_trade_no;
        $data['lottery_time'] = $lottery_time;
        $data['period'] = $this->getPeriod($lottery_time);//开奖期数
		
        //$data['ip_info'] = $this->getIpInfo();//用户的ip信息
		

        $goods_type = M('Document')->where(array('id'=>$goods_id))->getField('price');
        $data['goods_type'] = $goods_type;

        if($goods_type == 1){
            $data['money_w'] = $num*28;
            $data['money'] = $num*28;
            if($type == 1){
                $data['number_section'] = '1-28';
            }elseif($type == 2){
                $data['number_section'] = '29-56';
            }
        }elseif($goods_type == 2){
            $data['money_w'] = $num*55;
            $data['money'] = $num*55;
            if($type == 1){
                $data['number_section'] = '1-55';
            }elseif($type == 2){
                $data['number_section'] = '56-110';
            }
        }

        if($orderid = M('WinOrder')->add($data)){
            $arr['method'] = 'submitOrderInfo';
            $arr['out_trade_no'] = $out_trade_no;
            $arr['sub_openid'] = $openid;
            $arr['body'] = $out_trade_no;
//            $arr['total_fee'] = $data['money']*100;
            $arr['total_fee'] = 0.01*100;
            $arr['mch_create_ip'] = get_client_ip();
        }else{
            exit();
        }

        $this->reqHandler->setReqParams($arr,array('method'));
        $this->reqHandler->setParameter('service','pay.weixin.jspay');//接口类型
        $this->reqHandler->setParameter('mch_id',$this->cfg->C('mchId'));//必填项，商户号，由平台分配
        $this->reqHandler->setParameter('version',$this->cfg->C('version'));
        $this->reqHandler->setParameter('limit_credit_pay','1');
        $this->reqHandler->setParameter('appid','wx7fb456d4e2e698a4');
//        $this->reqHandler->setParameter('op_user_id','yida');
//        $this->reqHandler->setParameter('op_shop_id','yida1');
//        $this->reqHandler->setParameter('op_device_id','yida2');
//        $this->reqHandler->setParameter('device_info','yida3');
        //$this->reqHandler->setParameter('time_expire','20170306152200');
//        $this->reqHandler->setParameter('goods_tag',$arr['out_trade_no']);
        $this->reqHandler->setParameter('is_raw','1');
        //通知地址，必填项，接收平台通知的URL，需给绝对路径，255字符内格式如:http://wap.tenpay.com/tenpay.asp
        //$notify_url = 'http://'.$_SERVER['HTTP_HOST'];
        //$this->reqHandler->setParameter('notify_url',$notify_url.'/payInterface/request.php?method=callback');
        $this->reqHandler->setParameter('notify_url','http://duobao.akng.net/Weixin/Request/callback?t='.$this->mynum);//
        $this->reqHandler->setParameter('callback_url','http://duobao.akng.net/Weixin/My/orderDetail/id/'.$orderid);
        $this->reqHandler->setParameter('nonce_str',mt_rand(time(),time()+rand()));//随机字符串，必填项，不长于 32 位
//        $this->reqHandler->setParameter('sub_openid','o8DGQs1COe0UyuwgbvmxvJvnH-KA');
        $this->reqHandler->createSign();//创建签名

        $data = \Utils::toXml($this->reqHandler->getAllParameters());


        $this->pay->setReqContent($this->reqHandler->getGateURL(),$data);



        if($this->pay->call()){
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());

            if($this->resHandler->isTenpaySign()){
                //当返回状态与业务结果都为0时才返回，其它结果请查看接口文档
                if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0){
                    echo json_encode(array('token_id'=>$this->resHandler->getParameter('token_id'),
                        'pay_info'=>$this->resHandler->getParameter('pay_info'),
                        'orderid'=>$orderid));
                    exit();
                }else{
                    echo json_encode(array('status'=>500,'msg'=>'Error Code:'.$this->resHandler->getParameter('status').' Error Message:'.$this->resHandler->getParameter('message')));
                    exit();
                }
            }

            echo json_encode(array('status'=>500,'msg'=>'Error Code:'.$this->resHandler->getParameter('status').' Error Message:'.$this->resHandler->getParameter('message')));
        }else{
            echo json_encode(array('status'=>500,'msg'=>'Response Code:'.$this->pay->getResponseCode().' Error Info:'.$this->pay->getErrInfo()));
        }
    }


    /**
     * 充值
     */
    public function rechargeOrderInfo(){
        //支付处理
        $total_fee = $_POST['total_fee'];//总费用
        //获取预支付信息
        // 1,获取openid
        $openid = $_SESSION['openid'];
        //$openid = 'oPlawwN7QTh_2Nqt8Gl7UmedjXaM';
        if($openid){
            $uid = M('Member')->where(array('openid'=>$openid))->getField('uid');
            $out_trade_no = 'RE-'.date('YmjHis').sprintf("%07d", $uid).'3'.rand(1000,9999);//商户订单号
        }else{
            die('openid不能为空!');
        }

        //创建订单
        $data['uid'] = D('Member')->uid();
        $data['create_time'] = time();
        $data['total_fee'] = $total_fee;
        $data['out_trade_no'] = $out_trade_no;
		
        //$data['ip_info'] = $this->getIpInfo();//用户的ip信息


        if($orderid = M('RechargeOrder')->add($data)){
            $arr['method'] = 'submitOrderInfo';
            $arr['out_trade_no'] = $out_trade_no;
            $arr['sub_openid'] = $openid;
            $arr['body'] = $out_trade_no;
            //$arr['total_fee'] = $total_fee*100;
            $arr['total_fee'] = 0.01*100;
            $arr['mch_create_ip'] = get_client_ip();
        }else{
            exit();
        }

        $this->reqHandler->setReqParams($arr,array('method'));
        $this->reqHandler->setParameter('service','pay.weixin.jspay');//接口类型
        $this->reqHandler->setParameter('mch_id',$this->cfg->C('mchId'));//必填项，商户号，由平台分配
        $this->reqHandler->setParameter('version',$this->cfg->C('version'));
        $this->reqHandler->setParameter('limit_credit_pay','1');
        $this->reqHandler->setParameter('appid','wx7fb456d4e2e698a4');
//        $this->reqHandler->setParameter('op_user_id','yida');
//        $this->reqHandler->setParameter('op_shop_id','yida1');
//        $this->reqHandler->setParameter('op_device_id','yida2');
//        $this->reqHandler->setParameter('device_info','yida3');
        //$this->reqHandler->setParameter('time_expire','20170306152200');
//        $this->reqHandler->setParameter('goods_tag',$arr['out_trade_no']);
        $this->reqHandler->setParameter('is_raw','1');
        //通知地址，必填项，接收平台通知的URL，需给绝对路径，255字符内格式如:http://wap.tenpay.com/tenpay.asp
        //$notify_url = 'http://'.$_SERVER['HTTP_HOST'];
        //$this->reqHandler->setParameter('notify_url',$notify_url.'/payInterface/request.php?method=callback');
        $this->reqHandler->setParameter('notify_url','http://duobao.akng.net/Weixin/Request/callback?t='.$this->mynum);//
        $this->reqHandler->setParameter('callback_url','http://duobao.akng.net/Weixin/My/orderDetail/id/'.$orderid);
        $this->reqHandler->setParameter('nonce_str',mt_rand(time(),time()+rand()));//随机字符串，必填项，不长于 32 位
//        $this->reqHandler->setParameter('sub_openid','o8DGQs1COe0UyuwgbvmxvJvnH-KA');
        $this->reqHandler->createSign();//创建签名

        $data = \Utils::toXml($this->reqHandler->getAllParameters());


        $this->pay->setReqContent($this->reqHandler->getGateURL(),$data);



        if($this->pay->call()){
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());

            if($this->resHandler->isTenpaySign()){
                //当返回状态与业务结果都为0时才返回，其它结果请查看接口文档
                if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0){
                    echo json_encode(array('token_id'=>$this->resHandler->getParameter('token_id'),
                        'pay_info'=>$this->resHandler->getParameter('pay_info'),
                        'orderid'=>$orderid));
                    exit();
                }else{
                    echo json_encode(array('status'=>500,'msg'=>'Error Code:'.$this->resHandler->getParameter('status').' Error Message:'.$this->resHandler->getParameter('message')));
                    exit();
                }
            }

            echo json_encode(array('status'=>500,'msg'=>'Error Code:'.$this->resHandler->getParameter('status').' Error Message:'.$this->resHandler->getParameter('message')));
        }else{
            echo json_encode(array('status'=>500,'msg'=>'Response Code:'.$this->pay->getResponseCode().' Error Info:'.$this->pay->getErrInfo()));
        }

    }

    /**
     * 查询订单
     */
    public function queryOrder(){
        $this->reqHandler->setReqParams($_POST,array('method'));
        $reqParam = $this->reqHandler->getAllParameters();
        if(empty($reqParam['transaction_id']) && empty($reqParam['out_trade_no'])){
            echo json_encode(array('status'=>500,
                'msg'=>'请输入商户订单号,平台订单号!'));
            exit();
        }
        $this->reqHandler->setParameter('version',$this->cfg->C('version'));
        $this->reqHandler->setParameter('service','unified.trade.query');//接口类型
        $this->reqHandler->setParameter('mch_id',$this->cfg->C('mchId'));//必填项，商户号，由平台分配
        $this->reqHandler->setParameter('nonce_str',mt_rand(time(),time()+rand()));//随机字符串，必填项，不长于 32 位
        $this->reqHandler->createSign();//创建签名
        $data = Utils::toXml($this->reqHandler->getAllParameters());

        $this->pay->setReqContent($this->reqHandler->getGateURL(),$data);
        if($this->pay->call()){
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if($this->resHandler->isTenpaySign()){
                $res = $this->resHandler->getAllParameters();
                Utils::dataRecodes('查询订单',$res);
                //支付成功会输出更多参数，详情请查看文档中的7.1.4返回结果
                echo json_encode(array('status'=>200,'msg'=>'查询订单成功，请查看result.txt文件！','data'=>$res));
                exit();
            }
            echo json_encode(array('status'=>500,'msg'=>'Error Code:'.$this->resHandler->getParameter('status').' Error Message:'.$this->resHandler->getParameter('message')));
        }else{
            echo json_encode(array('status'=>500,'msg'=>'Response Code:'.$this->pay->getResponseCode().' Error Info:'.$this->pay->getErrInfo()));
        }
    }


    /**
     * 提交退款
     */
    public function submitRefund(){
        $this->reqHandler->setReqParams($_POST,array('method'));
        $reqParam = $this->reqHandler->getAllParameters();
        if(empty($reqParam['transaction_id']) && empty($reqParam['out_trade_no'])){
            echo json_encode(array('status'=>500,
                'msg'=>'请输入商户订单号或平台订单号!'));
            exit();
        }
        $this->reqHandler->setParameter('version',$this->cfg->C('version'));
        $this->reqHandler->setParameter('service','unified.trade.refund');//接口类型
        $this->reqHandler->setParameter('mch_id',$this->cfg->C('mchId'));//必填项，商户号，由平台分配
        $this->reqHandler->setParameter('nonce_str',mt_rand(time(),time()+rand()));//随机字符串，必填项，不长于 32 位
        $this->reqHandler->setParameter('op_user_id',$this->cfg->C('mchId'));//必填项，操作员帐号,默认为商户号

        $this->reqHandler->createSign();//创建签名
        $data = Utils::toXml($this->reqHandler->getAllParameters());//将提交参数转为xml，目前接口参数也只支持XML方式
        //var_dump($data);
        $this->pay->setReqContent($this->reqHandler->getGateURL(),$data);

        if($this->pay->call()){
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if($this->resHandler->isTenpaySign()){
                //当返回状态与业务结果都为0时才返，其它结果请查看接口文档
                if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0){
                    /*$res = array('transaction_id'=>$this->resHandler->getParameter('transaction_id'),
                                 'out_trade_no'=>$this->resHandler->getParameter('out_trade_no'),
                                 'out_refund_no'=>$this->resHandler->getParameter('out_refund_no'),
                                 'refund_id'=>$this->resHandler->getParameter('refund_id'),
                                 'refund_channel'=>$this->resHandler->getParameter('refund_channel'),
                                 'refund_fee'=>$this->resHandler->getParameter('refund_fee'),
                                 'coupon_refund_fee'=>$this->resHandler->getParameter('coupon_refund_fee'));*/
                    $res = $this->resHandler->getAllParameters();
                    Utils::dataRecodes('提交退款',$res);
                    echo json_encode(array('status'=>200,'msg'=>'退款成功,请查看result.txt文件！','data'=>$res));
                    exit();
                }else{
                    echo json_encode(array('status'=>300,'msg'=>'Error Code:'.$this->resHandler->getParameter('err_code').' Error Message:'.$this->resHandler->getParameter('err_msg')));
                    exit();
                }
            }
            echo json_encode(array('status'=>400,'msg'=>'Error Code:'.$this->resHandler->getParameter('status').' Error Message:'.$this->resHandler->getParameter('message')));
        }else{
            echo json_encode(array('status'=>500,'msg'=>'Response Code:'.$this->pay->getResponseCode().' Error Info:'.$this->pay->getErrInfo()));
        }
    }

    /**
     * 查询退款
     */
    public function queryRefund(){
        $this->reqHandler->setReqParams($_POST,array('method'));
        if(count($this->reqHandler->getAllParameters()) === 0){
            echo json_encode(array('status'=>500,
                'msg'=>'请输入商户订单号,平台订单号,商户退款单号,平台退款单号!'));
            exit();
        }
        $this->reqHandler->setParameter('version',$this->cfg->C('version'));
        $this->reqHandler->setParameter('service','unified.trade.refundquery');//接口类型
        $this->reqHandler->setParameter('mch_id',$this->cfg->C('mchId'));//必填项，商户号，由平台分配
        $this->reqHandler->setParameter('nonce_str',mt_rand(time(),time()+rand()));//随机字符串，必填项，不长于 32 位

        $this->reqHandler->createSign();//创建签名
        $data = Utils::toXml($this->reqHandler->getAllParameters());//将提交参数转为xml，目前接口参数也只支持XML方式

        $this->pay->setReqContent($this->reqHandler->getGateURL(),$data);//设置请求地址与请求参数
        if($this->pay->call()){
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if($this->resHandler->isTenpaySign()){
                //当返回状态与业务结果都为0时才返回，其它结果请查看接口文档
                if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0){
                    /*$res = array('transaction_id'=>$this->resHandler->getParameter('transaction_id'),
                                  'out_trade_no'=>$this->resHandler->getParameter('out_trade_no'),
                                  'refund_count'=>$this->resHandler->getParameter('refund_count'));
                    for($i=0; $i<$res['refund_count']; $i++){
                        $res['out_refund_no_'.$i] = $this->resHandler->getParameter('out_refund_no_'.$i);
                        $res['refund_id_'.$i] = $this->resHandler->getParameter('refund_id_'.$i);
                        $res['refund_channel_'.$i] = $this->resHandler->getParameter('refund_channel_'.$i);
                        $res['refund_fee_'.$i] = $this->resHandler->getParameter('refund_fee_'.$i);
                        $res['coupon_refund_fee_'.$i] = $this->resHandler->getParameter('coupon_refund_fee_'.$i);
                        $res['refund_status_'.$i] = $this->resHandler->getParameter('refund_status_'.$i);
                    }*/
                    $res = $this->resHandler->getAllParameters();
                    Utils::dataRecodes('查询退款',$res);
                    echo json_encode(array('status'=>200,'msg'=>'查询成功,请查看result.txt文件！','data'=>$res));
                    exit();
                }else{
                    echo json_encode(array('status'=>500,'msg'=>'Error Code:'.$this->resHandler->getParameter('message')));
                    exit();
                }
            }
            echo json_encode(array('status'=>500,'msg'=>$this->resHandler->getContent()));
        }else{
            echo json_encode(array('status'=>500,'msg'=>'Response Code:'.$this->pay->getResponseCode().' Error Info:'.$this->pay->getErrInfo()));
        }
    }

    /**
     * 后台异步回调通知
     */
    public function callback(){
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $this->resHandler->setContent($xml);
        //var_dump($this->resHandler->setContent($xml));
        $this->resHandler->setKey($this->cfg->C('key'));
        if($this->resHandler->isTenpaySign()){
            if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0){
                $out_trade_no = $this->resHandler->getParameter('out_trade_no');
                // 此处可以在添加相关处理业务，校验通知参数中的商户订单号out_trade_no和金额total_fee是否和商户业务系统的单号和金额是否一致，一致后方可更新数据库表中的记录。
                //更改订单状态
                $order_initials = substr($out_trade_no , 0 ,1);  //获取订单首字母
                if($out_trade_no){

                    switch($order_initials){

                        case 'F':  //购买
                            $orderData = M('WinOrder')->where(array('order_number'=>$out_trade_no))->find();
                            $time_end = $this->get_time_on_clock(time());//倒计时时间
                            $period = $this->getPeriod($time_end);//开奖期数
                            //如果订单状态为0，将其设置成1
                            if($orderData['status'] == 0){
                                $disbut_open = M('Config')->getFieldByName('DISTRIBUTION','value');//获取分销开启状态

                                $oneuid = M('Member')->getFieldByUid($orderData['uid'],'parent_id');
                                $join_user_id = M('Join')->where(array('is_delete'=>0,'status'=>1))->getField('uid',true);
                                if(in_array($oneuid,$join_user_id)){
                                    $ratio = M('Join')->where(array('is_delete'=>0,'status'=>1,'uid'=>$oneuid))->getField('ratio');
                                }else{
                                    $ratio = M('Config')->getFieldByName('DISTRIBUTION_PTC','value');//获取分销比率
                                }
								
								if($orderData['lottery_time'] != $time_end){
									$arr['lottery_time'] = $time_end;
									$arr['period'] = $period;
									$data['create_time'] = time();
								}
								$uid = $orderData['uid'];						
                                if($disbut_open && $oneuid){

                                    $arr['pid'] = $oneuid;
                                    $arr['money_p'] = $orderData['money'] * ($ratio * 0.01);

                                    $pmap['status'] = 1;
                                    $pmap['ratio'] = $ratio;
                                    $pmap['pid'] = $oneuid;
                                    $pmap['money_p'] = $orderData['money'] * ($ratio * 0.01);
                                    $pmap['out_trade_no'] = $out_trade_no;
                                    $pmap['uid'] = $orderData['uid'];
                                    $pmap['create_time'] = time();
                                    M('AccountLog')->add($pmap);
									
                                }

								$map['status'] = 2;
								$map['ratio'] = 0;
								$map['pid'] = 0;
								$map['money_p'] = $orderData['money'];
								$map['out_trade_no'] = $out_trade_no;
								$map['uid'] = $orderData['uid'];
								$map['create_time'] = time();
								M('AccountLog')->add($map);						
								
								$arr['status'] = 1;
								$arr['paytype'] = '微信支付';
								$arr['pay_time'] = time();
								M('WinOrder')->where(array('order_number'=>$out_trade_no))->save($arr);		

                            }
                            break;
                        case 'R': //充值
                            $orderinfo = M('RechargeOrder')->where(array('out_trade_no'=>$out_trade_no))->find();
                            if($orderinfo['status'] == 0){
                                M('RechargeOrder')->where(array('out_trade_no'=>$out_trade_no))->setField('status',1);
                                $map['status'] = 3;
                                $map['pid'] = 0;
                                $map['money_p'] = $orderinfo['total_fee'];
                                $map['out_trade_no'] = $out_trade_no;
                                $map['uid'] = $orderinfo['uid'];
                                $map['create_time'] = time();
                                M('AccountLog')->add($map);
                                $uid = $orderinfo['uid'];
								//更新当前用户资金
								$account = M('Member')->getFieldByUid($uid,'account');//当前会员原有资金
								M('Member')->where(array('uid'=>$uid))->setField('account',$account+$map['money_p']);									
								
                            }
                            break;
                    }

                    //将记录插入支付记录表
                    $data['out_trade_no']= $out_trade_no;
                    $data['uid']= $uid;
                    $data['create_time']= time();
                    $WinPayLog = M('WinPayLog');
                    $isExist = $WinPayLog->where(array('out_trade_no'=>$out_trade_no))->find();
                    if(!$isExist){
                        $result = $WinPayLog->add($data);
                    }

                    //M('Test')->add(array('name'=>$this->cfg->C('mchId')));

                }
                \Utils::dataRecodes('接口回调收到通知参数',$this->resHandler->getAllParameters());
                ob_clean();
                echo 'success';

            }else{
                echo 'failure1';
            }
        }else{
            echo 'failure2';
        }
    }
}


?>