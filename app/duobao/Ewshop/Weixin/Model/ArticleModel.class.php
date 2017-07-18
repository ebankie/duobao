<?php
// +----------------------------------------------------------------------
// | 微信管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2017  All rights reserved.
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------

namespace Weixin\Model;
use Think\Model;


/**
 * 文章模型
 * Class ArticleModel
 * @package Weixin\Model
 * @author
 */
class ArticleModel extends Model {

    /**
     * 获得指定分类下的所有文章
     * @param $categoryId   分类ID
     * @return mixed 返回文章数组
     * @author
     */
    public function getCategoryArticle($categoryId){
        $sql         = <<<SQL
SELECT a.id,a.title,a.content,p.path FROM ewshop_article AS a
LEFT JOIN ewshop_picture AS p ON p.id = a.cover_id
WHERE a.category_id = $categoryId AND a.status = 1
SQL;
        $articleList = $this->query($sql);
        return $articleList;

    }


    /**
     * 获得新闻详情
     * @param $newsId   新闻ID
     * @return mixed
     * @author
     */
    public function getDetail($newsId){
        $newsDetail = $this->query("SELECT id,title,create_time,content FROM ewshop_article WHERE id = $newsId AND STATUS = 1");
        return $newsDetail[0];
    }


    /**
     * 获得全部新闻资讯
     * @return mixed
     * @author
     */
    public function getAll(){
        $sql         = <<<SQL
SELECT a.id,a.title,a.create_time,a.shortnote,p.path FROM ewshop_article AS a
LEFT JOIN ewshop_picture AS p ON p.id = a.cover_id
WHERE a.category_id != 209 AND a.status = 1
SQL;
        $articleList = $this->query($sql);
        return $articleList;
    }

}
