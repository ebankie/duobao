<?php
// +----------------------------------------------------------------------
// | 微信管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2017  All rights reserved.
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------
namespace Weixin\Controller;
use Workerman\Worker;
use OT\DataDictionary;

header("content-Type: text/html; charset=Utf-8");
class TestController extends HomeController {


    public function index(){
        echo  $data['time_end'] = $this->get_time_on_clock(time());

        $str  =  str_replace('/','-',$data['time_end']);



        //        var_dump(strtotime(sprintf('%d' ,$str)));
        var_dump(strtotime($str));
        var_dump(date('Y-m-d H:i:s',time()));


        die();

        $end = explode(' ',$data['time_end']);
        $unix = strtotime();
          echo date('Y-m-d',strtotime($end[0])).' '.$end[1].'<br/>';

        echo strtotime(date('Y-m-d H:i:s',time()));

        echo date('Y-m-d H:i:s',time());
        var_dump($unix);


    }
}