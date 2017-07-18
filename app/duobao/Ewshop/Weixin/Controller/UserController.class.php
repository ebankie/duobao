<?php
// +----------------------------------------------------------------------
// | 微信管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2017  All rights reserved.
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------

namespace Weixin\Controller;
use User\Api\UserApi;

/**
 * 用户控制器
 * 包括用户中心，用户登录及注册
 * Class UserController
 * @package Weixin\Controller
 * @author
 */
class UserController extends HomeController {

    /**
     * 检查用户是否登录
     * @author
     */
    protected function checkLogin(){
        if (is_login()) {
            $url = U('Index/index');
            header("Location: {$url}");
            exit;
        }
    }
    /**
     * 注册
     * @param string $username
     * @param string $password
     * @param string $email
     * @param string $mobile_phone
     * @author
     */
    public function register($username = '' , $password = '' , $email = '' , $mobile = ''){
		$this->checkLogin();
		
        if (!C("USER_ALLOW_REGISTER")) {
            $this->error("注册已关闭");
        }
        if (IS_POST) { //注册用户
            empty($mobile) ? $this->error("手机号码不能为空！") : '';
            $is_exist = M('UcenterMember')->where("username = {$mobile}")->find();
			$Member = D("Member");
            if($is_exist){
				//$arr['parent_id'] = $_SESSION['parent_id'];//如果会员本身就存在，通过别人的分享二维码进入不会成为别人的分享会员
				$arr['nickname'] = $_SESSION['wx_info']['nickname'];
				$arr['headimgurl'] = $_SESSION['wx_info']['headimgurl'];
				$arr['sex'] = $_SESSION['wx_info']['sex'];
				$arr['openid'] = $_SESSION['openid'];
				$Member->where(array('uid'=>$is_exist["id"]))->save($arr);
		 
                if ($Member->login($is_exist['id'])) { //登录用户
                    //跳转首页
                    $url = U("Index/index");
                    header("Location: $url");
                } else {
                    $this->error($Member->getError());
                }
            }else{
                $username             = $mobile;
                $password             = 'ewangtx'.time().$mobile;
                $phone       		  = $mobile;
                /* 调用注册接口注册用户口注册用户 */
                $User = new UserApi;
                //返回ucentermember数据表用户主键id
                $uid = $User->register($username , $password , $email , $phone);

                if ($uid > 0) { //注册成功
                    // 调用登陆
                    //$this->login($username , $password);
					if ($Member->login($uid)) { //登录用户
						$arr['parent_id'] = $_SESSION['parent_id'];
						$arr['nickname'] = $_SESSION['wx_info']['nickname'];
						$arr['headimgurl'] = $_SESSION['wx_info']['headimgurl'];
						$arr['sex'] = $_SESSION['wx_info']['sex'];
						$arr['openid'] = $_SESSION['openid'];
						$Member->where(array('uid'=>$uid))->save($arr);
		
						//跳转首页
						$url = U("Index/index");
						header("Location: $url");
					} else {
						$this->error($Member->getError());
					}					
					
                } else { //注册失败，显示错误信息
                    $this->error($this->showRegError($uid));
                }
            }
        } else {
            $_SESSION['send_code'] = random(6 , 1);//生成随机加密码。发送手机短信使用
            $this->meta_title = '会员注册';
            $this->display('register');
        }
    }

    /**
     * 登录
     * @param string $username
     * @param string $password
     * @param string $verify
     * @author
     */
    public function login($username = "" , $password = "" , $verify = ""){
		$this->checkLogin();
        if (IS_POST) {
            /* 调用UC登录接口登录 */
            $user = new UserApi;
            $uid  = $user->login($username , $password);
            if ($uid >0) { //UC登录成功
                /* 登录用户 */
                $Member = D("Member");
                if($_SESSION['parent_id']){
                    $arr['parent_id'] = $_SESSION['parent_id'];
                }
                $arr['nickname'] = $_SESSION['wx_info']['nickname'];
                $arr['headimgurl'] = $_SESSION['wx_info']['headimgurl'];
                $arr['sex'] = $_SESSION['wx_info']['sex'];
                $Member -> where(array('uid'=>$uid)) -> save($arr);
                if ($Member->login($uid)) { //登录用户
                    //跳转首页
 					$url = U("Index/index");
					header("Location: $url");
                } else {
                    $this->error($Member->getError());
                }
            } else { //登录失败
                switch ($uid) {
                    case -1:
                        $error = "用户不存在或被禁用！";
                        break; //系统级别禁用
                    case -2:
                        $error = "密码错误！";
                        break;
                    default:
                        $error = "未知错误！";
                        break; // 0-接口参数错误（调试阶段使用）
                }
                $this->error($error);
            }
        } else {
            session("url" , NULL);
            $url = $_SERVER['HTTP_REFERER'];
            session("url" , $url);
            $this->meta_title = '会员登录';
            //显示登录表单
            $this->display();
        }
    }


