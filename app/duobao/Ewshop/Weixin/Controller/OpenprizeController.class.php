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
class OpenprizeController extends HomeController {

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
        //首页商品
        $map['category_id'] = 217;
        $map['is_index']    = 1;
        $data['list'] = M('Document')->where($map)->order('id asc')->select();

        $data['time_end'] = $this->get_time_on_clock(time());//倒计时时间

        //开奖号码
        $code_list = M('WinCode')->where("code <> '0'")->order('id desc')->limit('30')->select();
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




}