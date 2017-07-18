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
 * 后台留言管理控制器
 * @author ew_xiaoxiao
 */
class CommentController extends ControlController {

    /**
     * 留言管理列表
     * @author ew_xiaoxiao
     */
    public function index(){ 
		$content=I('get.content');  
		if($content){
			$where="content like '%".$content."%'"; 
		}
     	 
         $list = $this->lists('Comment',$where ,'id desc');
         $this->assign('list', $list);
		 $this->meta_title = '评论管理';
         $this->display();
    }
 
    /* 编辑评论 */
    public function edit($id = null){
        $Comment = D('Comment');

        if(IS_POST){ //提交表单
            if(false !== $Comment->update()){
                $this->success('编辑成功！', U('index'));
            } else {
                $error = $Comment->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {

            /* 获取留言信息 */
            $info = $id ? $Comment->info($id) : '';

            $this->assign('info',       $info);
            $this->meta_title = '编辑评论';
            $this->display();
        }
    }

    /* 回复留言 */
    public function reply($id = null, $pid = 0){
       
        if(IS_POST){ //提交表单
			 $reply = D('reply');
             if(false !==  $reply ->update()){
                $this->success('回复成功！', U('index'));
            } else {
                $error =  $reply ->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {
            $cate = '';
            if($pid){
                /* 获取上级留言信息 */
                $cate =  $reply ->info($pid, 'id,name,title,status');
                if(!($cate && 1 == $cate['status'])){
                    $this->error('指定的上级留言不存在或被禁用！');
                }
            }

            /* 获取留言信息 */
			 $Message = D('Message');
            $info = $id ? $Message->info($id) : '';
            $this->assign('info',$info);
           
            $this->meta_title = '回复留言';
            $this->display();
        }
    }



    /* 新增评论 */
    public function add(){
        $Comment = D('Comment');

        if(IS_POST){ //提交表单
            if(false !== $Comment->update()){
                $this->success('新增成功！', U('index'));
            } else {
                $error = $Comment->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {
            $this->assign('info', $info);
            $this->meta_title = '新增评论';
            $this->display('edit');
        }
    }

	/**
	 * 删除
	 * @author ew_xiaoxiao
	 */
	public function del(){
		if(IS_GET){
			$id=I('get.id');  
			$document   =   M('Comment');
			if( $document->where("id='$id'")->delete()){
				$this->success('删除成功');
			}else{  
				$this->error('删除失败');
			}
		}
		
		if(IS_POST){
			$ids = I('post.id');
			$document = M("Comment");
			if(is_array($ids)){
				foreach($ids as $id){
					$document->where("id='$id'")->delete();
				}
			}
			$this->success("删除成功！");
		}
	
	}

}
