<?php
// +----------------------------------------------------------------------
// | 微信管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2017  All rights reserved.
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------

namespace Weixin\Controller;

use OT\DataDictionary;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 * $url= $_SERVER[HTTP_HOST]; //获取当前域名
 */
class IndexController extends HomeController {

    /**
     * 构造方法
     * IndexController constructor.
     * @author
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * 首页
     * @author
     */
    public function index(){
        $ad1 = D('Ad')->getAds(28);
        $data['indexImgs']  = $ad1;  //获得顶部信息

        $ad2   = D('Ad')->getAds(29);  //获得中部广告
        $data['indexGoods'] = $ad2[0];

        //首页商品
        $data['list_50'] = M('Document')->where("category_id = 217 and price = 1 and status = 1")->order('id asc')->select();//50元卡
//        foreach($data['list_50'] as $key => $val){
//            //授权url
//            $detail_url = 'http://'.$_SERVER['HTTP_HOST'].'/Weixin/Goods/detail/id/'.$val['id'];
//            $data['list_50'][$key]['url'] = R('Qfpay/getGoodsDetailUrl' , array ($detail_url));
//        }
        $data['list_100'] = M('Document')->where("category_id = 217 and price = 2 and status = 1")->order('id asc')->select();//100元卡
//        foreach($data['list_100'] as $key => $val){
//            //授权url
//            $detail_url = 'http://'.$_SERVER['HTTP_HOST'].'/Weixin/Goods/detail/id/'.$val['id'];
//            $data['list_100'][$key]['url'] = R('Qfpay/getGoodsDetailUrl' , array ($detail_url));
//        }

        $data['time_end'] = $this->get_time_on_clock(time());//倒计时时间

        //最近中奖(中奖记录)
        $pk_list = M('WinExchange')->order('buy_time DESC')->limit(10)->select();
        foreach($pk_list as $key => $val){
            $pk_list[$key]['goods_title'] = M('Document')->where("id = {$val['goods_id']}")->getField('title');
            if($val['is_virtual'] == 1){
                $pk_list[$key]['userinfo'] = M('MemberTemp')->field('headimgurl,nickname')->where("id = {$val['uid']}")->find();//虚拟用户
            }else{
                $pk_list[$key]['userinfo'] = M('Member')->field('headimgurl,nickname')->where("uid = {$val['uid']}")->find();
            }
        }
        $data['pk_list'] = $pk_list;

        //开奖号码
        $code_list = M('WinCode')->where("code <> '0'")->order('id desc')->limit('10')->select();
        foreach($code_list as $key => $val){
            $code_list[$key]['code'] = chunk_split($val['code'],1,' ');
            $code_list[$key]['create_time'] = explode(' ',$val['create_time']);
            $code_list[$key]['code_56_type'] = ($val['code_56_type'] == 1) ? '小' : '大';
            $code_list[$key]['code_110_type'] = ($val['code_110_type'] == 1) ? '小' : '大';

        }
//        dump($code_list);

        $data['code_list'] = $code_list;
        $this->assign('data' , $data);
        $this->meta_title = '首页';
        $this->display();
    }

    /**
     * 新手介绍
     * @author
     */
    public function introduce(){
        $this->meta_title = '玩法规则';
        $this->display();
    }

    /**
     * 游戏算法规则
     * @author
     */
    public function gameIntroduce(){
        $this->meta_title = '玩法规则';
        $this->display();
    }

    /**
     * 彩票种类列表
     * @author
     */
    public function test(){

		$a = array('ss'=>'11');
		$b = array('ww'=>'22');
		$data = array();
		$data = $a;
		$data = $b;
		print_r($data );
    }
}