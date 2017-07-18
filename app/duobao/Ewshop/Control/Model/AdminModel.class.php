<?php
// +----------------------------------------------------------------------
// | 微信管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2017  All rights reserved.
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------

namespace Control\Model;
use Think\Model;

/**
 * 管理员模型
 * @author ew_xiaoxiao
 */

class AdminModel extends Model {

    protected $_validate = array(
        array('username', '1,16', '用户名长度为1-16个字符', self::EXISTS_VALIDATE, 'length'),
		array('username', 'checkName', '用户名已经被使用', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH),//用户名被占
		array('password', 'require', '密码不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('password', 'checkpass', '两次密码输入不一致', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH),
    );
	/* 用户模型自动完成 */
	protected $_auto = array(
		array('password', 'think_ucenter_md5', self::MODEL_BOTH, 'callback', UC_AUTH_KEY),
	);
	
    public function lists($status = 1, $order = 'id DESC', $field = true){
        $map = array('status' => $status);
        return $this->field($field)->where($map)->order($order)->select();
    }
	
    public function checkName(){
		$username        = I('post.username');
		$map = array('username' => $username);
        $res = $this->where($map)->getField('uid');
        if ($res) {
            return false;
        }
		return true;
    }
    public function checkpass(){
		$password        = I('post.password');
		$repassword        = I('post.repassword');
        if ($password!=$repassword) {
            return false;
        }
		return true;
    }	
    /**
     * 更新信息
     * @return boolean 更新状态
     * @author ew_xiaoxiao
     */
    public function update(){
        $data = $this->create();
        if(!$data){ //数据对象创建错误
            return false;
        }

        /* 添加或更新数据 */
        if(empty($data['uid'])){
            $res = $this->add();
        }else{
            $res = $this->save();
        }

        //记录行为
        action_log('update_Admin', 'Admin', $data['uid'] ? $data['uid'] : $res, UID);

        return $res;
    }	
    /**
     * 登录指定用户
     * @param  integer $uid 用户ID
     * @return boolean      ture-登录成功，false-登录失败
     */
    public function login($username = null, $password = null){
		$map['username'] = $username;
		/* 获取用户数据 */
		$user = $this->where($map)->find();
	//	echo $_SERVER['HTTP_HOST'];
//		die();
		if($_SERVER['HTTP_HOST'] == 'daili.akng.net' && $user['groupid'] != 7){
		$this->error = '没有权限';
		return false;

		}


		if(is_array($user) && $user['status']==1){
			/* 验证用户密码 */			
			if($this->think_ucenter_md5($password, UC_AUTH_KEY) === $user['password']){
				//记录行为
				action_log('user_login', 'admin', $uid, $uid);
				/* 登录用户 */
				$this->autoLogin($user);
				return true;					
			} else {
				$this->error = '密码错误'; //密码错误
				return false;
			}
			
		} else {
			$this->error = '用户不存在或被禁用'; //用户不存在或被禁用
			return false;
		}		

    }

    /**
     * 注销当前用户
     * @return void
     */
    public function logout(){
        session('user_auth', null);
        session('user_auth_sign', null);
    }

    /**
     * 自动登录用户
     * @param  integer $user 用户信息数组
     */
    private function autoLogin($user){
        /* 更新登录信息 */
        $data = array(
            'uid'             => $user['uid'],
            'login'           => array('exp', '`login`+1'),
            'last_login_time' => NOW_TIME,
            'last_login_ip'   => get_client_ip(1),
        );
        $this->save($data);

        /* 记录登录SESSION和COOKIES */
        $auth = array(
            'uid'             => $user['uid'],
            'username'        => $user['nickname'],
            'last_login_time' => $user['last_login_time'],
        );

        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));

    }

    public function getNickName($uid){
        return $this->where(array('uid'=>(int)$uid))->getField('nickname');
    }
	
	 /**
     * 获取详细信息
     * @param  milit   $id ID或标识
     * @param  boolean $field 查询字段
     * @return array     信息
     * @author ew_xiaoxiao
     */
    public function info($id, $field = true){
        $map = array();
        if(is_numeric($id)){ //通过ID查询
            $map['uid'] = $id;
        } else { //通过标识查询
            $map['name'] = $id;
        }
        return $this->field($field)->where($map)->find();
    }
	
	/**
	 * 系统非常规MD5加密方法
	 * @param  string $str 要加密的字符串
	 * @return string 
	 */
	function think_ucenter_md5($str, $key = 'ThinkUCenter'){
		return '' === $str ? '' : md5(sha1($str) . $key);
	}	
	
}
