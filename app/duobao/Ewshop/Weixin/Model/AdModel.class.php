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
 * 广告数据模型
 * Class AdModel
 * @package Weixin\Model
 * @author
 */
class AdModel extends Model {

    /**
     * 获得广告数据
     * @param $place    广告位ID
     * @return mixed    广告数据
     * @author
     */
    public function getAds($place){
        $sql = <<<SQL
SELECT a.id,a.title,a.url,p.path FROM ewshop_ad AS a
LEFT JOIN ewshop_picture AS p ON a.icon = p.id
WHERE a.status = 1 AND place = $place
ORDER BY a.ord ASC
SQL;

        $indexImgs = $this->query($sql);
        return $indexImgs;
    }


}
