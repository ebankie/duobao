<?php
// +----------------------------------------------------------------------
// | 微信管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2017  All rights reserved.
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------
namespace Weixin\Controller;
use Workerman\Worker;
use OT\DataDictionary;


class GoodsController extends HomeController {

    public function _initialize(){
        parent::_initialize();
        $this->checkLogin();

//        //获取code
//        echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
//        $location_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
//        $url_arr = explode('&',$location_url);
//        $url_arr2 = explode('=',$url_arr[1]);
//        $code = $url_arr2[1];
//        if($code){
//            //获取openid
//            $openid = R('Qfpay/getOpenid' , array($code));
//            if($openid){
//                $_SESSION['qfpay_openid'] = $openid;
//            }
//        }
    }

    /**
     * 检查用户是否登录
     * @author
     */
    protected function checkLogin(){
        if (!is_login()) {
            $url = U('User/register');
            header("Location: {$url}");
            exit;
        }
    }


    /**
     * 拍品详情页
     * @author
     */
    public function detail(){

//        $openid = $_SESSION['qfpay_openid'];
//        $location_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
//
//        $direct_url = R('Qfpay/getDirectUrl' , array($openid , '', 1,$location_url));
//        echo '<a href="'.$direct_url.'">测试</a>';
//        dump($direct_url);

        if($_GET['t'] == 'cuihua'){
            $data['time_end'] = $this->get_time_on_clock(time());//倒计时时间

            $this->assign('data' , $data);
            $this->display('My/recharge');
            exit();
        }
        $goodsId             = intval($_GET['id']);
        empty($goodsId) ? $this->error('信息不存在',U('Index/index')) : '';

        $data['goods_id'] = $goodsId;
        $data['goodsDetail'] = D('Document')->getDetail($goodsId);
//        dump($data['goodsDetail']);
        $data['pageTitle']   = $data['goodsDetail']['title'];

        //当前用户信息
        $uid = D('Member')->uid();
        $price = $this->getPrice($uid);
        $this->assign('price',$price);
        $data['memberData'] = M('Member')->where(array('uid'=>$uid))->find();
		
        //$data['memberData']['ip'] = $this->getIpInfo();//用户的ip信息
		$data['memberData']['ip'] = getip();//用户的ip信息
		

        $data['time_end'] = $this->get_time_on_clock(time());//倒计时时间
        $data['period_now'] = $this->getPeriod($data['time_end']);//开奖期数
        $data['myunix'] = strtotime(str_replace('/','-',$data['time_end']));
        $period_last = M('WinCode')->where("code <> 0")->order("id desc")->find();
//        dump($period_last);
        if($data['goodsDetail']['price'] == 1){//号码(56除余+1）
            $data['period_last_type'] = ($period_last['code_56_type'] == 1) ? '小' : '大'; //上期大/小
            $data['code_word1'] = '1-28';
            $data['code_word2'] = '29-56';
            $data['price_half'] = 28;//半价
            $data['price_half_1'] = 28;//半价
        }elseif($data['goodsDetail']['price'] == 2){//号码(110除余+1)
            $data['period_last_type'] = ($period_last['code_110_type'] == 1) ? '小' : '大'; //上期大/小
            $data['code_word1'] = '1-55';
            $data['code_word2'] = '56-110';
            $data['price_half'] = 55;//半价
            $data['price_half_1'] = 55;//半价
        }

        //半价pk榜(中奖记录)
        $pk_list = M('WinExchange')->where("goods_id = {$goodsId}")->order('buy_time DESC')->limit(10)->select();
        foreach($pk_list as $key => $val){
            if($val['is_virtual'] == 1){
                $pk_list[$key]['userinfo'] = M('MemberTemp')->field('headimgurl,nickname')->where("id = {$val['uid']}")->find();//虚拟用户
            }else{
                $pk_list[$key]['userinfo'] = M('Member')->field('headimgurl,nickname')->where("uid = {$val['uid']}")->find();
            }
        }	
		
        $data['pk_list'] = $pk_list;
        $this->assign('data' , $data);
        $this->meta_title = $data['goodsDetail']['title'];
        $this->display();
    }

//    /**
//     * 获取钱方支付跳转链接和订单号
//     */
//    public function getQfpayUrl()
//    {
//        $openid = $_SESSION['qfpay_openid'];
//        $total_fee = $_POST['total_fee'];//总金额
//        $goods_id = $_POST['goods_id'];//商品id
//        $type      = $_POST['type'];//1：小号码段   2：大号码段
//        $num            = $_POST['num'];//参与单数
//        $lottery_time   = $_POST['lottery_time'];//开奖时间
//
//        //获取获取钱方支付的跳转链接
//        $return_data = R('Qfpay/getDirectUrl' ,array($openid , $goods_id, $total_fee,$type));
//        $direct_url  = $return_data['direct_url'];//跳转链接
//        $out_trade_no  = $return_data['out_trade_no'];//订单号
//
//
//        //创建订单
//        $data['uid'] = D('Member')->uid();
//        $data['goods_id'] = $goods_id;
//        $data['num'] = $num;
//        $data['type'] = $type;
//        $data['create_time'] = time();
//        $data['order_number'] = $out_trade_no;
//        $data['lottery_time'] = $lottery_time;
//        $data['period'] = $this->getPeriod($lottery_time);//开奖期数
//        $data['ip_info'] = $this->getIpInfo();//用户的ip信息
//
//        $goods_type = M('Document')->where(array('id'=>$goods_id))->getField('price');
//        $data['goods_type'] = $goods_type;
//
//        if($goods_type == 1){
//            $data['money_w'] = $num*28;
//            $data['money'] = $num*28;
//            if($type == 1){
//                $data['number_section'] = '1-28';
//            }elseif($type == 2){
//                $data['number_section'] = '29-56';
//            }
//        }elseif($goods_type == 2){
//            $data['money_w'] = $num*55;
//            $data['money'] = $num*55;
//            if($type == 1){
//                $data['number_section'] = '1-55';
//            }elseif($type == 2){
//                $data['number_section'] = '56-110';
//            }
//        }
//
//        if($res = M('WinOrder')->add($data)){//创建成功
//            $result['direct_url'] = $direct_url;//跳转链接
//            $result['out_trade_no'] = $out_trade_no;//订单号
//            $result['status'] = 1;
//            $result['msg']    = 'success';
//        }else{//创建失败
//            $result['status'] = 0;
//            $result['msg']    = 'fail';
//        }
//
//        echo json_encode($result);
//
//    }

/****
    //这种获取用户资金的方式效率太低，不可取换成下面的方式，直接在字段中存值，每次充值、购买、获得佣金自动更新
    public function getPrice($uid){
        $map['uid'] = $uid;
        $map['pid'] = $uid;
        $map['_logic'] = 'OR';
        $arr = M('AccountLog')->where($map)->order('create_time DESC')->select();
        $price = 0;
        $ratio = M('Config')->getFieldByName('DISTRIBUTION_PTC','value');//获取分销开启状态
        foreach($arr as $key => $val){
            $paytype = M('WinOrder')->where(array('order_number'=>$val['out_trade_no']))->getField('paytype');
            switch($val['status']){
                case '1':  //分销
                    if($val['uid'] == $uid){
                        if($paytype == '个人账户'){
                            $price = $price - ($val['money_p']/($val['ratio'] * 0.01));
                        }
                    }else{
                        $price = $price + $val['money_p'];
                    }
                    break;
                case '2': //购买

                    if($paytype == '个人账户'){
                        $price = $price - $val['money_p'];
                    }
                    break;
                case '3': //充值
                    $price = $price + $val['money_p'];
                    break;
            }
        }
        return $price;
    }
***/

