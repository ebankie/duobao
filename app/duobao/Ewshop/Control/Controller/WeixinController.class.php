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
 * 后台配置控制器
 * @author ew_xiaoxiao
 */
class WeixinController extends ControlController {

	/**
	 * 配置管理
	 * @author ew_xiaoxiao
	 */
	public function index(){
		if(IS_POST){
			$Config = D('Wxsetting');
			$data = $Config->create();
			if($data){
				if($Config->save()){
					S('DB_CONFIG_DATA',null);
					//记录行为
					action_log('update_setting','weixin',$data['id'],UID);
					$this->success('更新成功');
				} else {
					$this->error('更新失败');
				}
			} else {
				$this->error($Config->getError());
			}
		} else {
			$id     =   '1';
			$info = array();
			/* 获取数据 */
			$settinginfo = M('Wxsetting')->where(array('id'=>$id))->select();

			if($settinginfo){
				$info = $settinginfo[0];
			}
			$info['url'] = 'http://' . $_SERVER ['HTTP_HOST'] . "/Control/Wechat/index";
			$this->assign('info', $info);
			$this->meta_title = '微信配置';
			$this->display();
		}
	}

	/**
	 * 菜单管理
	 * author
	 */
	public function menu(){
		/* 查询条件初始化 */
		$list[] = array();
		$menulist   =   M("Wxmenu")->where(array('pid'=>0))->order('listorder desc')->select();
		//重构维新菜单二级显示
		$i=0;
		foreach($menulist as $id => $v) {
			$list[$i] = $v;
			$submenulist = M("Wxmenu")->where(array('pid'=>$v['id']))->order('listorder desc')->select();
			$i++;
			foreach($submenulist as $submenu_id => $subv) {
				$subv['name'] = "&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;".$subv['name'];
				$list[$i] = $subv;
				$i++;
			}
		}
		//重构维新菜单二级显示
		$this->assign('list', $list);
		// 记录当前列表页的cookie
		Cookie('__forward__',$_SERVER['REQUEST_URI']);

		$this->meta_title = '微信菜单管理';
		$this->display();
	}

	/* 编辑微信菜单 */
	public function menuedit($id = null){
		$menu = D('Wxmenu');
		if(IS_POST){ //提交表单
			$id = $_POST['id'];
			$menuinfo = M("Wxmenu")->where(array('id'=>$id))->find();
			$pid = $_POST['pid'];
			if($pid){
				$menulists2 = M("Wxmenu")->where(array('pid'=>$pid))->select();
				$menulists2_mums = count($menulists2);//二级菜单数量
				if($menuinfo['pid']!=$pid){
					if($menulists2_mums>=5){
						$this->error("同一个一级菜单下最多只能添加五个二级菜单，当前已达设置上限！");
					}
				}
			}else{
				$menulists = M("Wxmenu")->where(array('pid'=>0))->select();
				$menulists_mums = count($menulists);//一级菜单数量	

				if($menuinfo['pid']!=0){
					if($menulists_mums>=3){
						$this->error("最多只能添加三个一级菜单，当前已达设置上限！");
					}
				}

			}
			if(false !== $menu->update()){
				$this->success('修改成功！', U('menu'));
			} else {
				$error = $menu->getError();
				$this->error(empty($error) ? '未知错误！' : $error);
			}
		} else {
			/* 获取菜单信息 */
			$info = $id ? $menu->info($id) : '';
			$this->assign('info',       $info);

			$list   =   M("Wxmenu")->where(array('pid'=>0))->order('listorder desc')->select();
			$this->assign('list', $list);
			$this->meta_title = '编辑菜单';
			$this->display();
		}
	}


