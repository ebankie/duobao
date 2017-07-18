<?php
// +----------------------------------------------------------------------
// | 微信管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2017  All rights reserved.
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------

namespace Control\Controller;
use User\Api\UserApi as UserApi;

/**
 * 后台首页控制器
 * @author ew_xiaoxiao
 */
class IndexController extends ControlController {
    /**
     * 后台首页
     * @author ew_xiaoxiao
     */
    public function index(){
   $damain=$_SERVER['SERVER_NAME'];
        $this->assign('data',$damain); 
	    $url="http://".$damain.__ROOT__;
        M("config")->where("name='DOMAIN'")->setField('value',$url);
  
	 $this->meta_title = '管理首页';
      
		 $this->display();
    }

   public function insert(){
	if($_POST['code']){
	  $code=$_POST['code'];
     M("config")->where('id=75')->setField('SCODE',$code);
    $ycode=M("config")->where('id=75')->getField('code');
    if($ycode){
	 $data['ycode'] = $ycode;
     $this->ajaxReturn($data); 
	  }
	  else
		  {$ycode=M("config")->where('id=75')->getField('code');

	  $data['ycode'] = $ycode;
      $this->ajaxReturn($data);
	  
	  }

	}
	else



    $this->display();
	}

}