	//获取当前会员资金
    public function getPrice($uid){
        $account = M('Member')->getFieldByUid($uid,'account');//当前会员原有资金
		$price = intval($account); 
        return $price;
    }

    /**
     * 获取jsApiParameters
     * @author
     */
    public function getJsApiParametersNo(){
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
        $paymentInfo     = R('Wxpay/getPayment' , array ($total_fee , $type));
        $jsApiParameters = $paymentInfo['jsApiParameters'];
        $out_trade_no    = $paymentInfo['out_trade_no'];//订单号


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

        if($res = M('WinOrder')->add($data)){//创建成功
            $result['status'] = 1;
            $result['msg']    = 'success';
            $result['jsApiParameters'] = json_decode($jsApiParameters);
            $result['out_trade_no'] = $out_trade_no;//订单号
        }else{//创建失败
            $result['status'] = 0;
            $result['msg']    = 'fail';
        }
        echo json_encode($result);
    }

    /**
     * 支付完成后的操作-产生订单
     * @author
     */
    public function  doSthByPayment(){
//        $goods_id       = $_POST['goods_id'];//商品id
//        $type           = $_POST['type'];//1:小号码段，2:大号码段
//        $num            = $_POST['num'];//参与单数
//        $lottery_time   = $_POST['lottery_time'];//开奖时间
//
//        $data['uid'] = D('Member')->uid();
//        $data['goods_id'] = $goods_id;
//        $data['num'] = $num;
//        $data['type'] = $type;
//        $data['create_time'] = time();
//        $data['order_number'] = $out_trade_no;
//        $data['lottery_time'] = $lottery_time;
//        $data['period'] = $this->getPeriod($lottery_time);//开奖期数
//        $data['ip_info'] = $this->getIpInfo();//用户的ip信息
//        $data['status'] = 1;//订单已支付状态
//
//        $goods_type = M('Document')->where(array('id'=>$goods_id))->getField('price');
//        $data['goods_type'] = $goods_type;
//
//        if($goods_type == 1){
//            $data['money_w'] = $num*28;
//            $data['money'] = $num*28;
//            if($type == 1){
//                $data['number_section'] = '1-28';
//            }elseif($type == 2){
//                $data['number_section'] = '29-56';
//            }
//        }elseif($goods_type == 2){
//            $data['money_w'] = $num*55;
//            $data['money'] = $num*55;
//            if($type == 1){
//                $data['number_section'] = '1-55';
//            }elseif($type == 2){
//                $data['number_section'] = '56-110';
//            }
//        }




        $out_trade_no   = $_POST['out_trade_no'];//订单号

        $map['order_number'] = $out_trade_no;
        $orderData = M('WinOrder')->where($map)->find();
        if($orderData['status'] == 0){
            M('WinOrder')->where($map)->setField('status',1);//将订单状态设置成已支付
        }

        if($orderData){
            $data2['order_id'] = $orderData['id'];
            $data2['status'] = 1;
            $data2['msg']    = 'success';
        }else{
            $data2['status'] = 0;
            $data2['msg']    = 'fail';
        }
        echo json_encode($data2);

    }

