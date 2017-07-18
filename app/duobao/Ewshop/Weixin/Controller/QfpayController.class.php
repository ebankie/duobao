<?php
namespace Weixin\Controller;

use Think\Controller;

class QfpayController extends Controller
{

    private $appcode   = '20A4374AF7DC4274A7F5CE9E7321B4DF';//开发者的唯一标示
    private $qfkey     = '3A8817E4BD944BB7B6F94BC8307BEC24';//支付key
    private $qfpay_url = 'https://openapi-test.qfpay.com';//测试环境
    private $direct_url = 'https://o2.qfpay.com/q/direct';//跳转到支付页面

    /**
     * 查询订单是否存在
     * @param string $out_trade_no
     */
    public function getOrderStatus($out_trade_no = '')
    {
//        $out_trade_no = 'FD20170502163826000053129806';
        $openid_data = array(
            'out_trade_no' => $out_trade_no,
        );
        $openid_sign = $this->make_req_sign($openid_data, $this->qfkey);
        $get_openid_url = $this->qfpay_url.'/trade/v1/query?out_trade_no='.$out_trade_no;
        $headers = array();
        $headers[] = 'X-QF-APPCODE: '.$this->appcode;
        $headers[] = 'X-QF-SIGN: '.$openid_sign;
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$get_openid_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $result = json_decode($result,true);
        curl_close($ch);
//        dump($result);

        if($result['respcd'] == '0000'){//查询支付后的订单是否成功生成
            if($result['data'][0]['out_trade_no'] && $out_trade_no){
                if($result['data'][0]['out_trade_no'] == $out_trade_no){
                    $data['status'] = 1;
                    $data['msg'] = 'Success';
                }else{
                    $data['status'] = 0;
                    $data['msg'] = 'Not Found';
                }
            }else{
                $data['status'] = 0;
                $data['msg'] = 'Error';
            }
        }else{
            $data['status'] = 0;
            $data['msg'] = 'Error';
        }
//        dump($data);
        return $data;
    }


    /**
     * 获取商品的授权url
     * 目的：获取微信code
     */
    public function getGoodsDetailUrl($redirect_uri = '')
    {
        $code_data = array(
            'app_code' => $this->appcode,
            'redirect_uri' => $redirect_uri,
        );
        $code_sign = $this->make_req_sign($code_data, $this->qfkey);
        $get_code_url = $this->qfpay_url.'/tool/v1/get_weixin_oauth_code?app_code='.$this->appcode.'&redirect_uri='.$redirect_uri.'&sign='.$code_sign;
        return $get_code_url;
    }

    /**
     *  获取openid
     * @param string $code
     * @return string
     */
    public function getOpenid($code = '')
    {
        $openid_data = array(
            'code' => $code,
        );
        $openid_sign = $this->make_req_sign($openid_data, $this->qfkey);
        $get_openid_url = $this->qfpay_url.'/tool/v1/get_weixin_openid?code='.$code;
        $headers = array();
        $headers[] = 'X-QF-APPCODE: '.$this->appcode;
        $headers[] = 'X-QF-SIGN: '.$openid_sign;
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$get_openid_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $result = json_decode($result,true);
        curl_close($ch);
//        dump($result);
        if($result['respcd'] == '0000' && $result['openid']){
            return $result['openid'];
        }else{
            return '';
        }
    }