	/* 新增微信菜单 */
	public function menuadd(){
		$menu = D('Wxmenu');
		if(IS_POST){ //提交表单
			$pid = $_POST['pid'];
			if($pid){
				$menulists2 = M("Wxmenu")->where(array('pid'=>$pid))->select();
				$menulists2_mums = count($menulists2);//二级菜单数量	
				if($menulists2_mums>=5){
					$this->error("同一个一级菜单下最多只能添加五个二级菜单，当前已达设置上限！");
				}else{
					if(false !== $menu->update()){
						$this->success('新增成功！', U('menu'));
					} else {
						$error = $menu->getError();
						$this->error(empty($error) ? '未知错误！' : $error);
					}
				}
			}else{
				$menulists = M("Wxmenu")->where(array('pid'=>0))->select();
				$menulists_mums = count($menulists);//一级菜单数量	
				if($menulists_mums>=3){
					$this->error("最多只能添加三个一级菜单，当前已达设置上限！");
				}else{
					if(false !== $menu->update()){
						$this->success('新增成功！', U('menu'));
					} else {
						$error = $menu->getError();
						$this->error(empty($error) ? '未知错误！' : $error);
					}
				}
			}
		} else {
			$this->assign('info',       null);
			$list   =   M("Wxmenu")->where(array('pid'=>0))->order('listorder desc')->select();
			$this->assign('list', $list);
			$this->meta_title = '新增菜单';
			$this->display('menuedit');
		}
	}

	/* 删除微信菜单 */
	public function menudel(){
		if(IS_POST){
			$ids = I('post.id');
			$menu = M("Wxmenu");
			if(is_array($ids)){
				foreach($ids as $id){
					$menu->where("id='$id'")->delete();
				}
			}
			$this->success("删除成功！");
		}else{
			$id = I('get.id');
			$menu = M("Wxmenu");
			$status = $menu->where("id='$id'")->delete();
			if ($status){
				$this->success("删除成功！");
			}else{
				$this->error("删除失败！");
			}
		}
	}

	//生成微信菜单
	public function createMenu() {
		$wxinfo = M ("Wxsetting")->where (array("id" =>"1"))->find ();

		$menu[] = array();
		$menulist   =   M("Wxmenu")->where(array('pid'=>0))->order('listorder desc')->select();
		$i=0;
		foreach($menulist as $id => $v) {
			$submenulist = M("Wxmenu")->where(array('pid'=>$v['id']))->order('listorder desc')->select();
			if($submenulist){//有子菜单
				$menu [$i]['name'] = $v["name"];
				$j=0;
				foreach($submenulist as $submenu_id => $subv) {
					$menu [$i]['sub_button'][$j]["type"] = $subv["type"];
					$menu [$i]['sub_button'][$j]["name"] = $subv["name"];
					if ($subv ["type"] == "view") {
						if(strstr($subv["view_url"],$wxinfo['authurl'])){
							$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$wxinfo['appid']."&redirect_uri=".$subv["view_url"]."&response_type=code&scope=snsapi_base&state=1#wechat_redirect";
						}else{
							$url = $subv["view_url"];
						}
						$menu [$i]['sub_button'][$j]["url"] = $url;
					} else {
						$menu [$i]['sub_button'][$j]["key"] = $subv["event_key"];
					}
					$j++;
				}
			}else{//无子菜单
				$menu [$i] ["type"] = $v["type"];
				$menu [$i] ["name"] = $v["name"];
				if ($v ["type"] == "view") {
					if(strstr($v["view_url"],$wxinfo['authurl'])){
						$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$wxinfo['appid']."&redirect_uri=".$v["view_url"]."&response_type=code&scope=snsapi_base&state=1#wechat_redirect";
					}else{
						$url = $v["view_url"];
					}
					$menu [$i] ["url"] = $url;
				} else {
					$menu [$i] ["key"] = $v["event_key"];
				}
			}
			$i++;
		}
//		dump($menu);die;
		$newmenu ["button"] = $menu;
		$weObj = R('Wechat/init');
		$result = $weObj->createMenu ($newmenu);
		if($result){
			$this->success ( "重新创建菜单成功!" );
		}else{
			$this->error ( "重新创建菜单失败!" );
		}

	}

	/**
	 * 关注回复规则管理
	 * author
	 */
	public function guanzhu(){
		if(IS_POST){ //提交表单
			$info  =  $_POST['info'];
			$return = M ("Wxkey")->save($info);
			if($return!== flase){
				if($info['type']=='1'){//图文
					$kid = $info['id'];
					$this->success ( "保存成功!" , "/Control/Weixin/pictextreply/kid/".$kid);
				}else{
					$this->success ( "保存成功!" );
				}
			}else{
				$this->error ( "保存失败!" );
			}
		} else {
			/* 关注回复规则 */
			$keyword = M("Wxkey")->where (array ("keyword" => "subscribe"))->find ();
			if(empty($keyword)){
				$id = M ("Wxkey")->add(array('name'=>"关注回复",'keyword'=>"subscribe",'type'=>'1'));
				$keyword = M("Wxkey")->where (array ("id" => $id))->find ();
			}

			$this->assign('info', $keyword);

			$this->meta_title = '关注回复管理';
			$this->display();
		}
	}

