<?php
// +----------------------------------------------------------------------
// | 微信管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2017  All rights reserved.
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------

namespace Weixin\Controller;
use Think\Controller;

/**
 * Class AutoController
 * @package Weixin\Controller
 * @author
 */
class AutoController extends HomeController {

    /**
     * 自动获取5位随机数
     * @author
     */
    public function doAuto(){
        //开奖时间是 10:00 - 2:00
        $time_H = date('H');
        $time_m = date('i');
        if($time_H >= 10 || $time_H < 2 || ($time_H == 2 && $time_m < 1)){
//            echo  date('Y-m-d H:i:s')."\n";

            $lottery_time = $this->get_time_on_clock(time());//下期开奖时间

            $lottery_time_stamp = strtotime($lottery_time);

            $data['create_time'] = $lottery_time;//开奖时间
            $data['period'] = $this->getPeriod($lottery_time);//开奖期数
            $data['time'] = $lottery_time_stamp;//开奖时间

            if(date('H:i:s',$lottery_time_stamp) != '02:05:00'){
                $map['create_time'] = $lottery_time;//开奖时间
                $map['period'] = $this->getPeriod($lottery_time);//开奖期数
                $is_exist = M('WinCode')->where($map)->find();
                (!$is_exist) ?  M('WinCode')->add($data) : ''; //插入数据
            }
            //判断是否有code为0的数据
            $is_exist_code = M('WinCode')->where("code = 0 and time < {$lottery_time_stamp}")->order('id desc')->select();
//            echo M()->getLastSql();
//            dump($is_exist_code);
            if($is_exist_code){
                foreach($is_exist_code as $key => $val){
					$this->setOrderStatus($val);
				}
				//if($is_exist_code[0]){
					//$this->setOrderStatus($is_exist_code[0]);
				//}  
            }
        }
		
		if($time_H == 22 && $time_m < 10){
			//清空数据表
			$sql = 'truncate table ewshop_lottery';
			M('Lottery')->execute($sql);
            //清空数据表

			$lotterys = getlotterys();//获取所有开奖结果（10个结果）
			//将22点的接口数据存入数据库
			foreach($lotterys as $key=>$val){
				if($val){
					 M('Lottery')->add($val);
				}
			}
			//将22点的接口数据存入数据库
		}
    }
    //  */1 * * * * /usr/bin/curl http://duobao.akng.net/Weixin/Auto/doAuto  >> /opt/web/mydata.log



    /**
     * 设置订单状态
     * @author
     */
    public function setOrderStatus($code_arr){
        $data = $this->getCodeRand($code_arr['create_time']);
		if($data){
			
			$lottery_nums = $data['lottery_nums'];
			
			$code = $data['code'];
			$code_56 =  $code%56 + 1;
			$code_110   =  $code%110 + 1;
	
			//更新数据
			$data2['id'] = $code_arr['id'];
			$data2['code'] = $lottery_nums;//开奖号码
			$data2['code_56'] = $code_56;
			$data2['code_56_type'] = $code_56 <= 28 ? 1 : 2;
			$data2['code_110']   = $code_110;
			$data2['code_110_type'] = $code_110 <= 55 ? 1 : 2;
			$data2['czh']   = $data['lottery'];//彩种名称
			$data2['cno']   = $data['lottery_no'];//开奖号码
			$data2['ctime']   = $data['lottery_time'];//开奖时间
			$data2['province']   = $data['province'];//彩种所在城市
			$data2['company']   = $data['company'];//彩票类型
			$data2['info']   = $data['info'];//彩票信息	
	
			$res2 = M('WinCode')->save($data2);
			//设置获胜者
			if($res2){
				$order_list = M('WinOrder')->where(array('lottery_time'=>$code_arr['create_time'],'status'=>1))->select();
				if($order_list){
					foreach($order_list as $key=>$val){
						if(($val['goods_type'] == 1 && $val['type'] == $data2['code_56_type']) || ($val['goods_type'] == 2 && $val['type'] == $data2['code_110_type'])){
							$exchangeData['uid'] = $val['uid'];
							$exchangeData['goods_id'] = $val['goods_id'];//购买商品
							$exchangeData['order_id'] = $val['id'];
							$exchangeData['exchange_number'] = $this->randStr();
							$exchangeData['buy_num'] = $val['num'];//购买数量
							$exchangeData['buy_time'] = $val['create_time'];//购买时间
							$exchangeData['create_time']     = time();
							$exchangeData['city']     = $this->getIpInfo();
							M('WinExchange')->add($exchangeData);
						}
					}
				}
			}
		}

        echo  date('Y-m-d H:i:s').':This code is '.$code.'.'."\n";
    }



