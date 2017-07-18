<?php
// +----------------------------------------------------------------------
// | 微信管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2017  All rights reserved.
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------

namespace Control\Controller;
use User\Api\UserApi;
/**
 * 后台管理员控制器
 * @author ew_xiaoxiao
 */
class AdminController extends ControlController {

    /**
     * 管理员管理首页
     * @author ew_xiaoxiao
     */
    public function index(){
        $list   = $this->lists('Admin', $map);
		
		$grouplist = $this->lists('AuthGroup',array('module'=>'admin'));	
		$groupid = array();
		if($grouplist){
			foreach ($grouplist as $k=>$v){
				$groupid[$v['id']]=$v['title'];
			}
		}	
        int_to_string($list,array('status'=>array(1=>'正常',0=>'禁用'),'groupid'=>$groupid));

        $this->assign('_list', $list);
        $this->meta_title = '管理员管理';
        $this->display();
    }
    /**
     * 会员状态修改
     * @author ew_xiaoxiao
     */
    public function changeStatus($method=null){
        $id = array_unique((array)I('id',0));
        if( in_array(C('USER_ADMINISTRATOR'), $id)){
            $this->error("不允许对超级管理员执行该操作!");
        }
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $map['uid'] =   array('in',$id);
		$Admin = D('Admin');
        switch ( strtolower($method) ){
            case 'forbiduser':
                $this->forbid('Admin', $map );
                break;
            case 'resumeuser':
                $this->resume('Admin', $map );
                break;
            case 'deleteuser':
                //$this->delete('Admin', $map );
				$Admin->where($map)->delete();	
				$this->success('删除管理员成功！');
                break;
            default:
                $this->error('参数非法');
        }
    }

    public function add(){
        if(IS_POST){
			$Admin = D('Admin');
            if(false !== $Admin->update()){
                $this->success('新增成功！', U('index'));
            } else {
                $error = $Admin->getError();
				$this->error(empty($error) ? '未知错误！' : $error);
            }      
        } else {
			$grouplist = $this->lists('AuthGroup',array('module'=>'admin'),'id asc');
			$this->assign( '_grouplist', $grouplist );//角色列表			
            $this->meta_title = '新增管理员';
            $this->display();
        }
    }
	
	 public function edit($id = null){
		$Admin = D('Admin');
        if(IS_POST){ 
			$data['uid'] = $_POST['uid'];
			$data['username'] = $_POST['username'];
			$data['nickname'] = $_POST['nickname'];
			$data['email'] = $_POST['email'];
			$data['groupid'] = $_POST['groupid'];
			
			$password = $_POST['password'];
			$repassword = $_POST['repassword'];
			/* 检测密码 */
			if(!empty($password)){
				$data['password'] = $Admin->think_ucenter_md5($password,UC_AUTH_KEY);
			}			
			if($password != $repassword){
				$this->error('密码和重复密码不一致！');
			}
			
			if(false !== $Admin->save($data)){
				$this->success('编辑成功！', U('index'));
			} else {
				$error = $Admin->getError();
				$this->error(empty($error) ? '未知错误！' : $error);
			}
        } else {
			$grouplist = $this->lists('AuthGroup',array('module'=>'admin'),'id asc');
			$this->assign( '_grouplist', $grouplist );//角色列表
			
			$info = $id ? $Admin->info($id) : '';
            $this->assign('info',       $info);
            $this->meta_title = '编辑管理员';
            $this->display();
        }
    }
			
    /**
     * 修改昵称初始化
     * @author ew_xiaoxiao
     */
    public function updateNickname(){
        $nickname = M('Admin')->getFieldByUid(UID, 'nickname');
        $this->assign('nickname', $nickname);
        $this->meta_title = '修改昵称';
        $this->display('updatenickname');
    }

    /**
     * 修改昵称提交
     * @author ew_xiaoxiao
     */
    public function submitNickname(){
        //获取参数
        $nickname = I('post.nickname');
        $password = I('post.password');
        empty($nickname) && $this->error('请输入昵称');
        empty($password) && $this->error('请输入密码');
		
		$Member =   D('Admin');
		//密码验证
		$passwd = $Member->getFieldByUid(UID, 'password');
		$password = $Member->think_ucenter_md5($password, UC_AUTH_KEY);
		if($password!==$passwd){
			$this->error('密码不正确');
		}
		
        $data['nickname'] = $nickname;
        $res = $Member->where(array('uid'=>UID))->save($data);

        if($res){
			$user               =   session('user_auth');
            $user['username']   =   $data['nickname'];
            session('user_auth', $user);
            session('user_auth_sign', data_auth_sign($user));
            $this->success('修改昵称成功！');
        }else{
            $this->error('修改昵称失败！');
        }
    }

