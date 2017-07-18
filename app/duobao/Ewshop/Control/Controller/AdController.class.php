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
 * 后台订单控制器
  * @author
 */
class AdController extends ControlController {

    /**
     * 订单管理
     * author
     */
    public function index(){
        /* 查询条件初始化 */
	
       $map  = array('status' => 1);
       if(IS_GET){ 
	   	 $title=trim(I('get.title'));
		 if($title){
			 $map['title'] = array('like',"%{$title}%");
		 }
		 $place=trim(I('get.adp'));
		 if($place){
		 	$map['place'] = $place;
		 }
         //$list  =  M("Ad")->where($map)->field(true)->order('px ASC ,id desc')->select();
		 $list = $this->lists('Ad', $map,'px ASC ,id desc');
	    }else{ 
		 $list = $this->lists('Ad', $map,'px ASC ,id desc');
	    }
		$adp = D('Adposition');
		foreach($list as $k=>$value){
		 	$info = $adp->info($value['place']);
			if($info){
				$list[$k]['place'] = $info['name'];
			}
		}	   
        $this->assign('list', $list);
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        
        $this->meta_title = '首页分类推广管理';
        $this->display();
    }

    /* 编辑分类 */
    public function edit($id = null, $pid = 0){
        $adp = D('Adposition');
		$list = $adp->select();
        $this->assign('list',$list);//广告位列表
		$ad = D('Ad');
        if(IS_POST){ //提交表单
            if(false !== $ad->update()){
                $this->success('编辑成功！', U('index'));
            } else {
                $error = $ad->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {
            $info = $id ? $ad->info($id) : '';
            $this->assign('info',       $info);
            $this->meta_title = '编辑广告';
            $this->display();
        }
    }

    /* 新增分类 */
    public function add(){
        $adp = D('Adposition');
		$list = $adp->select();
        $this->assign('list',$list);//广告位列表
		$ad = D('Ad');
        if(IS_POST){ //提交表单
            if(false !== $ad->update()){
                $this->success('新增成功！', U('index'));
            } else {
                $error = $ad->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {
            $this->meta_title = '新增广告';
            $this->display();
        }
    }


 public function del(){
       if(IS_POST){
             $ids = I('post.id');
            $order = M("ad");
			
            if(is_array($ids)){
                             foreach($ids as $id){
		
                             $order->where("id='$id'")->delete();
						
                }
            }
           $this->success("删除成功！");
        }else{
            $id = I('get.id');
            $db = M("ad");
            $status = $db->where("id='$id'")->delete();
            if ($status){
                $this->success("删除成功！");
            }else{
                $this->error("删除失败！");
            }
        } 
    }


}