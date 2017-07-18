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
 * Class HomeController
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 * @package Weixin\Controller
 * @author
 */
class HomeController extends Controller {

	/**
	 * 初始化
	 * @author
	 */
	protected function _initialize(){
//		$wx_url		= $_SERVER['REQUEST_URI'];
//		$wx_url_arr = explode('/',$wx_url);
//		$_GET['code'] = $wx_url_arr[5];


		//微信自动登录注册
		if($_GET['code']) {//微信code码

			$config = M ( "Wxsetting" )->where ( array ("id" => "1" ) )->find ();
			if($config){
				$code = $_GET['code'];

				//$get_openid_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$config['appid'].'&secret='.$config['appsecret'].'&code='.$code.'&grant_type=authorization_code';
//				$get_openid_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=wxe1cbef33f16282fd&secret=9e2cffb5f066e5e92f300ed17cc46618&code='.$code.'&grant_type=authorization_code';
				$get_openid_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx7fb456d4e2e698a4&secret=ac31f7eda547be33a2cdc7b0aec24063&code='.$code.'&grant_type=authorization_code';

				$data = $this->get_by_curl($get_openid_url);
				$data = json_decode($data);

				$openid = $data->openid;
				$access_token = $data->access_token;
			}

			//通过微信进去网站，默认登录操作
			if($openid){
				//$url =  'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$config['appid'].'&secret='.$config['appsecret'];
				$url =  'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx7fb456d4e2e698a4&secret=ac31f7eda547be33a2cdc7b0aec24063';
				$my_access_token = json_decode($this->get_by_curl($url)); //得到自己 的 access_token

				$url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$my_access_token->access_token.'&openid='.$openid.'&lang=zh_CN';

				$res = $this->get_by_curl($url);     //获取到的用户信息
				$res = json_decode($res);

				if($res->nickname){
					$_SESSION['wx_info']['nickname'] = $res->nickname;
					$_SESSION['wx_info']['headimgurl'] = $res->headimgurl;
					$_SESSION['wx_info']['sex'] = $res->sex;

				}else{
					$scope_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
					$scope_res = json_decode($this->get_by_curl($scope_url));
					$_SESSION['wx_info']['nickname'] = $scope_res->nickname;
					$_SESSION['wx_info']['headimgurl'] = $scope_res->headimgurl;
					$_SESSION['wx_info']['sex'] = $scope_res->sex;
				}

				$memberinfo = M("Member")->where(array("openid"=>$openid))->find ();
				if($memberinfo){//会员存在
					// 登录用户
					$_SESSION['parent_id'] = $_GET['parent_id'];
					$Member = D("Member");
					$Member->login($memberinfo['uid']); //登录用户
					$_SESSION['openid']=$openid;
					$_SESSION['access_token']=$access_token;
					if(empty($memberinfo['nickname']) || $memberinfo['headimgurl']){
						$user2['nickname']   = $_SESSION['wx_info']['nickname'];
						$user2['headimgurl'] = $_SESSION['wx_info']['headimgurl'];
						M('Member')->where("uid = '{$memberinfo['uid']}'")->save($user2);
					}

				}else{//会员不存在
					//将微信标示——openid存入session
					$_SESSION['parent_id'] = $_GET['parent_id'];
					$_SESSION['openid']=$openid;
					$_SESSION['access_token']=$access_token;

				}
			}
		}

		/* 读取站点配置 */
		$config = api('Config/lists');
		C($config); //添加配置

		if(!C('WEB_SITE_CLOSE')){
			$this->error('站点已经关闭，请稍后访问~');
		}
	}

	/**
	 * 用户登录检测
	 * @author
	 */
	protected function login(){
		is_login() || $this->error('您还没有登录，请先登录！', U('User/login'));
	}



	/**
	 * 空操作，用于输出404页面
	 * @author
	 */
	public function _empty(){
		$this->redirect('Index/index');
	}