	/**
	 * 图文回复管理
	 * author
	 */
	public function pictextreply(){
		$kid  =  $_GET['kid'];
		if($kid){
			$list = M("Wxreply")->where(array('kid'=>$kid))->order('listorder desc')->select();
			$this->assign('kid', $kid);
			$this->assign('list', $list);
		}
		$this->meta_title = '图文回复列表';
		$this->display();

	}
	/* 新增编辑图文回复 */
	public function pictextreplyedit(){
		if(IS_POST){ //提交表单
			$kid  =  $_POST['kid'];
			$reply = D('Wxreply');
			if(false !== $reply->update()){
				$this->success('保存成功！', "/Control/Weixin/pictextreply/kid/".$kid);
			} else {
				$error = $reply->getError();
				$this->error(empty($error) ? '未知错误！' : $error);
			}
		}else{
			$kid  =  $_GET['kid'];
			$id  =  $_GET['id'];
			if($id){//编辑图文回复
				$info = M("Wxreply")->where (array ("id" => $id))->find ();
				$this->meta_title = '编辑图文';
			}else{//新增
				$this->meta_title = '新增图文';
			}
			$this->assign('kid', $kid);
			$this->assign('info', $info);
			$this->display();
		}
	}

	/* 删除微信图文回复 */
	public function pictextreplydel(){
		if(IS_POST){
			$ids = I('post.id');
			$wxreply = M("Wxreply");
			if(is_array($ids)){
				foreach($ids as $id){
					$wxreply->where("id='$id'")->delete();
				}
			}
			$this->success("删除成功！");
		}else{
			$id = I('get.id');
			$wxreply = M("Wxreply");
			$status = $wxreply->where("id='$id'")->delete();
			if ($status){
				$this->success("删除成功！");
			}else{
				$this->error("删除失败！");
			}
		}
	}

	/**
	 * 图文回复管理
	 * author
	 */
	public function keywords(){
		$name=trim(I('get.name'));
		if($name){
			$map['name'] = array('like',"%{$name}%");
		}
		$map['keyword'] = array('neq',"subscribe");//过滤关注回复
		$list =  M("Wxkey")->where($map)->field(true)->order('datetime desc')->select();

		$this->assign('list', $list);

		$this->meta_title = '自定义关键词列表';
		$this->display();
	}

	/**
	 * 关键词回复规则管理
	 * author
	 */
	public function keywordsedit(){
		if(IS_POST){ //提交表单
			$info  =  $_POST['info'];
			if($info['id']){//编辑保存
				$return = M ("Wxkey")->save($info);
			}else{//新增保存
				$return = M ("Wxkey")->add($info);
			}
			if($return!== flase){
				if($info['type']=='1'){//图文
					$kid = $info['id'];
					if(empty($kid)){
						$kid = $return;
					}
					$this->success ( "保存成功!" , "/Control/Weixin/pictextreply/kid/".$kid);
				}else{
					$this->success ( "保存成功!","/Control/Weixin/keywords");
				}
			}else{
				$this->error ( "保存失败!" );
			}
		} else {
			$id  = $_GET['id'];
			if($id){//编辑关键词
				$keyword = M("Wxkey")->where (array ("id" => $id))->find ();
				$this->meta_title = '编辑关键词';
			}else{//添加关键词
				$this->meta_title = '添加关键词';
			}
			$this->assign('info', $keyword);

			$this->display();
		}
	}
	/* 删除关键词回复规则 */
	public function keywordsdel(){
		$wxreply = M("Wxreply");
		$wxkey = M("Wxkey");
		if(IS_POST){
			$ids = I('post.id');
			if(is_array($ids)){
				foreach($ids as $id){
					$wxkey->where("id='$id'")->delete();
					$wxreply->where("kid='$id'")->delete();//删除关键词规则同时删除关键词回复内容
				}
			}
			$this->success("删除成功！");
		}else{
			$id = I('get.id');
			$status = $wxkey->where("id='$id'")->delete();
			if ($status){
				$wxreply->where("kid='$id'")->delete();//删除关键词规则同时删除关键词回复内容
				$this->success("删除成功！");
			}else{
				$this->error("删除失败！");
			}
		}
	}

}