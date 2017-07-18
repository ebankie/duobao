<?php
// +----------------------------------------------------------------------
// | 微信管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2017  All rights reserved.
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------

/**
 * 前台公共库文件
 * 主要定义前台公共函数库DF
 */

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author ew_xiaoxiao
 */
function check_verify($code, $id = 1){
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}

/**
 * 获取列表总行数
 * @param  string  $category 分类ID
 * @param  integer $status   数据状态
 * @author ew_xiaoxiao
 */
function get_list_count($category, $status = 1){
    static $count;
    if(!isset($count[$category])){
        $count[$category] = D('Document')->listCount($category, $status);
    }
    return $count[$category];
}
function get_group_price($unionid){
	$unionid= explode('、',$unionid);
	$total="";
foreach($unionid as $val){
$id=$val;
$total+=get_good_price($id);
}
   
    return $total;
}
function get_group_count($unionid){
	$array= explode('、',$unionid);
$number=count($array);
  
    return $number;
}
function get_face($uid){

	$comment=M("ucenter_member");
	$map['id']=$uid;
	$count=$comment->where($map)->find();
  
    return $count["face"];
}
function get_comment_count($id){
	$comment=M("comment");
	$map['goodid']=$id;
	$count=$comment->where($map)->count();
  
    return $count;
}
function get_message_count($id){
$message=M("message");
$map['goodid']=$id;
	$count=$message->where($map)->count();
  
    return $count;
}

function get_group_marketprice($unionid){
	$unionid= explode('、',$unionid);
	$total="";
foreach($unionid as $val){
$id=$val;
$total+=get_good_yprice($id);
}

 if(!isset($total)){
$price=get_group_price($unionid);

 }
 return $total?$total:$price;  
 
}
/**
 * 返回优惠券可抵用金额
 */
function get_fcoupon_fee($code,$total){
    $lowfee=get_fcoupon_lowpayment($code);//优惠券最低消费金额
	if($lowfee<$total)
	{
$info=M("fcoupon")->where("code='$code' and status='1'")->find();//获取优惠券主键id
$fee=$info["price"];//获取优惠券金额
$codeid=$info["id"];
$usercouponid=M("usercoupon")->where("couponid='$codeid' and status='1'")->getField('id');//获取用户可用优惠券主键id
if($usercouponid){
$deccode=$fee;
$uid=D("member")->uid();
M("usercoupon")->where("couponid='$codeid' and uid='$uid' ")->setField('status',2);//设置优惠券已用
}
else{
	$deccode=0;
}
}
else{
$deccode=0;
}
 return $deccode; 
}

/**
 * 获取段落总数
 * @param  string $id 文档ID
 * @return integer    段落总数
 * @author ew_xiaoxiao
 */
function get_part_count($id){
    static $count;
    if(!isset($count[$id])){
        $count[$id] = D('Document')->partCount($id);
    }
    return $count[$id];
}
function get_tuan_count($id){
 $number=M('Tuanid')->where("tuanpid='$id'")->count();
    return  $number;
}
function get_shop_mobile($id){
 $info=M('shop')->where("id='$id'")->find();
    return  $info["mobile"];
}
function get_shop_address($id){
 $info=M('shop')->where("id='$id'")->find();
    return  $info["shopaddress"];
}
function  get_up_school(){
	$get_up=$_GET['id'];
	return $get_up;
}
/**
 * 获取首页幻灯片
 * @param  string $url 导航URL
 * @return string      解析或的url
 * @author
 */
function get_slide(){
     $slide=M('slide');
    $slidelist=$slide->where('status=1')->select();
    return  $slidelist;
}

//在线交易订单支付处理函数
 //函数功能：根据支付接口传回的数据判断该订单是否已经支付成功；
 //返回值：如果订单已经成功支付，返回true，否则返回false；
 function checkorderstatus($ordid){
    $Ord=M('Orderlist');
    $ordstatus=$Ord->where('ordid='.$ordid)->getField('ordstatus');
    if($ordstatus==1){
        return true;
    }else{
        return false;    
    }
 }
