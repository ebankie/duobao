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



/**
 * 个人中心
 * Class MyController
 * @package Weixin\Controller
 * @author
 */
class MyController extends HomeController {
    public function _initialize(){
        parent::_initialize();
        $this->checkLogin();
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
     * 个人中心首页
     * @author
     */
    public function index(){
        $data = M('Member')->where(array('uid'=>$_SESSION['onethink_home']['uid']))->find();
        $this->assign('data' , $data);
		
		$uid = $_SESSION['onethink_home']['uid'];
		$join_user_id = M('Join')->where(array('is_delete'=>0,'status'=>1))->getField('uid',true);
		
		if(in_array($uid,$join_user_id)){
			$usertype = '总代理商';
		}elseif($data['parent_id'] > 0){
			$usertype = '普通代理';
		}else{
			$usertype = '普通会员';
		}
		$this->assign('usertype' , $usertype);
		
		if(in_array($uid,$join_user_id) || $data['parent_id'] > 0){//代理或者下级代理会员
			$isjoin = true;
		}else{
			$isjoin = false;
		}	
		$this->assign('isjoin' , $isjoin);
        $this->meta_title = '个人中心';
        $this->display();
    }


    /**
     * 个人资料页面
     * @author
     */
    public function userData(){
        $data['userDetail'] = D('Member')->getUserDetail($_SESSION['onethink_home']['uid']);
        $this->assign('data' , $data);
        $this->meta_title = '个人资料';
        $this->display();
    }

    /**
     * 更新用户资料
     * @author
     */
    public function doEdit(){
        M('UcenterMember')->create();
        M('UcenterMember')->where(array ('id' => $_SESSION['onethink_home']['uid']))->save();
        M('Member')->create();
        $bool = M('Member')->where(array ('uid' => $_SESSION['onethink_home']['uid']))->save();
        if ($bool) {
            $this->success('修改成功');
        } else {
            $this->error('修改成功');
        }
    }

    /**
     * 购买记录
     * @author
     */
    public function buyLog(){
        $uid = D('Member')->uid();
        $list = M('WinOrder')->where(array('uid'=>$uid,'status'=>1))->order('create_time DESC')->select();
        $order_doing_num = 0 ;
        foreach($list as $key => $val){
            if($val['type'] == 1){
                $list[$key]['type'] = '小';
            }elseif($val['type'] == 2){
                $list[$key]['type'] = '大';
            }else{
                $list[$key]['type'] = '';
            }

//            if($val['goods_type'] == 1){
//                $list[$key]['goods_type'] = '28.00';
//            }elseif($val['goods_type'] == 2){
//                $list[$key]['goods_type'] = '55.00';
//            }else{
//                $list[$key]['goods_type'] = '未知';
//            }

            $list[$key]['goods_detail'] = M('Document')->field('title,cover_id,price')->where(array('id'=>$val['goods_id']))->find();

            if(time() < strtotime($val['lottery_time'])){
                $list[$key]['order_type'] = 1;//匹配中
                $order_doing_num ++;
            }else{
                $is_win = M('WinExchange')->where(array('order_id'=>$val['id']))->find();
                if($is_win){
                    if($is_win['is_exchange'] == 1){
                        $list[$key]['order_type'] = 4;//已兑奖
                    }else{
                        $list[$key]['order_type'] = 2;//中奖
                    }
                }else{
                    $list[$key]['order_type'] = 3;//未中奖
                }
            }
        }
        $day_start = strtotime(date('Y-m-d'));
        $day_end = $day_start + 24*3600;
        $order_num = M('WinOrder')->where("uid = {$uid} and status = 1 and create_time >= {$day_start} and create_time < $day_end")->count();//当日参与单数
        $order_success_num = M('WinExchange')->where("uid = {$uid} and create_time >= {$day_start} and create_time < $day_end")->count();//当日获胜单数

        $order_fail_num = $order_num - $order_success_num - $order_doing_num;//当日失败单数
        $this->assign('order_num',$order_num);
        $this->assign('order_success_num',$order_success_num);
        $this->assign('order_fail_num',$order_fail_num);
        $this->assign('list',$list);
        $this->display();
    }

    /**
     * 订单详情
     * @author
     */
    public function orderDetail(){
        $id = I('get.id');
        empty($id) ? $this->error('信息不存在',U('My/index')) : '';
        $info = M('WinOrder')->where(array('id'=>$id,'status'=>1))->find();//订单详情
        $info['is_open'] = (time() > strtotime($info['lottery_time'])) ? 1 : 0 ;//是否开奖

//        //PK对象
//        if($info['lottery_time'] && $info['period']){
//            $info_pk['list'] = M('WinOrder')->where("type <> {$info['type']} and uid <> {$info['uid']} and lottery_time = '{$info['lottery_time']}' and period = {$info['period']} and goods_id = {$info['goods_id']}")->order('id desc')->limit(3)->select();
//
//            if($info_pk){
//                $info_pk['num'] = count($info_pk['list']);
//                $info_pk['total_num'] = 0;
//                foreach($info_pk['list'] as $key => $val){
//                    $info_pk['list'][$key]['member_info'] =  M('Member')->field('headimgurl,nickname')->where(array('uid'=>$val['uid']))->find();
//                    $info_pk['total_num'] += $val['num'];
//                }
//            }
//
//        }

        //PK对象
        $is_order = M('WinOrderTemp')->where("order_id = {$id}")->find();//是否存在pk对象

        if($info['num'] < 3){
            if($is_order && $is_order['uid_arr']){
                $uid_arr = $is_order['uid_arr'];
            }else{
                $uid_arr = $this->getRandVal1();
                $tempdata['order_id'] = $id;
                $tempdata['uid_arr'] = $uid_arr;
                M('WinOrderTemp')->add($tempdata);
            }

            $map['id'] = $uid_arr;
            $info_pk['list'][] = M('MemberTemp')->where($map)->find();
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
            $info_pk['list'] = M('MemberTemp')->where($map)->select();

        }

//
//        $uid_arr =  M('WinOrderTemp')->where("order_id = {$id}")->getField('uid_arr');
//        $map['id'] = array('in',$uid_arr);
//        $info_pk['list'] = M('MemberTemp')->where($map)->select();



//        dump($info_pk);die;
        $info['goods_detail'] = D('Document')->getDetail($info['goods_id']);
        if($info['type'] == 1){
            $info['type_val'] = '小';
        }else{
            $info['type_val'] = '大';
        }
        $info['win_code'] = M('WinCode')->where(array('period'=>$info['period'],'create_time'=>$info['lottery_time']))->find();
        $info['is_win'] = M('WinExchange')->where(array('order_id'=>$id))->find();//是否中奖
//        $info['member_info'] = M('Member')->where(array('uid'=>$_SESSION['onethink_home']['uid']))->find();//用户信息
        $info['member_info'] = M('Member')->where(array('uid'=>$info['uid']))->find();//用户信息

        if($info['is_open'] == 1){//已开奖
            $info['win_code']['code'] = chunk_split($info['win_code']['code'],1,' ');//开奖结果
            if($info['goods_type'] == 1){
                $info['win_code_val'] = $info['win_code']['code_56'];   //获胜号码
                $info['win_code_val_type'] = ($info['win_code']['code_56_type'] == 1) ? '小' : '大';   //获胜号码
            }else{
                $info['win_code_val'] = $info['win_code']['code_110'];   //获胜号码
                $info['win_code_val_type'] = ($info['win_code']['code_110_type'] == 1) ? '小' : '大';
            }
        }else{//未开奖
            $info['win_code']['code'] = '待开奖';//开奖结果
            $info['win_code_val'] = '待开奖';   //获胜号码
            $info['win_code_val_type'] = '';   //获胜号码
        }

        $this->assign('info_pk',$info_pk);
        $this->assign('info',$info);
        $this->display();
    }

    /**
     * 兑换记录
     * @author
     */
    public function exchangeList(){
        $uid = D('Member')->uid();
        $noExchangeList = M('WinExchange')->where(array('uid'=>$uid,'is_exchange'=>0,'is_virtual'=>0))->order("id DESC")->select();
        foreach ($noExchangeList as $k => $v) {
            $noExchangeList[$k]['order'] = M('WinOrder')->where(array('id'=>$v['order_id'],'status'=>1))->find();
            $noExchangeList[$k]['order']['goods_detail'] = M('Document')->field('title,cover_id,price')->where(array('id'=>$noExchangeList[$k]['order']['goods_id']))->find();
            if($noExchangeList[$k]['order']['type'] == 1){
                $noExchangeList[$k]['type'] = '小';
            }elseif($noExchangeList[$k]['order']['type'] == 2){
                $noExchangeList[$k]['type'] = '大';
            }else{
                $noExchangeList[$k]['type'] = '';
            }

            if($noExchangeList[$k]['order']['goods_type'] == 1){
                $noExchangeList[$k]['goods_type'] = '28.00';
            }elseif($noExchangeList[$k]['order']['goods_type'] == 2){
                $noExchangeList[$k]['goods_type'] = '55.00';
            }else{
                $noExchangeList[$k]['goods_type'] = '未知';
            }
            //获胜号码
            if($noExchangeList[$k]['order']['goods_type'] == 1){
                $noExchangeList[$k]['win_code'] = M('WinCode')->where(array('period'=>$noExchangeList[$k]['order']['period'],'create_time'=>$noExchangeList[$k]['order']['lottery_time']))->getField('code_56');
            }else{
                $noExchangeList[$k]['win_code'] = M('WinCode')->where(array('period'=>$noExchangeList[$k]['order']['period'],'create_time'=>$noExchangeList[$k]['order']['lottery_time']))->getField('code_110');
            }
        }
//        dump($noExchangeList);


        $yesExchangeList = M('WinExchange')->where(array('uid'=>$uid,'is_exchange'=>1,'is_virtual'=>0))->order("id DESC")->select();
        foreach ($yesExchangeList as $k => $v) {
            $yesExchangeList[$k]['order'] = M('WinOrder')->where(array('id'=>$v['order_id'],'status'=>1))->find();
            $yesExchangeList[$k]['order']['goods_detail'] = M('Document')->field('title,cover_id,price')->where(array('id'=>$yesExchangeList[$k]['order']['goods_id']))->find();
            if($yesExchangeList[$k]['order']['type'] == 1){
                $yesExchangeList[$k]['type'] = '小';
            }elseif($yesExchangeList[$k]['order']['type'] == 2){
                $yesExchangeList[$k]['type'] = '大';
            }else{
                $yesExchangeList[$k]['type'] = '';
            }

            if($yesExchangeList[$k]['order']['goods_type'] == 1){
                $yesExchangeList[$k]['goods_type'] = '28.00';
            }elseif($yesExchangeList[$k]['order']['goods_type'] == 2){
                $yesExchangeList[$k]['goods_type'] = '55.00';
            }else{
                $yesExchangeList[$k]['goods_type'] = '未知';
            }

            //获胜号码
            if($yesExchangeList[$k]['order']['goods_type'] == 1){
                $yesExchangeList[$k]['win_code'] = M('WinCode')->where(array('period'=>$yesExchangeList[$k]['order']['period'],'create_time'=>$yesExchangeList[$k]['order']['lottery_time']))->getField('code_56');
            }else{
                $yesExchangeList[$k]['win_code'] = M('WinCode')->where(array('period'=>$yesExchangeList[$k]['order']['period'],'create_time'=>$yesExchangeList[$k]['order']['lottery_time']))->getField('code_110');
            }
        }
        $this->assign('noExchangeList',$noExchangeList);//未兑换
        $this->assign('yesExchangeList',$yesExchangeList);//已兑换

        $this->display();

    }

    /**
     * 设置查询密码
     * @author
     */
    public function setFindPwd(){
        $uid = D('Member')->uid();
        if(IS_POST){
            $find_code = $_POST['password'];
            $res = M('Member')->where(array('uid'=>$uid))->setField('find_code',$find_code);
            if($res){
                $url = U('My/index');
                header("Location: {$url}");
            }else{
                $this->error('设置异常',U('My/exchangeList'));
            }
        }else{
            $mobile = M('UcenterMember')->where("id = {$uid}")->getField('mobile');
            $data['mobile'] = substr($mobile,0,3).'****'.substr($mobile,7,4);
            $this->assign('data',$data);
            $this->display();
        }
    }

    /**
     * 输入密码
     * @author
     */
    public function typePwd(){
        $uid = D('Member')->uid();
        if(IS_POST){
            $pwd = $_POST['password'];
            $find_code = M('Member')->where("uid = {$uid}")->getField('find_code');
            if($pwd == $find_code){
                $url = U('My/exchangeList');
                header("Location: {$url}");
            }else{
                $this->error('输入错误',U('My/index'));
            }
        }else{
            $this->display();
        }
    }

    /**
     * 获取兑换码
     * @author
     */
    public function getExchangeCode(){
        $id = I('get.id');
        $data['exchange_number'] = M('WinExchange')->where(array('id'=>$id))->getField('exchange_number');
        $this->assign('data',$data);
        $this->display();
    }


    /**
     * 二维码
     * @author
     */
    public function mylink (){
        $uid = $_SESSION['onethink_home']['uid'];
        $config = M('Wxsetting')->where(array('id'=>1))->find();
        $userinfo = M('Member')->where(array('uid'=>$uid))->find();
        if($userinfo['ewm']){
            $url = $userinfo['ewm'];
        }else{
            $oldpic = $userinfo['headimgurl'];
            $shareurl ='http://' . $_SERVER['HTTP_HOST'] . '/Weixin/User/register/parent_id/'.$userinfo['uid'];
//            $wurl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$config['appid']."&redirect_uri=".$shareurl."&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
            $wurl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7fb456d4e2e698a4&redirect_uri=".$shareurl."&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
            $url = $this->makeCodeLogo($uid,$oldpic,$wurl);
            $res['ewm']  = ltrim($url,'.');
            M('Member')->where(array('uid'=>$uid))->save($res);
        }

        $join_user_id = M('Join')->where(array('is_delete'=>0,'status'=>1))->getField('uid',true);
        if(in_array($uid,$join_user_id)){
            $data['ratio'] = M('Join')->where(array('is_delete'=>0,'status'=>1,'uid'=>$uid))->getField('ratio');
        }else{
            $data['ratio'] = M('Config')->getFieldByName('DISTRIBUTION_PTC','value');//获取分销比率
        }
        $data['title'] = '我的二维码';
        $this->assign('data',$data);
        $this->assign('userinfo',$userinfo);
        $this->assign('wurl',ltrim($url,'.'));
        $this->display();
    }

    /**
     * 生成带logo的二维码
     * @param       $uid
     * @param string $oldpic
     * @param        $shareurl
     * @return string
     */
    public function makeCodeLogo($uid,$oldpic,$shareurl){
        //		dump($oldpic);die();
        vendor("phpqrcode");
        $value = $shareurl;
        $errorCorrectionLevel = 'L';//纠错级别：L、M、Q、H
        $matrixPointSize = 10;//二维码点的大小：1到10
        $ewm = "./Public/Weixin/erweima/".$uid.".png";
        \QRcode::png ( $value, $ewm, $errorCorrectionLevel, $matrixPointSize, 2 );//不带Logo二维码的文件名
        $logo = $oldpic ? $oldpic :"http://".$_SERVER['HTTP_HOST']."/Public/Weixin/img/erweima_logo.png";;//需要显示在二维码中的Logo图像
        $QR = ltrim($ewm,'.');
//        dump($logo);

        if ($logo !== FALSE) {
            $QR = imagecreatefromstring ( file_get_contents ( 'http://' . $_SERVER['HTTP_HOST']."/".$QR ) );
//            $logo = imagecreatefromstring ( file_get_contents ('http://' . $_SERVER['HTTP_HOST']."/".$logo) );

            $logo = imagecreatefromstring($this->get_by_curl($logo));
            if (imageistruecolor($logo)) imagetruecolortopalette($logo, false, 65535);
//            $logo = imagecreate($this->get_by_curl($logo));
//            dump($logo);die();
            $QR_width = imagesx ( $QR );
            $QR_height = imagesy ( $QR );
            $logo_width = imagesx ( $logo );
            $logo_height = imagesy ( $logo );
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width / $logo_qr_width;
            $logo_qr_height = $logo_height / $scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            imagecopyresampled ( $QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height );
        }
        $logoCode = "./Public/Weixin/erweima/logo_".$uid.".png";
        $res = imagepng ($QR, $logoCode);//带Logo二维码的文件名
        @unlink($ewm);
        return $logoCode;
    }


    /**
     * 虚拟账户
     * @author
     */
    public function account(){
        $uid = D('Member')->uid();
        $map['uid'] = $uid;
        $map['pid'] = $uid;
        $map['_logic'] = 'OR';
        $list = M('AccountLog')->where($map)->order('create_time DESC')->select();
        //$price = 0;
//        $ratio = M('Config')->getFieldByName('DISTRIBUTION_PTC','value');//获取分销开启状态
        foreach($list as $key => $val){
            $paytype = M('WinOrder')->where(array('order_number'=>$val['out_trade_no']))->getField('paytype');
            switch($val['status']){
                case '1':  //分销
                    if($val['uid'] == $uid){
                        $list[$key]['money']  = '-'.($val['money_p']/($val['ratio'] * 0.01));
                        $list[$key]['remark']  = '微信购买';
                        if($paytype == '个人账户'){
                            $price = $price - ($val['money_p']/($val['ratio'] * 0.01));
                            $list[$key]['remark']  = '个人账户购买';
                        }
                    }else{
                        //$price = $price + $val['money_p'];
                        $list[$key]['money']  = '+'.$val['money_p'];
                        $list[$key]['remark']  = '分销';
                    }
                    break;
                case '2': //购买
                    $list[$key]['money']  = '-'.$val['money_p'];
                    $list[$key]['remark']  = '微信购买';
                    if($paytype == '个人账户'){
                        //$price = $price - $val['money_p'];
                        $list[$key]['remark']  = '个人账户购买';
                    }
                    break;
                case '3': //充值
                    //$price = $price + $val['money_p'];
                    $list[$key]['money']  = '+'.$val['money_p'];
                    $list[$key]['remark']  = '充值';
                    break;
            }

        }
		
        $account = M('Member')->getFieldByUid($uid,'account');//当前会员原有资金
		$price = intval($account); 		
        $this->assign('price',$price);
        $this->assign('list',$list);
        $this->display();

    }


    /**
     * 充值
     * @author
     */
    public function recharge(){
        $data['time_end'] = $this->get_time_on_clock(time());//倒计时时间
        $this->assign('data' , $data);
        $this->display();
    }



    /**
     * 获取jsApiParameters
     * @author
     */
    public function getJsApiParametersNo(){
        //支付处理
        $total_fee = $_POST['total_fee'];//总费用

        //获取预支付信息
        $paymentInfo     = R('Wxpay/getPayment_account' , array ($total_fee));
        $jsApiParameters = $paymentInfo['jsApiParameters'];
        $out_trade_no    = $paymentInfo['out_trade_no'];//订单号


        //创建订单
        $data['uid'] = D('Member')->uid();
        $data['create_time'] = time();
        $data['total_fee'] = $total_fee;
        $data['out_trade_no'] = $out_trade_no;
        $data['ip_info'] = $this->getIpInfo();//用户的ip信息


        if($res = M('RechargeOrder')->add($data)){//创建成功
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

        $out_trade_no   = $_POST['out_trade_no'];//订单号

        $map['out_trade_no'] = $out_trade_no;
        $orderData = M('RechargeOrder')->where($map)->find();
        if($orderData['status'] == 0){
            M('RechargeOrder')->where($map)->setField('status',1);//将订单状态设置成已支付
        }
        if($orderData){
            $data2['status'] = 1;
            $data2['msg']    = 'success';
        }else{
            $data2['status'] = 0;
            $data2['msg']    = 'fail';
        }
        echo json_encode($data2);

    }


    /**
     * 申请加盟
     * @author
     */
    public function join(){
        if(IS_POST){
            $_POST['create_time'] = time();
            $_POST['uid'] = D('Member')->uid();
            $id = M('Join')->where(array('uid'=>$_POST['uid'],'is_delete'=>0))->getField('id');
            if($id){
                $res = M('Join')->where(array('uid'=>$_POST['uid'],'is_delete'=>0))->save($_POST);
            }else{
                $res = M('Join')->add($_POST);
            }
            if($res){
                $this->success('提交成功',U('My/index'));
            }else{
                $this->error('提交失败',U('My/join'));
            }
        }else{
            $uid = D('Member')->uid();
            $list = M('Join')->where(array('uid'=>$uid,'is_delete'=>0))->find();
            $this->assign('uid',$uid);
            $this->assign('list',$list);
            $this->display();

        }
    }

    /**
     * 分佣记录
     * @author
     */
    public function yongjinlog(){
		$puid = D('Member')->uid();
		$fxusers = getfxuser($puid);//当前会员下级会员 无限级别
		$fxuids = $puid;
        foreach ($fxusers as $key => $val) {
			if($fxuids==''){
				$fxuids = $val['uid'];
			}else{
				$fxuids = $fxuids.",".$val['uid'];	
			}
        }	
		//$map['pid']  = $puid;
		$map['pid']  = array('in',$fxuids);
		//佣金总金额
		$zong =M('AccountLog')->where($map)->Sum('money_p');
		$this->assign('zong' , $zong);
		//当天收益
		//$tdmap['pid']  = $puid;
		$tdmap['pid']  = array('in',$fxuids);
		$tdmap['create_time']  = array('egt',strtotime(date('Y-m-d')));
		$tdzong =M('AccountLog')->where($tdmap)->Sum('money_p');
		$tdzong = sprintf("%.2f",$tdzong);
		$this->assign('tdzong' , $tdzong);
		
		$list = M('AccountLog')->where($map)->order('create_time desc')->select();
        foreach ($list as $key => $val) {
            $list[$key]['nickname'] = M('Member')->where("uid='$val[uid]'")->getField("nickname");
        }		
		$this->assign('list',$list);
		$this->display();
    }
    /**
     * 分销会员
     * @author
     */
    public function fxuser(){
		$puid = I('get.uid');
		if(!$puid){
			$puid = D('Member')->uid();
		}

		$fxusers = getfxuser($puid);//当前会员下级会员 无限级别
		$fxuids = $puid;
		foreach ($fxusers as $key => $val) {
			if($fxuids==''){
				$fxuids = $val['uid'];
			}else{
				$fxuids = $fxuids.",".$val['uid'];	
			}
		}			
		//佣金总金额
		$zong = getfxyongjin($fxuids);//当前会员下级会员 无限级别
		$this->assign('zong' , $zong);
		//当天收益
		$tdzong = getdayyongjin($fxuids);//当前会员下级会员 无限级别
		$this->assign('tdzong' , $tdzong);		
		
		
		$userinfo = M('Member')->where(array('uid'=>$puid))->find();
		$this->assign('username',$userinfo['nickname']);
		
		$map['parent_id'] = $puid;
		//分销会员列表
		$list = M('Member')->where($map)->select();
        foreach ($list as $key => $val) {
            $list[$key]['mobile'] = M('UcenterMember')->where("id='$val[uid]'")->getField("mobile");
            $list[$key]['email']  = M('UcenterMember')->where("id='$val[uid]'")->getField("email");
			
			//佣金总金额
			$uzong = getself($val['uid']);//获取指定会员佣金
			$list[$key]['zong']  = sprintf("%.2f",$uzong);
			//当天收益
			$utdzong = getdayself($val['uid']);//获取指定会员当日佣金
			$list[$key]['tdzong']  = sprintf("%.2f",$utdzong);				
        }		
		$this->assign('list',$list);
		$nums = M('Member')->where($map)->count();
		$this->assign('nums',$nums);
		$this->display();
    }
}