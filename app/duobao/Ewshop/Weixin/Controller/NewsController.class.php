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
 * 新闻类
 * Class NewsController
 * @package Weixin\Controller
 * @author
 */
class NewsController extends HomeController {

    /**
     * 活动说明
     * @author
     */
    public function explain(){
        $data['articleList'] = D('Article')->getCategoryArticle(209);   //获得活动说明下的所有文章
        $this->assign('data' , $data);
        $this->meta_title = '活动说明';
        $this->display();
    }


    /**
     * 新闻详情
     * @author
     */
    public function detail(){
        $id             = intval($_GET['id']);
        $data['detail'] = D('Article')->getDetail($id);
        $this->assign('data' , $data);
        $this->meta_title = '新闻详情';
        $this->display();
    }

    /**
     * 新闻列表
     * @author
     */
    public function newsList(){
        $data['articleList'] = D('Article')->getAll();  //获得全部新闻资讯
        $this->assign('data' , $data);
        $this->meta_title = '新闻列表';
        $this->display();
    }

}