    /**
     * 个人账户支付完成后的操作-产生订单
     * @author
     */
//  public function  doSetstatus(){
//      $out_trade_no   = $_POST['out_trade_no'];//订单号
//      $map['order_number'] = $out_trade_no;
//      $orderData = M('WinOrder')->where($map)->find();
////        $uid = D('Member')->uid();
//      if($orderData['status'] == 0){
//          $time_end = $this->get_time_on_clock(time());//倒计时时间
//          $period = $this->getPeriod($time_end);//开奖期数
//          if($orderData['lottery_time'] != $time_end){
//              $arr['lottery_time'] = $time_end;
//              $arr['period'] = $period;
//              $data['create_time'] = time();
//          }
//          $data['paytype'] = '个人账户';
//          $data['pay_time'] = time();
//          $data['status'] = 1;
//          M('WinOrder')->where($map)->save($data);//将订单状态设置成已支付
//
//          $disbut_open = M('Config')->getFieldByName('DISTRIBUTION','value');//获取分销开启状态
//          $oneuid = M('Member')->getFieldByUid($orderData['uid'],'parent_id');
//          $join_user_id = M('Join')->where(array('is_delete'=>0,'status'=>1))->getField('uid',true);
//          if(in_array($oneuid,$join_user_id)){
//              $ratio = M('Join')->where(array('is_delete'=>0,'status'=>1,'uid'=>$oneuid))->getField('ratio');
//          }else{
//              $ratio = M('Config')->getFieldByName('DISTRIBUTION_PTC','value');//获取分销比率
//          }
//          if($disbut_open && $oneuid){
//              $map['status'] = 1;
//              $map['ratio'] = $ratio;
//              $map['pid'] = $oneuid;
//              $map['money_p'] = $orderData['money'] * ($ratio * 0.01);
//              $map['out_trade_no'] = $out_trade_no;
//              $map['uid'] = $orderData['uid'];
//              $map['create_time'] = time();
//              M('AccountLog')->add($map);
//          }else{
//              $map['status'] = 2;
//              $map['pid'] = 0;
//              $map['money_p'] = $orderData['money'];
//              $map['out_trade_no'] = $out_trade_no;
//              $map['uid'] = $orderData['uid'];
//              $map['create_time'] = time();
//              M('AccountLog')->add($map);
//          }
//
//
//      }
//
//      if($orderData){
//          $data2['order_id'] = $orderData['id'];
//          $data2['status'] = 1;
//          $data2['msg']    = 'success';
//      }else{
//          $data2['status'] = 0;
//          $data2['msg']    = 'fail';
//      }
//      echo json_encode($data2);
//
//  }




//    /**
//     * 支付后跳转的页面
//     * @author
//     */
//    public function doByPay(){
//        $order_number = I('get.order_number');//订单号
//        empty($order_number) ? $this->error('操作异常') : '';
//        $orderDetail = M('WinOrder')->where(array('order_number'=>$order_number))->find();//订单详情
//
//        //订单是否已经支付
//        $is_pay = R('Qfpay/getOrderStatus' , array($order_number));
//        if($is_pay){//已支付
//            //如果订单状态为0，将其设置成1->已支付
//            if($orderDetail['status'] == 0){
//                M('WinOrder')->where(array('order_number'=>$order_number))->setField('status',1);
//            }
//
//            $id = $orderDetail['id'];
//            //订单信息
//            $data['order_detail'] = $orderDetail;
//            $data['order_detail']['type_val'] = ($data['order_detail']['type'] == 1) ? '小':'大';
//            $data['goodsDetail']  = D('Document')->getDetail($data['order_detail']['goods_id']);
//
//            //当前用户信息
//            $uid = D('Member')->uid();
//            $data['memberData'] = M('Member')->field('headimgurl,nickname')->where(array('uid'=>$uid))->find();
//
//            $data['time_end'] = $this->get_time_on_clock(time());//倒计时时间
//            $data['period_now'] = $this->getPeriod($data['time_end']);//开奖期数
//
//            //上期大/小
//            if($data['goodsDetail']['price'] == 1){//号码(56除余+1）
//                $period_last_type = M('WinCode')->where(array('create_time'=>$data['time_end']))->getField('code_56');
//                $data['period_last_type'] = ($period_last_type <= 28) ? '小' : '大';
//            }elseif($data['goodsDetail']['price'] == 2){//号码(110除余+1)
//                $period_last_type = M('WinCode')->where(array('create_time'=>$data['time_end']))->getField('code_110');
//                $data['period_last_type'] = ($period_last_type <= 55) ? '小' : '大';
//            }
//
//
//            //PK对象
//            $data['info_pk']['type_val'] = ($data['order_detail']['type'] == 1) ? '大':'小';
//            $is_order = M('WinOrderTemp')->where("order_id = {$id}")->find();//是否存在pk对象
//
//            if($data['order_detail']['num'] < 3){
//                if($is_order && $is_order['uid_arr']){
//                    $uid_arr = $is_order['uid_arr'];
//                }else{
//                    $uid_arr = $this->getRandVal1();
//                    $tempdata['order_id'] = $id;
//                    $tempdata['uid_arr'] = $uid_arr;
//                    M('WinOrderTemp')->add($tempdata);
//                }
//
//                $map['id'] = $uid_arr;
//                $data['info_pk']['list'][] = M('MemberTemp')->where($map)->find();
//            }else{
//                if($is_order && $is_order['uid_arr']){
//                    $uid_arr = $is_order['uid_arr'];
//                }else{
//                    $uid_arr = $this->getRandVal3();
//                    $tempdata['order_id'] = $id;
//                    $tempdata['uid_arr'] = $uid_arr;
//                    M('WinOrderTemp')->add($tempdata);
//                }
//                $map['id'] = array('in',$uid_arr);
//                $data['info_pk']['list'] = M('MemberTemp')->where($map)->select();
//            }
//
//            $this->assign('data',$data);
//            $this->display();
//        }else{
//            //当未查询到订单时，调转到商品详情页
//            $this->error('操作异常',U('index/index'));
//        }
//    }