	/**
	 * 根据时间的分钟值，以每10分钟一个间隔，向上取整
	 * @param $timestamp
	 * @return int
	 * @author
	 */
	function get_time_on_clock($timestamp = ''){
		$timestamp = !empty($timestamp) ? $timestamp : time();
//		$timestamp = '1492364763';
//		echo date('Y/m/d H:i:s',$timestamp);
		$time_H = date('H',$timestamp);
		$time_m = date('i',$timestamp);
//		dump($time_H);
//		dump($time_m);
		$as = 10;
		if($time_H >= 22 || $time_H < 2 || ($time_H == 2 && $time_m < 1)){
			$as = 5;
		}
//		dump($as);

		if($time_H > 2 && $time_H < 10){
			return date('Y/m/d ', $timestamp)  . '10:00:00';
		}else{
			$minute = date('i', $timestamp);
			$timestamp += $as*60;//时间戳加十分钟

			if($minute == '00'){
				$minute = $as;
			}else{
				if($minute%$as == 0){
					$minute =  ($minute/$as + 1)*$as;//分钟数除以10，然后向上取整，乘10
				}else{
					$minute =  ceil($minute/$as)*$as;//分钟数除以10，然后向上取整，乘10
				}
				if($minute == 60){//当分钟数是60，分钟数为0
					$minute = '00';
				}

			}
			return date('Y/m/d H:', $timestamp) . $minute . ':00';
		}

	}

	/**
	 * GET  POST 模拟
	 * @param $url
	 * @param bool $post
	 * @return mixed
	 * @author
	 */
	public function get_by_curl($url,$post = false){
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if($post){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
		}
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}


	/**
	 *  获取开奖期数
	 * @param $lottery_time  开奖时间
	 * @author
	 * $lottery_time
	 */
	public function getPeriod($lottery_time = ''){
		$lottery_time = !empty($lottery_time) ? $lottery_time : $this->get_time_on_clock(time());
		$timestamp = !empty($timestamp) ? $timestamp : time();

//		$timestamp = '1492279082';
//		echo date('Y/m/d H:i:s',$timestamp);
//
//		$lottery_time = $this->get_time_on_clock($timestamp);

		$time_H = date('H',$timestamp);
		$time_m = date('i',$timestamp);
//		dump($time_H);
//		dump($time_m);

		$as = 10;
		if($time_H >= 22 || $time_H < 2 || ($time_H == 2 && $time_m < 1)){
			$as = 5;
			if(($time_H >= 0 && $time_H < 2) ||  ($time_H == 2 && $time_m < 1)){
				$period = 96 + (strtotime($lottery_time) - strtotime(date('Y/m/d ').'00:00:00'))/(60*$as);
			}else{
				$period = 72 + (strtotime($lottery_time) - strtotime(date('Y/m/d ') . '22:00:00')) / (60 * $as);
			}
		}else{
			$period = (strtotime($lottery_time) - strtotime(date('Y/m/d ') . '10:00:00')) / (60 * $as);
		}
//		dump($as);
//		dump($period);
		return $period;
	}

	/**
	 * 获取用户的ip信息
	 * @author
	 */
	public function getIpInfo(){
		$arr = get_ip_address();
		$ipInfo = $arr->city;
		return $ipInfo;
	}

	/**
	 * 获取随机的临时用户id
	 * @author
	 */
	public function getRandVal1(){
		$rand_uid1 = rand(9,205);
		return $rand_uid1;
	}


	/**
	 * 获取随机的临时用户id
	 * @author
	 */
	public function getRandVal3(){
		$uid_arr = array();
		$rand_uid1 = rand(9,205);
		$uid_arr[] = $rand_uid1;
		$rand_uid2 = rand(9,205);
		while(true){
			if($rand_uid1 == $rand_uid2){
				$rand_uid2 = rand(9,205);

			}else{
				break;
			}
		}
		$uid_arr[] = $rand_uid2;

		$rand_uid3 = rand(9,205);

		while(true){
			if(($rand_uid3 == $rand_uid1) || ($rand_uid3 == $rand_uid1)){
				$rand_uid3 = rand(9,205);

			}else{
				break;
			}
		}
		$uid_arr[] = $rand_uid3;
		$uid_str = implode(',',$uid_arr);
		return $uid_str;

	}

}
