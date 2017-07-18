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
 * 拍品数据模型
 * Class DocumentModel
 * @package Weixin\Model
 * @author
 */
class DocumentModel extends Model {




    /**
     * 获得全部商品信息，分为没过期，和已过期的商品
     * @param key 需要搜索的标题
     * @return mixed
     * @author
     */
    public function getAll(){


    }


    /**
     * 获得商品详情
     * @param $goodsId
     * @return mixed
     * @author
     */
    public function getDetail($goodsId){
        $sql = <<<SQL
SELECT d.id,d.title,d.price,p.path AS cover_img,pro.pics FROM ewshop_document AS d
LEFT JOIN ewshop_document_product AS pro ON pro.id = d.id
LEFT JOIN ewshop_picture AS p ON p.id = d.cover_id
WHERE d.id = $goodsId
SQL;


        $detail = $this->query($sql);
        if ($detail[0]['pics']) {
            $sql               = "SELECT path FROM ewshop_picture WHERE id IN({$detail[0]['pics']})";
            $pics              = $this->query($sql);
            $detail[0]['pics'] = $pics;
        }

        return $detail[0];

    }


    /**
     * 获得商品的出价记录
     * @param $goodsId 商品ID
     * @param $changeTime 是否转化时间
     * @return mixed
     * @author
     */
    public function getBiddingLog($goodsId , $changeTime = FALSE){
        $sql        = <<<SQL
SELECT l.*,m.username FROM ewshop_bidding_log AS l
LEFT JOIN ewshop_ucenter_member AS m ON m.id = l.uid
WHERE l.goods_id = $goodsId
ORDER BY price DESC ,create_at DESC
SQL;
        $biddingLog = $this->query($sql);

        if ($changeTime == FALSE) {
            foreach ($biddingLog as $k => $v) {
                $biddingLog[$k]['mobile']    = '***'.substr($v['username'],7,4);
            }
            return $biddingLog;
        } else {  //转化换时间
            foreach ($biddingLog as $k => $v) {
                $biddingLog[$k]['create_at'] = date('m.d H:i:s' , $v['create_at']);
                $biddingLog[$k]['mobile']    = '***'.substr($v['username'],7,4);
            }
            return $biddingLog;
        }


    }




    /**
     * 判断商品拍卖时间是否过期
     * @param $goodsId 商品ID
     * @return bool 过期TRUE，没过期FALSE
     * @author
     */
    public function isEnd($goodsId){
        $now_time = time();
        $now_time = $now_time + 3;
        $end_at   = $this->query("SELECT end_at FROM ewshop_document WHERE id = {$goodsId}");
        return $now_time >= $end_at[0]['end_at'] ? TRUE : FALSE;
    }


    /**
     * 获得用户已拍的的商品列表
     * @param $userId   用户ID
     * @return mixed
     * @author
     */
    public function getIsGet($userId , $key){

        if (empty($key)) {
            $sql = <<<SQL
SELECT l.goods_id,l.price,l.create_at,d.title,p.path FROM ewshop_bidding_log AS l
LEFT JOIN ewshop_document AS d ON d.id = l.goods_id
LEFT JOIN ewshop_picture AS p ON p.id = d.cover_id
WHERE l.uid = $userId AND l.is_get = 1
SQL;
        } else {
            $sql = <<<SQL
SELECT l.goods_id,l.price,l.create_at,d.title,p.path FROM ewshop_bidding_log AS l
LEFT JOIN ewshop_document AS d ON d.id = l.goods_id
LEFT JOIN ewshop_picture AS p ON p.id = d.cover_id
WHERE l.uid = $userId AND l.is_get = 1  AND d.title LIKE '%$key%'
SQL;
        }

        $getList = M()->query($sql);
        return $getList;
    }


}