    /**
     * 支付后跳转的页面
     * @autho
     */
    public function doByPay(){
        $id = I('get.id');
        empty($id) ? $this->error('操作异常') : '';
        //订单信息
        $data['order_detail'] = M('WinOrder')->where(array('id'=>$id))->find();
        $data['order_detail']['type_val'] = ($data['order_detail']['type'] == 1) ? '小':'大';
        $data['goodsDetail']  = D('Document')->getDetail($data['order_detail']['goods_id']);

        //当前用户信息
        $uid = D('Member')->uid();
        $data['memberData'] = M('Member')->field('headimgurl,nickname')->where(array('uid'=>$uid))->find();

        $data['time_end'] = $this->get_time_on_clock(time());//倒计时时间
        $data['period_now'] = $this->getPeriod($data['time_end']);//开奖期数



        $period_last = M('WinCode')->where("code <> 0")->order("id desc")->find();
//        dump($period_last);
        if($data['goodsDetail']['price'] == 1){//号码(56除余+1）
            $data['period_last_type'] = ($period_last['code_56_type'] == 1) ? '小' : '大'; //上期大/小

        }elseif($data['goodsDetail']['price'] == 2){//号码(110除余+1)
            $data['period_last_type'] = ($period_last['code_110_type'] == 1) ? '小' : '大'; //上期大/小

        }
//
//        //上期大/小
//        if($data['goodsDetail']['price'] == 1){//号码(56除余+1）
//            $period_last_type = M('WinCode')->where(array('create_time'=>$data['time_end']))->getField('code_56');
//            $data['period_last_type'] = ($period_last_type <= 28) ? '小' : '大';
//        }elseif($data['goodsDetail']['price'] == 2){//号码(110除余+1)
//            $period_last_type = M('WinCode')->where(array('create_time'=>$data['time_end']))->getField('code_110');
//            $data['period_last_type'] = ($period_last_type <= 55) ? '小' : '大';
//        }


        //PK对象
        $data['info_pk']['type_val'] = ($data['order_detail']['type'] == 1) ? '大':'小';
        $is_order = M('WinOrderTemp')->where("order_id = {$id}")->find();//是否存在pk对象

        if($data['order_detail']['num'] < 3){
            if($is_order && $is_order['uid_arr']){
                $uid_arr = $is_order['uid_arr'];
            }else{
                $uid_arr = $this->getRandVal1();
                $tempdata['order_id'] = $id;
                $tempdata['uid_arr'] = $uid_arr;
                M('WinOrderTemp')->add($tempdata);
            }

            $map['id'] = $uid_arr;
            $data['info_pk']['list'][] = M('MemberTemp')->where($map)->find();
        }else{
            if($is_order && $is_order['uid_arr']){
                $uid_arr = $is_order['uid_arr'];
            }else{
                $uid_arr = $this->getRandVal3();
                $tempdata['order_id'] = $id;
                $tempdata['uid_arr'] = $uid_arr;
                M('WinOrderTemp')->add($tempdata);
            }
            $map['id'] = array('in',$uid_arr);
            $data['info_pk']['list'] = M('MemberTemp')->where($map)->select();

        }

        $this->assign('data',$data);
        $this->display();
    }