    /**
     * 获取钱方支付的跳转链接和订单号
     * @param string $openid
     * @param string $goods_id
     * @param int $total_fee
     * @param $payType
     * @param string $redirect_uri
     * @return array|string
     */
    public function getDirectUrl($openid = '',$goods_id = '',$total_fee = 1, $payType)
    {
        ($total_fee <= 0) ?  die('支付金额必须大于0!') : '';
        (empty($payType)) ? die('支付类型不能为空!') : '';
        (empty($goods_id)) ? die('商品不能为空!') : '';
        (empty($openid)) ? die('openid不能为空!') : '';

        $goods_title = M('Document')->where(array('id'=>$goods_id))->getField('title');

        $openid_self = $this->get_openid_self();
        if($openid_self){
            $uid = M('Member')->where(array('openid'=>$openid_self))->getField('uid');
            $out_trade_no = 'FD'.date('YmdHis').sprintf("%07d", $uid).$payType.rand(1000,9999);
        }else{
            die('用户id不能为空!');
        }

        //唤起微信支付
//        $txamt = $total_fee;
        $txamt = 1;
        $txcurrcd = 'CNY';
        $pay_type = '800207';
        $out_trade_no = $out_trade_no;
        $txdtm = date('Y-m-d H:i:s');
        $sub_openid = $openid;
        $goods_name = $goods_title;
        $limit_pay = 'no_credit';
        $udid = time().random(0,9);
        $qfkey = $this->qfkey;
        $data = array(
            'txamt' => $txamt,
            'txcurrcd' => $txcurrcd,
            'pay_type' => $pay_type,
            'out_trade_no' => $out_trade_no,
            'txdtm' => $txdtm,
            'sub_openid' => $sub_openid,
            'goods_name'=>$goods_name,
            'limit_pay' => $limit_pay,
            'udid' => $udid,
        );
//            dump($data);

        $url = $this->qfpay_url.'/trade/v1/payment';
        $headers = array();
        $headers[] = 'X-QF-APPCODE: '.$this->appcode;
        $headers[] = 'X-QF-SIGN: '.$this->make_req_sign($data, $qfkey);
        $retult = $this->api_notice_increment($url, $data, $headers);//post 获取数据

        $res = json_decode($retult,true);
        $jsApiParameters = $res['pay_params'];
        if($jsApiParameters){
            $jsApiParameters['mchntnm'] = '半价商城';
            $jsApiParameters['txamt'] = $txamt;
            $jsApiParameters['goods_name'] = $goods_name;

            $redirect_uri = 'http://'.$_SERVER['HTTP_HOST'].'/Weixin/Goods/doByPay/order_number/'.$out_trade_no;//回调地址

            $jsApiParameters['redirect_url'] = $redirect_uri;
            $data['direct_url'] = $this->direct_url.'?'.http_build_query($jsApiParameters);
            $data['out_trade_no'] = $out_trade_no;
            return $data;
        }else{
            return '';
        }

    }