    /**
     * 修改密码初始化
     * @author ew_xiaoxiao
     */
    public function updatePassword(){
        $this->meta_title = '修改密码';
        $this->display('updatepassword');
    }

    /**
     * 修改密码提交
     * @author ew_xiaoxiao
     */
    public function submitPassword(){
        //获取参数
        $password   =   I('post.old');
        empty($password) && $this->error('请输入原密码');
		
        $newpassword = I('post.password');
        empty($newpassword) && $this->error('请输入新密码');
        $repassword = I('post.repassword');
        empty($repassword) && $this->error('请输入确认密码');

        if($newpassword !== $repassword){
            $this->error('您输入的新密码与确认密码不一致');
        }

		$Member =   D('Admin');
		//密码验证
		$passwd = $Member->getFieldByUid(UID, 'password');
		$password = $Member->think_ucenter_md5($password, UC_AUTH_KEY);
		if($password!==$passwd){
			$this->error('原密码不正确');
		}

        $data['password'] = $Member->think_ucenter_md5($newpassword, UC_AUTH_KEY);
        $res = $Member->where(array('uid'=>UID))->save($data);

        if($res){
            D('Admin')->logout();
            session('[destroy]');
            $this->success('修改密码成功，请重新登录！', U('login'));
        }else{
            $this->error('修改密码失败！');
        }
    }

    /**
     * 用户行为列表
     * @author ew_xiaoxiao
     */
    public function action(){
        //获取列表数据
        $Action =   M('Action')->where(array('status'=>array('gt',-1)));
        $list   =   $this->lists($Action);
        int_to_string($list);
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);

        $this->assign('_list', $list);
        $this->meta_title = '用户行为';
        $this->display();
    }

    /**
     * 新增行为
     * @author ew_xiaoxiao
     */
    public function addAction(){
        $this->meta_title = '新增行为';
        $this->assign('data',null);
        $this->display('editaction');
    }

	 /**
     * 分销商设置
     * @author ew_xiaoxiao
     */
    public function power($id = null){
        $member = D('Member');
        if(IS_POST){ //提交表单
			$map['uid'] = $_POST['id'];
			$data['isdis'] = $_POST['isdis'];
            if(false !== $member->where($map)->save($data)){
                $this->success('编辑成功！', U('index'));
            } else {
                $error = $member->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {
            $info = $id ? $member->info($id) : '';
            $this->assign('info',       $info);
            $this->meta_title = '设置分销商';
            $this->display();
        }	
    }
    /**
     * 编辑行为
     * @author ew_xiaoxiao
     */
    public function editAction(){
        $id = I('get.id');
        empty($id) && $this->error('参数不能为空！');
        $data = M('Action')->field(true)->find($id);

        $this->assign('data',$data);
        $this->meta_title = '编辑行为';
        $this->display('editaction');
    }

    /**
     * 更新行为
     * @author ew_xiaoxiao
     */
    public function saveAction(){
        $res = D('Action')->update();
        if(!$res){
            $this->error(D('Action')->getError());
        }else{
            $this->success($res['id']?'更新成功！':'新增成功！', Cookie('__forward__'));
        }
    }



    /**
     * 获取用户注册错误信息
     * @param  integer $code 错误编码
     * @return string        错误信息
     */
    private function showRegError($code = 0){
        switch ($code) {
            case -1:  $error = '用户名长度必须在16个字符以内！'; break;
            case -2:  $error = '用户名被禁止注册！'; break;
            case -3:  $error = '用户名被占用！'; break;
            case -4:  $error = '密码长度必须在6-30个字符之间！'; break;
            case -5:  $error = '邮箱格式不正确！'; break;
            case -6:  $error = '邮箱长度必须在1-32个字符之间！'; break;
            case -7:  $error = '邮箱被禁止注册！'; break;
            case -8:  $error = '邮箱被占用！'; break;
            case -9:  $error = '手机格式不正确！'; break;
            case -10: $error = '手机被禁止注册！'; break;
            case -11: $error = '手机号被占用！'; break;
            default:  $error = '未知错误';
        }
        return $error;
    }

}