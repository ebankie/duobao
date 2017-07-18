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
 * 后台广告位控制器
  * @author
 */
class AdpositionController extends ControlController {

    /**
     * 广告位管理
     * author
     */
    public function index(){
        /* 查询条件初始化 */
        $map  = array();
        if(IS_GET){ 
			$name=trim(I('get.name'));
		    $map['name'] = array('like',"%{$name}%");
          //  $list = M("Adposition")->where($map)->field(true)->order('id desc')->select();
		  $list = $this->lists('Adposition', $map,'id desc');
		} else { 
		    $list = $this->lists('Adposition', $map,'id desc');
	 	}
        $this->assign('list', $list);
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        
        $this->meta_title = '广告位管理';
        $this->display();
    }

    /* 编辑广告位 */

    public function edit($id = null){
        $adp = D('adposition');
        if(IS_POST){ //提交表单
            if(false !== $adp->update()){
                $this->success('编辑成功！', U('index'));
            } else {
                $error = $adp->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {
            /* 获取广告位信息 */
            $info = $id ? $adp->info($id) : '';
            $this->assign('info',    $info);
            $this->meta_title = '编辑广告位';
            $this->display();
        }
    }

    /* 新增广告位 */
    public function add(){
        $adp = D('adposition');
        if(IS_POST){ //提交表单
            if(false !== $adp->update()){
                $this->success('新增成功！', U('index'));
            } else {
                $error = $ad->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {
            $this->meta_title = '新增广告位';
            $this->display();
        }
    }

	/* 删除广告位 */
	public function del(){
       if(IS_POST){
            $ids = I('post.id');
            $adp = M("Adposition");
			
            if(is_array($ids)){
				 foreach($ids as $id){
					 $adp->where("id='$id'")->delete();	
                 }
            }
           $this->success("删除成功！");
        }else{
            $id = I('get.id');
            $db = M("Adposition");
            $status = $db->where("id='$id'")->delete();
            if ($status){
                $this->success("删除成功！");
            }else{
                $this->error("删除失败！");
            }
        } 
    }

}