    /**
     *  获取本公众号对应的openid
     */
    private function get_openid_self() {
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
     * 测试页面
     * 地址：https://openapi-test.qfpay.com/tool/v1/get_weixin_oauth_code?app_code=20A4374AF7DC4274A7F5CE9E7321B4DF&redirect_uri=http://duobao.akng.net/Weixin/qfpay/index&sign=07e355f79680e30466931376cc7a2fcd
     */
    public function index()
    {
        $appcode = $this->appcode;
        $qfkey   = $this->qfkey;
        //获取code
        $redirect_uri = 'http://duobao.akng.net/Weixin/qfpay/index';

        $code_data = array(
            'app_code' => $appcode,
            'redirect_uri' => $redirect_uri,
        );
        $code_sign = $this->make_req_sign($code_data, $qfkey);

        $get_code_url = $this->qfpay_url.'/tool/v1/get_weixin_oauth_code?app_code='.$appcode.'&redirect_uri='.$redirect_uri.'&sign='.$code_sign;
        $this->get_curl($get_code_url);
        $code = $_GET['code'];

        //获取openid
        $openid_data = array(
            'code' => $code,
        );
        $openid_sign = $this->make_req_sign($openid_data, $qfkey);
        $get_openid_url = $this->qfpay_url.'/tool/v1/get_weixin_openid?code='.$code;
        $headers = array();
        $headers[] = 'X-QF-APPCODE: '.$this->appcode;
        $headers[] = 'X-QF-SIGN: '.$openid_sign;
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$get_openid_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $result = json_decode($result,true);
        curl_close($ch);


        if($result['respcd'] == '0000' && $result['openid']){
            $openid = $result['openid'];

            //唤起微信支付
            $txamt = '123';
            $txcurrcd = 'CNY';
            $pay_type = '800207';
            $out_trade_no = 'TD1453996260535648';
            $txdtm = date('Y-m-d H:i:s');
            $sub_openid = $openid;
            $goods_name = '测试产品';
            $limit_pay = 'no_credit';
            $udid = '18801055836';
            $qfkey = $this->qfkey;
            $data = array(
                'txamt' => $txamt,
                'txcurrcd' => $txcurrcd,
                'pay_type' => $pay_type,
                'out_trade_no' => $out_trade_no,
                'txdtm' => $txdtm,
                'sub_openid' => $sub_openid,
                'goods_name'=>$goods_name,
                'limit_pay' => $limit_pay,
                'udid' => $udid,
            );

            $url = $this->qfpay_url.'/trade/v1/payment';
            $headers = array();
            $headers[] = 'X-QF-APPCODE: '.$this->appcode;
            $headers[] = 'X-QF-SIGN: '.$this->make_req_sign($data, $qfkey);
            $retult = $this->api_notice_increment($url, $data, $headers);

            $res = json_decode($retult,true);
            $jsApiParameters = $res['pay_params'];
        }else{
            $jsApiParameters = '';
        }

        $jsApiParameters['mchntnm'] = '测试有限公司';
        $jsApiParameters['txamt'] = $txamt;
        $jsApiParameters['goods_name'] = $goods_name;
        $jsApiParameters['redirect_url'] = $redirect_uri;
        $direct_url = $this->direct_url.'?'.http_build_query($jsApiParameters);
        echo '<a href="'.$direct_url.'">测试</a>';
        dump($direct_url);


//        dump($jsApiParameters);
//        $this->assign('jsApiParameters' ,$jsApiParameters);

//        $this->display();

    }


    /**
     * 签名校验算法
     * @param $data
     * @param $qfkey
     * @return string
     */
    public function make_req_sign($data,$qfkey)
    {
        ksort($data);
        $str = '';
        foreach ($data as $key => $val) {
            $str .= $key.'='.$val.'&';
        }
        $str = substr($str,0,strlen($str)-1);//拼接字符串

        $str_md5 = md5($str.$qfkey);

        return strtoupper($str_md5);
    }




    /**
     * post 模拟
     * @param $url
     * @param $data
     * @param $headers
     * @return mixed
     */
    public function api_notice_increment($url, $data, $headers)
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        $data = http_build_query($data);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回

        $lst = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Errno'.curl_error($curl);//捕抓异常
        }
        curl_close($curl);
        return $lst;
    }

    /**
     * get 模拟
     * @param $url
     * @return mixed
     */
    public function get_curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }


    /**
     *  服务器异步通知页面路径
     */
    public function Paynotify()
    {
        if($this->checkSign() == TRUE){
            $returnData = $GLOBALS['HTTP_RAW_POST_DATA'];//异步通知返回的数据
            $returnData = json_decode($returnData, true);
            if($returnData){
                $order_number = $returnData['out_trade_no'];
                $orderData = M('WinOrder')->where(array('order_number'=>$order_number))->find();
                //如果订单状态为0，将其设置成1
                if($orderData['status'] == 0){
                    $res = M('WinOrder')->where(array('order_number'=>$order_number))->setField('status',1);
                    if($res){
                        echo 'SUCCESS';
                    }
                }else{
                    echo 'SUCCESS';
                }

                //插入订单支付记录表
                $payData['out_trade_no'] = $returnData['out_trade_no'];
                $payData['syssn'] = $returnData['syssn'];
                $payData['txdtm'] = $returnData['txdtm'];
                $payData['create_time'] = time();
                M('WinOrderPaylog')->add($payData);
            }
        } else {
            echo 'FAIL';
        }
    }

    private function checkSign()
    {
        $returnHead = getallheaders();
        $returnSign = $returnHead['X-Qf-Sign'];//返回的签名值

        $returnData = $GLOBALS['HTTP_RAW_POST_DATA'];//异步通知返回的数据
        $str_md5 = md5($returnData.$this->qfkey);
        $sign = strtoupper($str_md5);//算出的签名值

        if($returnSign == $sign){
            return TRUE;
        }
        return FALSE;

    }


    /**
     *  服务器异步通知页面路径
     */
    public function Paynotify_bak()
    {

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
                $data['out_trade_no']= $out_trade_no;
                $data['uid']= M('Member')->where(array('openid'=>$returnData["openid"]))->getField('uid');
                $data['create_time']= time();
                //将记录插入支付记录表
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