//处理订单函数
 //更新订单状态，写入订单支付后返回的数据
 function orderhandle($parameter){
    $ordid=$parameter['out_trade_no'];//商户网站订单系统中唯一订单号
    $data['payment_trade_no']      =$parameter['trade_no']; //支付宝交易号
    $data['payment_trade_status']  =$parameter['trade_status'];
    $data['payment_notify_id']     =$parameter['notify_id'];//通知校验ID。
    $data['payment_notify_time']   =$parameter['notify_time'];
    $data['payment_buyer_email']   =$parameter['buyer_email']; //买家支付宝帐号；
    $data['ordstatus']             =1;
    $Ord=M('Orderlist');
    $Ord->where('ordid='.$ordid)->save($data);
	$data = array('status'=>'1','ispay'=>'2');//设置订单为已经支付,状态为已提交
	M('order')->where('orderid='.$ordid)->setField($data);
 } 
 
 
//-----------------------------------------------20150717-------------------------------// 
 /**
 * 根据分类id获取子分类及产品
 * @param  string $cid 分类id
 * @return array      分类产品数据
 * @author
 */
function get_subcpbycid($cid){
	$field = 'id,name,pid,title';
	//根据分类id获取子分类
	$category = D('Category')->field($field)->order('sort desc')->where('display="1" and ismenu="1" and pid='.$cid)->select();
	foreach ($category as $k => $v ) {
		$category [$k] ['doc'] = array ();
		$category [$k] ['doc'] = getproducts($v['id']);
	}	
    return  $category;
}

function get_subcpby(){   //获取一级栏目分类及分类
	$field = 'id,name,pid,title';
	//根据分类id获取子分类
	$category = D('Category')->field($field)->order('sort desc')->where('display="1" and ismenu="1" and pid="0"')->select();
	foreach ($category as $k => $v ) {
		$category [$k] ['doc'] = array ();
		$category [$k] ['doc'] = getproducts($v['id']);
	}	
    return  $category;
} 
function getproducts($cid){//根据分类id获取分类下产品，无限级别获取
	$map['category_id']=$cid;
	$map['status']=1;
	$doc = M('Document')->where($map)->order("id desc")->limit(10)->select();
	$field = 'id,name,pid,title';
	//根据分类id获取子分类
	$subcategory = D('Category')->field($field)->order('sort desc')->where('display="1" and ismenu="1" and pid='.$cid)->select();	
	if($subcategory){
		foreach ($subcategory as $k => $v ) {
			$docs = getproducts($v['id']);
			if($doc){
				$doc = array_merge($doc, $docs);
			}else{
				$doc = $docs;
			}
		}	
	}
	return $doc;
}
 




/**
 * 高频彩票列表查询
 * @author
 */
function lotteryList(){
	header("Content-Type:text/html;charset=utf-8");
    $json=@file_get_contents("http://apis.haoservice.com/lifeservice/HighLottery/list?key=75c10683f4404a3b88ccd0282ab658cb&paybyvas=false");//根据taobao ip
	$data=json_decode($json,true);
	return $data;
}


/**
 * 高频彩票开奖查询  根据彩票类型查询当天开奖结果
 * @author   http://apis.haoservice.com/lifeservice/HighLottery/query?spell=sdsyydj&key=75c10683f4404a3b88ccd0282ab658cb
 */
function lotteryQuery($spell){
	header("Content-Type:text/html;charset=utf-8");
    $json=@file_get_contents("http://apis.haoservice.com/lifeservice/HighLottery/query?spell=".$spell."&key=75c10683f4404a3b88ccd0282ab658cb");//根据taobao ip
    $data=json_decode($json,true);
	return $data;
}

/**
 * 获取最近的一组开奖结果
 */