    /**
     * 使用微币支付生成订单
     * @autho
     */
    public function makeOrder(){
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

        $uid = D('Member')->uid();
        $out_trade_no = 'FD-'.date('YmjHis').sprintf("%07d", $uid).$type.rand(1000,9999);//商户订单号


        //创建订单
        $data['uid'] = $uid;
        $data['goods_id'] = $goods_id;
        $data['num'] = $num;
        $data['type'] = $type;
        $data['create_time'] = time();
        $data['order_number'] = $out_trade_no;
        $data['lottery_time'] = $lottery_time;
        $data['period'] = $this->getPeriod($lottery_time);//开奖期数  
        
        //$data['ip_info'] = $this->getIpInfo();//用户的ip信息    
        
//        $time_end = $this->get_time_on_clock(time());//倒计时时间
//        $period = $this->getPeriod($time_end);//开奖期数
//        if($data['lottery_time'] != $time_end){
//            $data['lottery_time'] = $time_end;
//            $data['period'] = $period;
//            $data['create_time'] = time();
//        }
        $data['paytype'] = '个人账户';
        $data['pay_time'] = time();
        $data['status'] = 1;

//      $goods_type = M('Document')->where(array('id'=>$goods_id))->getField('price');
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


        $res = M('WinOrder')->add($data);
        if($res){//创建成功
            $result['status'] = 1;
            $result['msg']    = 'success';
            $result['out_trade_no'] = $res;//订单对应id
            
		        $disbut_open = M('Config')->getFieldByName('DISTRIBUTION','value');//获取分销开启状态
		        $oneuid = M('Member')->getFieldByUid($uid,'parent_id');
		        $join_user_id = M('Join')->where(array('is_delete'=>0,'status'=>1))->getField('uid',true);
		
		        if(in_array($oneuid,$join_user_id)){
		            $ratio = M('Join')->where(array('is_delete'=>0,'status'=>1,'uid'=>$oneuid))->getField('ratio');
		        }else{
		            $ratio = M('Config')->getFieldByName('DISTRIBUTION_PTC','value');//获取分销比率
		        }
				
				//上级获得佣金 插入佣金记录
		        if($disbut_open && $oneuid){
		            $pmap['status'] = 1;
		            $pmap['ratio'] = $ratio;
		            $pmap['pid'] = $oneuid;
		            $pmap['money_p'] = $data['money'] * ($ratio * 0.01);
		            $pmap['out_trade_no'] = $out_trade_no;
		            $pmap['uid'] = $uid;
		            $pmap['create_time'] = time();
		            M('AccountLog')->add($pmap);
					//更新上级 资金（+佣金）
					$paccount = M('Member')->getFieldByUid($oneuid,'account');//上级会员原有资金
					M('Member')->where(array('uid'=>$oneuid))->setField('account',$paccount+$pmap['money_p']);
				}
				
				//插入当前会员消费记录
				$map['status'] = 2;
				$map['pid'] = 0;
				$map['money_p'] = $data['money'];
				$map['out_trade_no'] = $out_trade_no;
				$map['uid'] = $uid;
				$map['create_time'] = time();
				M('AccountLog')->add($map);
				//更新当前用户资金
				$account = M('Member')->getFieldByUid($uid,'account');//当前会员原有资金
				M('Member')->where(array('uid'=>$uid))->setField('account',$account-$map['money_p']);
		          
        
        }else{//创建失败
            $result['status'] = 0;
            $result['msg']    = 'fail';
        }

        echo json_encode($result);
    }



}