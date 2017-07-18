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


class LikeModel extends Model {

    /**
     * 获得用户收藏的商品列表
     * @param $userId 用户ID
     * @return mixed
     * @author
     */
    public function getLike($userId){
        $sql      = <<<SQL
SELECT l.*,d.title,d.now_price,d.start_at,p.path FROM ewshop_like AS l
LEFT JOIN ewshop_document AS d ON d.id = l.goods_id
LEFT JOIN ewshop_picture AS p ON p.id = d.cover_id
WHERE l.user_id = $userId
ORDER BY l.create_at DESC
SQL;
        $likeList = $this->query($sql);
        return $likeList;
    }


    /**
     * 执行收藏操作
     * @param $uid 用户ID
     * @param $goodsId 商品ID
     * @return int 0收藏失败 2已经收藏过 1 收藏成功
     * @author
     */
    public function doLike($uid , $goodsId){
        $count = $this->query("SELECT count(*) AS count FROM ewshop_like WHERE user_id = $uid AND goods_id = $goodsId");
        $count=$count[0]['count'];
        if ($count >= 1) {  //已经收藏过
            return 2;
        } else {  //执行收藏操作（入库）
            $time = time();
            $bool = $this->execute("INSERT INTO ewshop_like VALUES(NULL,$goodsId,$uid,$time)");
            return $bool ? 1 : 0;
        }
    }

}