    /**
     * 登录
     * @param string $username
     * @param string $password
     * @author
     */
    public function doLogin($username = "", $password = ""){
        if(IS_POST){ //登录验证
            /* 检测验证码 */
//            $password             = 'ewangtx'.time().$username;
            if(empty($username)){
                $status = 0;
                $msg = '请填写用户名！';
            }elseif(empty($password)){
                $status = 0;
                $msg = '请填写密码！';
            }else{
                /* 调用UC登录接口登录 */
                $user = new UserApi;
                $uid = $user->login($username, $password);
                if(0 < $uid){ //UC登录成功
                    /* 登录用户 */
                    $Member = D("Member");
                    if($Member->login($uid)){ //登录用户
                        $status = 1;
                        $msg = 'success';
                    } else {
                        $status = 0;
                        $msg = $Member->getError();
                    }
                } else { //登录失败
                    $status = 0;
                    switch($uid) {
                        case -1: $msg = "用户不存在或被禁用！"; break; //系统级别禁用
                        case -2: $msg = "密码错误！"; break;
                        default: $msg = "未知错误！"; break; // 0-接口参数错误（调试阶段使用）
                    }
                }
            }
        }else{
            $status = 0;
            $msg = '未知错误！';
        }

        $data['status'] = $status;//状态：0登录失败 1登录成功
        $data['msg'] = $msg;
        $this->ajaxReturn($data);
    }


    /**
     * 退出登录
     * @author
     */
    public function logout(){
        if (is_login()) {
            D("Member")->logout();
        }
        $this->redirect("User/register");
    }

    /**
     * 验证码，用于登录和注册
     * @author
     */
    public function verify(){
        $verify = new \Think\Verify();
        $verify->entry(1);
    }

    /**
     * 获取用户注册错误信息
     * @param int $code 错误编码
     * @return string   错误信息
     * @author
     */
    private function showRegError($code = 0){
        switch ($code) {
            case -1:
                $error = "用户名长度必须在16个字符以内！";
                break;
            case -2:
                $error = "用户名被禁止注册！";
                break;
            case -3:
                $error = "用户名被占用！";
                break;
            case -4:
                $error = "密码长度必须在6-30个字符之间！";
                break;
            case -5:
                $error = "邮箱格式不正确！";
                break;
            case -6:
                $error = "邮箱长度必须在1-32个字符之间！";
                break;
            case -7:
                $error = "邮箱被禁止注册！";
                break;
            case -8:
                $error = "邮箱被占用！";
                break;
            case -9:
                $error = "手机格式不正确！";
                break;
            case -10:
                $error = "手机被禁止注册！";
                break;
            case -11:
                $error = "手机号被占用！";
                break;
            default:
                $error = "未知错误";
        }
        return $error;
    }


    /**
     * 发送短信 -- 注册
     * @author
     */
    public function sendphone(){
        $phone = $_POST['phone'];
        if (empty($phone)) {
            $return = array ("status" => 0 , "info" => '手机号不正确');
            header('Content-type: text/html; charset=UTF-8');
            $this->ajaxreturn($return);
            exit();
        }

        $oldphone = $_GET['oldphone'];//修改手机号操作：原手机号码
        if ($oldphone) {
            if ($phone == $oldphone) {
                $return = array ("status" => 0 , "info" => '请填写新的手机号码');
                $this->ajaxreturn($return);
                exit();
            }
        }

//        $map['mobile'] = $phone;
//        $user_id       = M('UcenterMember')->where($map)->getField('id');
//        if ($user_id) {
//            $return = array ("status" => 0 , "info" => '手机号已经被注册');
//            header('Content-type: text/html; charset=UTF-8');
//            $this->ajaxreturn($return);
//            exit();
//        }

        $mobile_code = random(4 , 1);//生成手机验证码
        $send_code   = (!empty($_SESSION['send_code'])) ? $_SESSION['send_code'] : '8888';//获取提交随机加密码
        $content     = "您的短信验证码为：" . $mobile_code . "，有效期一小时。【千亩阳光】";
        $result      = sendsmscode($phone , $content , $send_code , $mobile_code);

        $this->ajaxreturn($result);

    }



    /**
     * 发送短信 -- 设置查询密码
     * @author
     */
    public function sendCodebyPhone(){
        $uid = D('Member')->uid();
        $phone       = M('UcenterMember')->where("id = {$uid}")->getField('mobile');
        $mobile_code = random(4 , 1);//生成手机验证码
        $send_code   = $_POST['send_code'];//获取提交随机加密码
        $content     = "您的短信验证码为：" . $mobile_code . "，有效期一小时。【千亩阳光】";
        $result      = sendsmscode($phone , $content , $send_code , $mobile_code);

        $this->ajaxreturn($result);

    }

    /**
     * 验证手机是否被使用
     * @author
     */
    public function ismobile_registered(){
        $mobile = $_POST['mobile'];
        $oldphone = $_GET['oldphone'];//修改手机号操作：原手机号码
        if ($oldphone) {
            if ($mobile == $oldphone) {
                $return = json_encode(array ("status" => 1 , "info" => ''));
                echo $return;
                exit();
            }
        }

        $map['mobile'] = $mobile;
        $userid        = M('UcenterMember')->where($map)->getField('id');
        if ($userid) {
            $return = array ("status" => 0 , "info" => '手机号已经被使用');
        } else {
            $return = array ("status" => 1 , "info" => '');
        }
        $this->ajaxreturn($return);
    }

    /**
     * 检查验证码短信
     * @author
     */
    public function checkphone(){
        if (trim($_POST['miss']) == $_SESSION['mobile_code']) {
            $return = array ("status" => 1 , "info" => "");
        } else {
            $return = array ("status" => 0 , "info" => '验证码不正确');
        }
        $this->ajaxreturn($return);
    }




}
