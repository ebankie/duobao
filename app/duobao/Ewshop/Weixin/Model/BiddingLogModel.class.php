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
 * 拍卖记录数据模型
 * Class BiddingLogModel
 * @package Weixin\Model
 * @author
 */
class BiddingLogModel extends Model {

    /**
     * 获得出价最高的人；标记已完成
     * @param $goodsId  商品ID
     * @return false|int
     * @author
     */
    public function setUserGet($goodsId){
        $info = $this->query("SELECT l.id,l.uid,m.username FROM ewshop_bidding_log AS l LEFT JOIN ewshop_ucenter_member AS m ON m.id = l.uid WHERE l.goods_id = {$goodsId} ORDER BY l.price DESC,l.create_at DESC LIMIT 1");   //出价最高的日志id
        foreach ($info as $k => $v) {
            $info[$k]['mobile']    = '***'.substr($v['username'],7,4);
        }

        if ($info) {
            $bool = $this->execute("UPDATE ewshop_bidding_log SET is_get = 1 WHERE id = {$info[0]['id']}"); //标记为已获得
        }

        return $bool ? $info : FALSE;
    }


    /**
     * 获得已获得商品的用户信息
     * @param $goodsId 商品ID
     * @return bool|mixed
     * @author
     */
    public function getUser($goodsId){

        $sql=<<<SQL
        SELECT l.id,l.uid,m.username FROM ewshop_bidding_log AS l
LEFT JOIN ewshop_ucenter_member AS m ON m.id = l.uid
WHERE l.goods_id = $goodsId AND l.is_get = 1
ORDER BY l.price DESC,l.create_at DESC LIMIT 1
SQL;


        $info = $this->query($sql);   //出价最高的日志id
        foreach ($info as $k => $v) {
            $info[$k]['mobile']    = '***'.substr($v['username'],7,4);
        }
        return $info ? $info[0] : FALSE;
    }


    /**
     * 获得用户出价过的商品信息
     * @param $userId   用户ID
     * @return mixed
     * @author
     */
    public function getRecords($userId , $key){

        if (empty($key)) {
            $sql = <<<SQL
SELECT l.goods_id,d.title,d.start_at,d.now_price,p.path FROM ewshop_bidding_log AS l
LEFT JOIN ewshop_document AS d ON d.id = l.goods_id
LEFT JOIN ewshop_picture AS p ON p.id = d.cover_id
WHERE l.uid = $userId
GROUP BY l.goods_id
SQL;
        }else{
        $sql = <<<SQL
SELECT l.goods_id,d.title,d.start_at,d.now_price,p.path FROM ewshop_bidding_log AS l
LEFT JOIN ewshop_document AS d ON d.id = l.goods_id
LEFT JOIN ewshop_picture AS p ON p.id = d.cover_id
WHERE l.uid = $userId AND d.title LIKE '%$key%'
GROUP BY l.goods_id
SQL;
        }

        $recordList = $this->query($sql);
        return $recordList;
    }

}
