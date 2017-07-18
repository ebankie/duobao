<?php
// +----------------------------------------------------------------------
// | 微信管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2017  All rights reserved.
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------
namespace Control\Controller;
use Common\Wechat\Wechat;

class WechatController{
	public function init() {
		$config = M ( "Wxsetting" )->where ( array ("id" => "1" ) )->find ();
		
		$options = array (
				'token' => $config ["token"], // 填写你设定的key
				'appid' => $config ["appid"], // 填写高级调用功能的app id
				'appsecret' => $config ["appsecret"], // 填写高级调用功能的密钥
				);
		$weObj = new Wechat( $options );
		return $weObj;
	}
	public function index() {
	    $weObj = $this->init ();
		$weObj->valid ();
		
		$type = $weObj->getRev ()->getRevType ();
		
		$member = M ( "Member" )->where (array('openid'=>$weObj->getRevFrom()))->find ();//获取会员信息
/*	
	if($member){//会员存在
			//暂不做处理
		}else{
			//不存在，插入会员数据
			$userinfo = $weObj->getUserInfo($weObj->getRevFrom());
			$member['openid']=$weObj->getRevFrom();
			$member['nickname']=$userinfo['nickname'];
			$member['sex']=$userinfo['sex'];
			//$member['province']=$userinfo['province'];
			//$member['city']=$userinfo['city'];
			//$member['country']=$userinfo['country'];
			$member['login']=1;//会员登录次数
			$member['status']=1;//会员状态 1启用 0禁用
			$member['reg_time']=time();//注册时间
			$member['last_login_time']=time();//最后一次登录时间
			$uid = M ("Member")->add($member);

			$ucmember['id'] = $uid;
			$ucmember['username'] = $userinfo['nickname']."_".rand(10,100);
			$ucmember['status']=1;//会员状态 1启用 0禁用
			//$ucmember['face']=$userinfo['headimgurl'];//头像
			$ucmember['reg_time'] =time();//注册时间
			$ucmember['last_login_time'] =time();//最后登录时间
			$ucmember['update_time'] =time();//更新时间
			M ("UcenterMember")->add($ucmember);
		}	
*/

		switch ($type) {
			case Wechat::MSGTYPE_TEXT ://输入文字模式
				$content = $weObj->getRev()->getRevContent();//获取接收消息内容正文
				
				//消息记录  m_type消息类型：0用户消息 1微信回复
//				M ("Wxmsglog")->add(array('openid'=>$weObj->getRevFrom(),'message'=>$content,'m_type'=>'0','status'=>'0','datetime'=>time()));
			case Wechat::MSGTYPE_EVENT ://点击事件模式
				$eventype = $weObj->getRev ()->getRevEvent ();//获取接收事件推送
				/***
				//显示触发事件返回值信息-测试开发使用
				$contentStr = "";
				foreach($eventype as $ket => $child){
				$contentStr .= $ket."=".$child."\n";
				}
				$weObj->text ($contentStr)->reply ();
				exit ();
				break;
				***/
							
				if ($eventype ['event'] == "CLICK") {//点击事件		
					$content = '菜单点击事件【'.$eventype ['key'].'】';
					$wxkey = $eventype ['key'];
				}elseif ($eventype['event'] == "subscribe") {//关注事件
					$content = '用户关注';
					$wxkey = $eventype['event'];
				}else{
					$content = $eventype['key'];
					$wxkey = $eventype ['key'];
				}
				
				$keyword = M("Wxkey")->where(array("keyword" =>$wxkey))->find();	//匹配关键字规则
				if($keyword['type']=='2'){//文本信息
					$weObj->text ($keyword["content"])->reply ();
				}elseif($keyword['type']=='1'){//图文信息
					$news = M ("Wxreply")->where ( array ("kid" => $keyword['id']) )->select ();
					if($news){
						for($i = 0; $i < count ( $news ); $i ++) {
							if($news[$i]["link"]){//自定义链接
								$url = $news[$i]["link"].'&uid=' . $weObj->getRevFrom ();
							}else{//内部链接
								$url = "http://".$_SERVER['SERVER_NAME']."/Weixin/Weixin/show/id/".$news[$i]["id"];
							}
							$picUrl = "http://".$_SERVER['SERVER_NAME'].get_cover($news[$i]["thumb"],'path');//图片地址
							$newsArr[$i] = array(
								'Title' => $news[$i]["title"],
								'Description' => $news[$i]["description"],
								'PicUrl' => $picUrl,
								'Url' => $url
							);
						}
						$weObj->getRev ()->news ( $newsArr )->reply ();							
					}
				}				
				
				//消息记录  m_type消息类型：0用户消息 1微信回复
//				M ("Wxmsglog")->add(array('openid'=>$weObj->getRevFrom(),'message'=>$content,'m_type'=>'0','status'=>'0','datetime'=>time()));
				exit ();
				break;
			case Wechat::MSGTYPE_LOCATION ://发送地理位置模式
				$eventype = $weObj->getRev ()->getRevGeo ();//获取接收地理位置
				/**
				//显示触发事件返回值信息-测试开发使用				
				$contentStr = "";
				foreach($eventype as $ket => $child){
				$contentStr .= $ket."=".$child."\n";
				}
				$weObj->text ($contentStr)->reply ();
				exit ();
				break;	
				**/
						
				/***发送地理位置业务处理代码***/
				
				/***发送地理位置业务处理代码***/
				$weObj->text ( "发送地理位置信息成功！" )->reply ();
				exit ();
				break;
			default ://其他发送模式
				$weObj->text ( "help info" )->reply ();
		}

	}

}