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
use Think\Page;
/**
 * 后台用户控制器
 * @author ew_xiaoxiao
 */
class JoinController extends ControlController {

    /**
     * 加盟列表
     * @author
     */
    public function index(){
        $title = htmlspecialchars($_GET['title']);
        $status = htmlspecialchars($_GET['status']);

        if($title){ $map['u.nickname'] = array('like' , '%'.$title.'%') ; }
        if($status != ''){$map['j.status'] = $status ; }

        $map['j.is_delete'] = 0;
        $list = M('Join')
            ->field('j.*,u.nickname')
            ->alias('j')
            ->where($map)
            ->join("LEFT JOIN ewshop_member AS u ON j.uid = u.uid")
            ->select();
        $page = new Page(count($list),20);
        $arr = array();
        foreach($list as $key=>$val) {
            if($key >= $page->firstRow && $key < ($page->firstRow+20) ){
                $arr[] = $val;
            }
        }
        $list = $arr;
        $show = $page->show();
        $this->assign('_page',$show);

        $this->assign('list' , $list);
        $this->meta_title = '加盟列表';
        $this->display();

    }

    public function edit(){
        if(IS_POST){
            $res = M('Join')->where(array('id'=>$_POST['id']))->save($_POST);
            if ($res) {
                $this->success("编辑成功！");
            } else {
                $this->error("编辑失败！");
            }
        }else{
            $id = I('get.id');
            $data = M('Join')->where(array('id'=>$id))->find();
            //$gids = M('Join')->field('gid')->select();
            //$gids = array_column($gids, 'gid');
            //foreach ($gids as $key=>$value) {
            //    if ($value === $data['gid']){unset($gids[$key]);}
            //}
            //$map['uid'] = array('not in',$gids);
            //$map['groupid'] = 7;
            //$glist = M('Admin')->where($map)->select();
            $this->assign('data' , $data);
            //$this->assign('glist' , $glist);
            $this->meta_title = '审核加盟信息';
            $this->display();
        }
    }

    /**
     * 删除
     * @author
     */
    public function del(){
        if (IS_POST) {
            $ids   = I('post.id');
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    M('Join')->where(array('id'=>$id))->save(array('is_delete'=>1));
                }
            }
            $this->success("删除成功！");
        } else {
            $id     = I('get.id');
            $status = M('Join')->where(array('id'=>$id))->save(array('is_delete'=>1));
            if ($status) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }

}