    /**
     *  获取5位随机数
     * @param $lottery_time
     * @author
     */
    public function getCodeRand($lottery_time = ''){

//        $lottery_time = '2017/04/16 23:25:00';
        $num_56_small = M('WinOrder')->where(array('lottery_time'=>$lottery_time,'goods_type'=>1,'type'=>1,'status'=>1))->sum('money');//买小（28元）的订单数的总金额
        $num_56_big   = M('WinOrder')->where(array('lottery_time'=>$lottery_time,'goods_type'=>1,'type'=>2,'status'=>1))->sum('money');//买大（28元）的订单数的总金额
        $num_110_small = M('WinOrder')->where(array('lottery_time'=>$lottery_time,'goods_type'=>2,'type'=>1,'status'=>1))->sum('money');//买小（55元）的订单数的总金额
        $num_110_big   = M('WinOrder')->where(array('lottery_time'=>$lottery_time,'goods_type'=>2,'type'=>2,'status'=>1))->sum('money');//买大（55元）的订单数的总金额
        if($num_56_small > $num_56_big){
            $code_56_val = 2;//选择大的赢
        }elseif($num_56_small < $num_56_big){
            $code_56_val = 1;//选择小的赢
        }else{
            $code_56_val = 0;//大小均可
        }

        if($num_110_small > $num_110_big){
            $code_110_val = 2;//选择大的赢
        }elseif($num_110_small < $num_110_big){
            $code_110_val = 1;//选择小的赢
        }else{
            $code_110_val = 0;//大小均可
        }

        //$data = $this->getCodeRand2($code_56_val,$code_110_val);//开启优势规则
		$data = $this->getCodeRand2(0,0);//正常开奖
        return $data;
    }


    /**
     *
     * @param $code_56_val
     * @param $code_110_val
     * @author
     */
    public function getCodeRand2($code_56_val = 0,$code_110_val = 0){
		$data = array();
		$time_H = date('H');
        if($code_56_val == 0 && $code_110_val== 0){
			if($time_H > 23 || $time_H < 2){
				//从数据库中直接获取数据
				$lotterys = getlotterybydata();
				foreach($lotterys as $key=>$val){
					$lottery = $val;
					break;
				}
			}else{
				$lottery = getnewlottery();//获取最近的一次开奖，大小无所谓
			}
			if(!empty($lottery)){
				$lottery_nums = $lottery['lottery_nums'];
				$lottery_nums = str_replace(',', '', $lottery_nums);
				//$lottery_nums = str_replace('0', '', $lottery_nums);
				//if(strlen($lottery_nums)>5){
				//	$lottery_nums = substr($lottery_nums, -5);
				//}
				$code = intval($lottery_nums);
				$lottery['code']=$code;
				
				$data = $lottery;		
			}
        }else{
			if($time_H > 23 || $time_H < 2){
				//从数据库中直接获取数据
				$lotterys = getlotterybydata();//获取所有开奖结果（10个结果）
			}else{
				$lotterys = getlotterys();//获取所有开奖结果（10个结果）
			}
			if(!empty($lotterys)){
				foreach($lotterys as $key=>$val){
					$lottery_nums = $val['lottery_nums'];
					$lottery_nums = str_replace(',', '', $lottery_nums);
					//$lottery_nums = str_replace('0', '', $lottery_nums);
					//if(strlen($lottery_nums)>5){
					//	$lottery_nums = substr($lottery_nums, -5);
					//}
					$code = intval($lottery_nums);
					$val['code']=$code;
					$data = $val;	
					$code_56 =  $code%56 + 1;
					$code_110   =  $code%110 + 1;
					if($code_56_val == 0 && $code_110_val == 1){
						if($code_110 <= 56) break;
					}elseif($code_56_val == 0 && $code_110_val == 2){
						if($code_110>56 && $code_110<=110) break;
					}elseif($code_56_val == 1 && $code_110_val ==0){
						if($code_56<=28) break;
					}elseif($code_56_val == 1 && $code_110_val ==1){
						if($code_56<=28 && $code_110<=56) break;
					}elseif($code_56_val == 1 && $code_110_val ==2){
						if($code_56<=28 && $code_110>56 && $code_110<=110) break;
					}elseif($code_56_val == 2 && $code_110_val ==0){
						if($code_56>28 && $code_56<=56) break;
					}elseif($code_56_val == 2 && $code_110_val ==1){
						if($code_56>28 && $code_56<=56 && $code_110<=56) break;
					}elseif($code_56_val == 2 && $code_110_val ==2){
						if($code_56>28 && $code_56<=56 && $code_110>56 && $code_110<=110) break;
					}	
				}	
			}
        }
		/***
		if(empty($data) || count($data)==0){//接口获取数据失败时调用内部算法
			$val['czh']   = '时时彩';//彩种名称
			//$val['cno']   = $data['lottery_no'];//开奖号码
			$val['ctime']   = date('Y-m-d H:m:s',$time);;//开奖时间
			$val['province']   = '安徽';//彩种所在城市
			$val['company']   = '体彩';//彩票类型
			$val['info']   = '每日：100 期，开奖频率：10分钟';//彩票信息	
			
			if($code_56_val == 0 && $code_110_val== 0){
				$code = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
				$val['lottery_nums']=$code;
				$data = $val;
			}else{		
				while(true){
					$code = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);//原有规则
					$val['lottery_nums']=$code;
					$data = $val;				
					$code_56 =  $code%56 + 1;
					$code_110   =  $code%110 + 1;
					if($code_56_val == 0 && $code_110_val == 1){
						if($code_110 <= 56) break;
					}elseif($code_56_val == 0 && $code_110_val == 2){
						if($code_110>56 && $code_110<=110) break;
					}elseif($code_56_val == 1 && $code_110_val ==0){
						if($code_56<=28) break;
					}elseif($code_56_val == 1 && $code_110_val ==1){
						if($code_56<=28 && $code_110<=56) break;
					}elseif($code_56_val == 1 && $code_110_val ==2){
						if($code_56<=28 && $code_110>56 && $code_110<=110) break;
					}elseif($code_56_val == 2 && $code_110_val ==0){
						if($code_56>28 && $code_56<=56) break;
					}elseif($code_56_val == 2 && $code_110_val ==1){
						if($code_56>28 && $code_56<=56 && $code_110<=56) break;
					}elseif($code_56_val == 2 && $code_110_val ==2){
						if($code_56>28 && $code_56<=56 && $code_110>56 && $code_110<=110) break;
					}
				}	
			}
		}
		***/
		return $data;
    }