function getnewlottery(){
	$data = lotteryList();	
	if($data['error_code']==0){
		if($data['result']){
			//是否需要打乱数组顺序，随即获取彩种
			$keys = array_keys($data['result']);   
			shuffle($keys);   
			$newdata = array();   
			foreach ($keys as $key){   
				$newdata[$key] = $data['result'][$key];   			
			}
			//是否需要打乱数组顺序，随即获取彩种
			foreach ($newdata as $k => $v ) {
				if($v['lottery']=='11选5' || $v['lottery']=='时时彩'){
					if($v['spell']){
						$data2 = lotteryQuery($v['spell']);	
						if($data2 && $data2['error_code']==0 && !empty($data2['result'])){
							$returnData = $data2['result'][0];
							$returnData['province'] = $v['province'];//城市
							$returnData['company'] = $v['company'];//彩票类型
							$returnData['info'] = $v['info'];//彩票信息
							
							return $returnData;//返回最近第一个有数据的彩票开奖结果
						}
					}	
				}
			}
		}
	}
}

/**
 * 获取开奖结果
 */
function getlotterys(){
	$datas = array();
	$data = lotteryList();	
	if($data['error_code']==0){
		if($data['result']){
			$iii = 0;
			
			//是否需要打乱数组顺序，随即获取彩种
			$keys = array_keys($data['result']);   
			shuffle($keys);   
			$newdata = array();   
			foreach ($keys as $key){   
				$newdata[$key] = $data['result'][$key];   			
			}
			//是否需要打乱数组顺序，随即获取
			
			foreach ($newdata as $k => $v ) {
				if($v['lottery']=='11选5' || $v['lottery']=='时时彩'){
					if($v['spell']){
						$data2 = lotteryQuery($v['spell']);	
						if($data2 && $data2['error_code']==0 && !empty($data2['result'])){
							$datas[$k] = $data2['result'][0];
							$datas[$k]['province'] = $v['province'];//城市
							$datas[$k]['company'] = $v['company'];//彩票类型
							$datas[$k]['info'] = $v['info'];//彩票信息							
							
							$iii++;	
						}
					}	
					if($iii==20){//有10个有结果的票种数据自动跳出
						break;
					}		
				}
			} 
		}
	}
	return $datas;
}



/**
 * 获取开奖结果---从本地数据库获取
 */
function getlotterybydata(){
	$data = D('Lottery')->select();
	
	//打乱数组顺序，随即获取彩种
	$keys = array_keys($data);   
	shuffle($keys);   
	$newdata = array();   
	foreach ($keys as $key){   
		$newdata[$key] = $data[$key];   			
	}
	//打乱数组顺序，随即获取	
    return  $newdata;
}

function getfxuser($uid){//根据id获取下级分销会员，无限级别获取
	global $memberlist; 
	$map['parent_id']=$uid; 
	$members = M('Member')->field('uid')->where($map)->select();
	//根据id获取下级用户
	if($members){
		if($memberlist){
			$memberlist = array_merge($memberlist, $members);
		}else{
			$memberlist = $members;	
		}
		foreach ($members as $k => $v ) {
			getfxuser($v['uid']);
		}	
	}
	return $memberlist;
}


function getfxyongjin($fxuids){//根据uid获取获得的所有佣金
	$map['pid']  = array('in',$fxuids);
	$zong =M('AccountLog')->where($map)->Sum('money_p');
	return $zong;
}

function getdayyongjin($fxuids){//根据uid获取获得的当日佣金
	$tdmap['pid']  = array('in',$fxuids);
	$tdmap['create_time']  = array('egt',strtotime(date('Y-m-d')));
	$tdzong =M('AccountLog')->where($tdmap)->Sum('money_p');
	return $tdzong;
}

function getself($uid){//获取指定会员佣金
	$map['uid']  = $uid;
	$map['pid']  = array('gt',0);
	//佣金总金额
	$zong =M('AccountLog')->where($map)->Sum('money_p');
	return $zong;
}

function getdayself($uid){//获取指定会员当日佣金
	$tdmap['uid']  = $uid;
	$tdmap['pid']  = array('gt',0);
	$tdmap['create_time']  = array('egt',strtotime(date('Y-m-d')));
	$tdzong =M('AccountLog')->where($tdmap)->Sum('money_p');
	$tdzong = $tdzong;
	return $tdzong;
}