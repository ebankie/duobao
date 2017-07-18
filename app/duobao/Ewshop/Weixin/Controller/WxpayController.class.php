<?php
namespace Weixin\Controller;
use Think\Controller;
class WxpayController extends Controller {

    private $wxpayConfig;
    private $wxpay;

    public function _initialize(){
        header("Content-type: text/html; charset=utf-8");
        vendor('Wxpay.jsapi.WxPaypubconfig');
        vendor('Wxpay.jsapi.WxPayPubHelper');
        vendor('Wxpay.jsapi.demo.log_');
        vendor('Wxpay.jsapi.SDKRuntimeException');
        $this->wxpayConfig = array(
            'SSLCERT_PATH' => __ROOT__ . THINK_PATH . 'Library/Vendor/Wxpay/jsapi/cacert/apiclient_cert.pem',        // 证书路径,注意应该填写绝对路径
            'SSLKEY_PATH' => __ROOT__ . THINK_PATH . 'Library/Vendor/Wxpay/jsapi/cacert/apiclient_key.pem',          // 证书路径,注意应该填写绝对路径
            'CURL_TIMEOUT' => 30
        );
        $wxconfig = M('wxsetting')->where('id = 1')->find();

        $this->wxpayConfig['APPID'] = $wxconfig['appid'];      // 微信公众号身份的唯一标识
        $this->wxpayConfig['APPSECRET'] = $wxconfig['appsecret']; // JSAPI接口中获取openid
        $this->wxpayConfig['MCHID'] = $wxconfig['mchid'];     // 受理商ID
        $this->wxpayConfig['SKEY'] = $wxconfig['key'];       // 商户支付密钥Key


        $this->wxpayConfig['js_api_call_url'] = $this->get_url();
        $this->wxpayConfig['notifyurl'] = $_SERVER['SERVER_NAME'].U('/Weixin/Wxpay/Paynotify');//完整地址路径
        $this->wxpayConfig['returnurl'] = U('/Weixin/My');

        $wxpaypubconfig = new \WxPayConf_pub($this->wxpayConfig);    // 初始化WxPayConf_pub

    }