    /**
     * 随机产生六位数兑换码
     * @param int $len
     * @param string $format
     * @return string
     * @author
     */
    public function randStr($len=6,$format='ALL') {
        switch($format) {
            case 'ALL':
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'; break;
            case 'CHAR':
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; break;
            case 'NUMBER':
                $chars='0123456789'; break;
            default :
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                break;
        }
        mt_srand((double)microtime()*1000000*getmypid());
        $password="";
        while(strlen($password)<$len)
            $password.=substr($chars,(mt_rand()%strlen($chars)),1);
        return $password;
    }

    /**
     * 设置虚拟的中奖记录
     * @author
     */
    public function setOrderLogRand(){
        //开奖时间是 10:00 - 2:00
        $time_H = date('H');
        $time_i = date('i');
        if(($time_H == 10 && $time_i >= 5)  || $time_H >= 11 || $time_H < 2) {
            $data['is_virtual'] = 1;
            $goods_arr = M('Document')->where("category_id = 217 and status = 1")->select();//获取所有的商品
            foreach ($goods_arr as $key => $val) {
                $data['uid'] = rand(9, 205);
                $data['goods_id'] = $val['id'];
                $data['buy_num'] = rand(1, 5);
                $data['buy_time'] = time() - rand(10,300);
                $data['city'] = $this->getRandCity();
                M('WinExchange')->add($data);
            }
            echo  date('Y-m-d H:i:s').':To write  is successful.'."\n";

        }
    }

    //  */5 * * * * /usr/bin/curl http://duobao.akng.net/Weixin/Auto/setOrderLogRand  >> /opt/web/order_data.log

    /**
     * 随机获取用户的城市
     * @author gechuan <gechuan@ewangtx.com> 天行云
     */
    public function getRandCity(){
        $id = rand(1,65);
        $cityName = M('Hotcity')->where(array('id'=>$id))->getField('name');
        return $cityName;
    }


//    public function tempdo(){
//        $list = M('WinExchange')->select();
//        dump($list);
//        foreach ($list as $item) {
//            $name = $this->getRandCity();
//            $res = M('WinExchange')->where("id = {$item['id']}")->setField('city',$name);
//            dump($res);
//        }
//    }

    public function test(){
		$lotterys = getlotterybydata();//获取所有开奖结果（10个结果）
		foreach($lotterys as $key=>$val){
			$lottery = $val;
			break;
		}
		
		
		print_r($lottery);
    }
}
