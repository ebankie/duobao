<?php
// +----------------------------------------------------------------------
// | 微信管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2017  All rights reserved.
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------

namespace Control\Controller;

/**
 * 后台客户反馈控制器
  * @author
 */
class ClientsayController extends ControlController {

    /**
     * 客户反馈
     * author
     */
    public function index(){
        /* 查询条件初始化 */
	
        $map  = array();


			$list = $this->lists('Clientsay', $map,'id desc');

        $this->assign('list', $list);
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        
        $this->meta_title = '客户声音';
        $this->display();
    }

    /* 添加编辑公司 */
    public function edit($id = null){
		$clientsay = D('Clientsay');
        if(IS_POST){ //提交表单
            if(false !== $clientsay->update()){
                $this->success('保存成功！', U('index'));
            } else {
                $error = $clientsay->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {
            $info = $id ? $clientsay->info($id) : '';
            $this->assign('info',       $info);
            $this->meta_title = $id ? '添加客户声音' : '编辑客户声音';
            $this->display();
        }
    }

  	public function del(){
        if(IS_POST){
            $ids = I('post.id');
            $clientsay = M("Clientsay");
            if(is_array($ids)){
				 foreach($ids as $id){
				 	$clientsay->where("id='$id'")->delete();	
                }
            }
            $this->success("删除成功！");
        }else{
            $id = I('get.id');
            $db = M("Clientsay");
            $status = $db->where("id='$id'")->delete();
            if ($status){
                $this->success("删除成功！");
            }else{
                $this->error("删除失败！");
            }
        } 
    }	
	
	
    /**
     * 客户留言
     * author
     */
    public function gongyi(){
        /* 查询条件初始化 */
	
        $map  = array();
			$user = M('Member');
			$list = $this->lists('Leaveword', $map,'issee asc,id desc');
			foreach($list as $k=>$v){
				if($v['uid']){
				$list[$k]['username'] = $user->getFieldByUid($v['uid'],'nickname');	
				}else{
				$list[$k]['username'] = '匿名';		
				}
			}

        $this->assign('list', $list);
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        
        $this->meta_title = '客户留言';
        $this->display();
    }	


    /* 添加编辑留言 */
    public function editleaveword($id = null){
		$leaveword = D('Leaveword');
        if(IS_POST){ //提交表单
            if(false !== $leaveword->update()){
                $this->success('保存成功！', U('leaveword'));
            } else {
                $error = $leaveword->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {
			$user = M('Member');
            $info = $id ? $leaveword->info($id) : '';
			$username = $user->getFieldByUid($info['uid'],'nickname');
			if($id){
				$map['id'] = $id;
				$data['issee'] = 1;
				$leaveword->where($map)->save($data);
			}
			if($username){
				$info['username'] = $username;
			}else{
				$info['username'] = '匿名';
			}
            $this->assign('info',       $info);
            $this->meta_title = $id ? '不存在此留言' : '查看留言';
            $this->display();
        }
    }	
	
  	public function delleaveword(){
        if(IS_POST){
            $ids = I('post.id');
            $leaveword = M("Leaveword");
            if(is_array($ids)){
				 foreach($ids as $id){
				 	$leaveword->where("id='$id'")->delete();	
                }
            }
            $this->success("删除成功！");
        }else{
            $id = I('get.id');
            $db = M("Leaveword");
            $status = $db->where("id='$id'")->delete();
            if ($status){
                $this->success("删除成功！");
            }else{
                $this->error("删除失败！");
            }
        } 
    }		
	
	
}



	