    /**
     * 获取当前页面完整URL地址
     */
    private function get_url() {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : $path_info);
        return $sys_protocal . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $relate_url;
    }


    /**
     *  获取openid
     */
    private function get_openid() {
        $openid = $_SESSION['openid'];
        if(empty($openid)) {
            // 使用jsapi接口
            $jsApi = new \JsApi_pub();
            // 通过code获得openid
            if (!isset($_GET['code'])) {
                // 触发微信返回code码
                $url = $jsApi->createOauthUrlForCode(\WxPayConf_pub::$JS_API_CALL_URL);
                Header("Location: " . $url);
            } else {
                // 获取code码,以获取openid
                $code = $_GET['code'];
                $jsApi->setCode($code);
                $openid = $jsApi->getOpenId();
                setcookie('apiopenid', $openid, time() + 86400);
            }
        }
        return $openid;
    }


    /**
     *  获取支付的信息
     * $total_fee  支付金额
     * $payType    支付类型(1:买小号段,2:买大号段)
     */
    public function getPayment($total_fee,$payType) {
        if($total_fee < 28){
            die('支付金额不正确!');
        }
        if(empty($payType)){
            die('支付类型不能为空!');
        }

        // 1,获取openid
        $openid = $this->get_openid();
        if($openid){
            $uid = M('Member')->where(array('openid'=>$openid))->getField('uid');
            $out_trade_no = 'FD-'.date('YmjHis').sprintf("%07d", $uid).$payType.rand(1000,9999);//商户订单号
        }else{
            die('openid不能为空!');
        }

        // 2,使用统一支付接口
        $unifiedOrder = new \UnifiedOrder_pub();

        // 设置统一支付接口参数
        // 设置必填参数
        // appid已填,商户无需重复填写
        // mch_id已填,商户无需重复填写
        // noncestr已填,商户无需重复填写
        // spbill_create_ip已填,商户无需重复填写
        // sign已填,商户无需重复填写
        $unifiedOrder->setParameter("openid", $openid);
        $unifiedOrder->setParameter("body", $out_trade_no);                      // 商品描述
        // 自定义订单号,此处仅作举例
        $unifiedOrder->setParameter("out_trade_no", $out_trade_no);              // 商户订单号
        $unifiedOrder->setParameter("total_fee", $total_fee*100);              				 // 总金额
        $unifiedOrder->setParameter("notify_url", \WxPayConf_pub::$NOTIFY_URL);  // 通知地址
        $unifiedOrder->setParameter("trade_type", "JSAPI");                      // 交易类型
        // 非必填参数,商户可根据实际情况选填
        //$unifiedOrder->setParameter("sub_mch_id", "XXXX");                     // 子商户号
        //$unifiedOrder->setParameter("device_info", "XXXX");                    // 设备号
        //$unifiedOrder->setParameter("attach", "XXXX");                         // 附加数据
        //$unifiedOrder->setParameter("time_start", "XXXX");                     // 交易起始时间
        //$unifiedOrder->setParameter("time_expire", "XXXX");                    // 交易结束时间
        //$unifiedOrder->setParameter("goods_tag", "XXXX");                      // 商品标记
        //$unifiedOrder->setParameter("openid", "XXXX");                         // 用户标识
        //$unifiedOrder->setParameter("product_id", "XXXX");                     // 商品ID

        $prepay_id = $unifiedOrder->getPrepayId();

        // 3,使用jsapi调起支付
        $jsApi = new \JsApi_pub();
        $jsApi->setPrepayId($prepay_id);
        $data['jsApiParameters'] = $jsApi->getParameters();
        $data['out_trade_no']    = $out_trade_no;

        return $data;

    }


    /**
     *  获取支付的信息
     * $total_fee  支付金额
     */
    public function getPayment_account($total_fee) {
        // 1,获取openid
        $openid = $this->get_openid();
        if($openid){
            $uid = M('Member')->where(array('openid'=>$openid))->getField('uid');
            $out_trade_no = 'RE-'.date('YmjHis').sprintf("%07d", $uid).'3'.rand(1000,9999);//商户订单号
        }else{
            die('openid不能为空!');
        }

        // 2,使用统一支付接口
        $unifiedOrder = new \UnifiedOrder_pub();

        // 设置统一支付接口参数
        // 设置必填参数
        // appid已填,商户无需重复填写
        // mch_id已填,商户无需重复填写
        // noncestr已填,商户无需重复填写
        // spbill_create_ip已填,商户无需重复填写
        // sign已填,商户无需重复填写
        $unifiedOrder->setParameter("openid", $openid);
        $unifiedOrder->setParameter("body", $out_trade_no);                      // 商品描述
        // 自定义订单号,此处仅作举例
        $unifiedOrder->setParameter("out_trade_no", $out_trade_no);              // 商户订单号
        $unifiedOrder->setParameter("total_fee", $total_fee*100);              				 // 总金额
        $url = $unifiedOrder->setParameter("notify_url", \WxPayConf_pub::$NOTIFY_URL);  // 通知地址
        $unifiedOrder->setParameter("trade_type", "JSAPI");                      // 交易类型
        // 非必填参数,商户可根据实际情况选填
        //$unifiedOrder->setParameter("sub_mch_id", "XXXX");                     // 子商户号
        //$unifiedOrder->setParameter("device_info", "XXXX");                    // 设备号
        //$unifiedOrder->setParameter("attach", "XXXX");                         // 附加数据
        //$unifiedOrder->setParameter("time_start", "XXXX");                     // 交易起始时间
        //$unifiedOrder->setParameter("time_expire", "XXXX");                    // 交易结束时间
        //$unifiedOrder->setParameter("goods_tag", "XXXX");                      // 商品标记
        //$unifiedOrder->setParameter("openid", "XXXX");                         // 用户标识
        //$unifiedOrder->setParameter("product_id", "XXXX");                     // 商品ID

        $prepay_id = $unifiedOrder->getPrepayId();

        // 3,使用jsapi调起支付
        $jsApi = new \JsApi_pub();
        $jsApi->setPrepayId($prepay_id);
        $data['jsApiParameters'] = $jsApi->getParameters();
        $data['out_trade_no']    = $out_trade_no;

        return $data;

    }


    /**
     *  服务器异步通知页面路径
     */
    public function Paynotify() {

        /**
         * 通用通知接口demo
         * ====================================================
         * 支付完成后，微信会把相关支付和用户信息发送到商户设定的通知URL，
         * 商户接收回调信息后，根据需要设定相应的处理流程。
         *
         * 这里举例使用log文件形式记录回调信息。
         */
        // 使用通用通知接口
        $notify = new \Notify_pub();

        // 存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $notify->saveData($xml);

        // 验证签名,并回应微信。
        // 对后台通知交互时,如果微信收到商户的应答不是成功或超时,微信认为通知失败，
        // 微信会通过一定的策略（如30分钟共8次）定期重新发起通知
        // 尽可能提高通知的成功率,但微信不保证通知最终能成功。
        if($notify->checkSign() == FALSE){
            $notify->setReturnParameter("return_code", "FAIL");      // 返回状态码
            $notify->setReturnParameter("return_msg", "签名失败");    // 返回信息
        } else {
            $notify->setReturnParameter("return_code", "SUCCESS");   // 设置返回码
            $notify->setReturnParameter("return_msg", "OK");   // 设置返回码
        }
        $returnXml = $notify->returnXml();
        echo $returnXml;

        //==商户根据实际情况设置相应的处理流程，此处仅作举例=======

        if($notify->checkSign() == TRUE) {
            if ($notify->data["return_code"] == "FAIL") {
                // 此处应该更新一下订单状态,商户自行增删操作
                //$log_->log_result($log_name, "【通信出错】:\n" . $xml . "\n");
            } elseif ($notify->data["result_code"] == "FAIL"){
                // 此处应该更新一下订单状态,商户自行增删操作
                //$log_->log_result($log_name, "【业务出错】:\n" . $xml . "\n");
            } else {
                // 此处应该更新一下订单状态,商户自行增删操作
                $returnData = $notify->getData();
                $out_trade_no = $returnData['out_trade_no'];

                $order_initials = substr($out_trade_no , 0 ,1);  //获取订单首字母

                if($out_trade_no){

                    switch($order_initials){

                        case 'F':  //购买
                            $orderData = M('WinOrder')->where(array('order_number'=>$out_trade_no))->find();
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
                                if($disbut_open && $oneuid){
                                    $arr['status'] = 1;
                                    $arr['pid'] = $oneuid;
                                    $arr['money_p'] = $orderData['money'] * ($ratio * 0.01);
                                    $arr['paytype'] = '微信支付';
                                    M('WinOrder')->where(array('order_number'=>$out_trade_no))->save($arr);
                                    $map['status'] = 1;
                                    $map['ratio'] = $ratio;
                                    $map['pid'] = $oneuid;
                                    $map['money_p'] = $orderData['money'] * ($ratio * 0.01);
                                    $map['out_trade_no'] = $out_trade_no;
                                    $map['uid'] = $orderData['uid'];
                                    $map['create_time'] = time();
                                    M('AccountLog')->add($map);
                                }else{
                                    $arr['status'] = 1;
                                    $arr['paytype'] = '微信支付';
                                    M('WinOrder')->where(array('order_number'=>$out_trade_no))->save($arr);
                                    $map['status'] = 2;
                                    $map['ratio'] = 0;
                                    $map['pid'] = 0;
                                    $map['money_p'] = $orderData['money'];
                                    $map['out_trade_no'] = $out_trade_no;
                                    $map['uid'] = $orderData['uid'];
                                    $map['create_time'] = time();
                                    M('AccountLog')->add($map);

                                }

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
                            }
                            break;
                    }


                }




//                $orderData = M('WinOrder')->where(array('order_number'=>$out_trade_no))->find();
//                //如果订单状态为0，将其设置成1
//                if($orderData['status'] == 0){
//                    $disbut_open = M('Config')->getFieldByName('DISTRIBUTION','value');//获取分销开启状态
//                    $ratio = M('Config')->getFieldByName('DISTRIBUTION_PTC','value');//获取分销开启状态
//                    $oneuid = M('Member')->getFieldByUid($orderData['uid'],'parent_id');
//                    if($disbut_open && $oneuid){
//                        $map['status'] = 1;
//                        $map['pid'] = $oneuid;
//                        $map['money_p'] = $orderData['money'] * ($ratio * 0.01);
//                        M('WinOrder')->where(array('order_number'=>$out_trade_no))->save($map);
//                        $map['out_trade_no'] = $out_trade_no;
//                        $map['uid'] = $orderData['uid'];
//                        $map['create_time'] = time();
//                        M('AccountLog')->add($map);
//                    }else{
//                        M('WinOrder')->where(array('order_number'=>$out_trade_no))->setField('status',1);
//                    }
//
//                }



                //将记录插入支付记录表
                $data['out_trade_no']= $out_trade_no;
                $data['uid']= M('Member')->where(array('openid'=>$returnData["openid"]))->getField('uid');
                $data['create_time']= time();
                $WinPayLog = M('WinPayLog');
                $isExist = $WinPayLog->where(array('out_trade_no'=>$out_trade_no))->find();
                if(!$isExist){
                    $result = $WinPayLog->add($data);
                }

            }

            //商户自行增加处理流程,
            //例如：更新订单状态
            //例如：数据库操作
            //例如：推送支付完成信息
        }
    }
}