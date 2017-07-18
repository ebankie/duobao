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
 * 用户模型
 * @author ew_xiaoxiao
 */

class MemberModel extends Model {

    protected $_validate = array(
        array('nickname', '1,16', '昵称长度为1-16个字符', self::EXISTS_VALIDATE, 'length'),
        array('nickname', '', '昵称被占用', self::EXISTS_VALIDATE, 'unique'), //用户名被占用
    );

    public function lists($status = 1, $order = 'uid DESC', $field = true){
        $map = array('status' => $status);
        return $this->field($field)->where($map)->order($order)->select();
    }

    /**
     * 登录指定用户
     * @param  integer $uid 用户ID
     * @return boolean      ture-登录成功，false-登录失败
     */
    public function login($uid){
        /* 检测是否在当前应用注册 */
        $user = $this->field(true)->find($uid);
        if(!$user || 1 != $user['status']) {
            $this->error = '用户不存在或已被禁用！'; //应用级别禁用
            return false;
        }

        //记录行为
        action_log('user_login', 'member', $uid, $uid);

        /* 登录用户 */
        $this->autoLogin($user);
        return true;
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
     * 新增或更新一个文档
     * @return boolean fasle 失败 ， int  成功 返回完整的数据
     * @author ew_xiaoxiao
     */
    public function updatem($data){


        /* 添加或新增基础内容 */
        if(empty($data['uid'])){ //新增数据
            $id = $this->add($data); //添加基础内容
            if(!$id){
                $this->error = '新增模型出错！';
                return false;
            }
        } else { //更新数据
            $status = $this->save($data); //更新基础内容
            if(false === $status){
                $this->error = '更新模型出错！';
                return false;
            }
        }
        // 清除模型缓存数据
       S('DOCUMENT_MODEL_LIST', null);

        //记录行为
      action_log('update_model','model',$data['uid'] ? $data['uid'] : $id,UID);

        //内容添加或更新完成
        return $data;
    }

	 /**
     * 获取优惠券详细信息
     * @param  milit   $id 优惠券ID或标识
     * @param  boolean $field 查询字段
     * @return array     优惠券信息
     * @author ew_xiaoxiao
     */
    public function info($id, $field = true){
        /* 获取优惠券信息 */
        $map = array();
        if(is_numeric($id)){ //通过ID查询
            $map['uid'] = $id;
        } else { //通过标识查询
            $map['name'] = $id;
        }
        return $this->field($field)->where($map)->find();
    }
	
	
	
}
