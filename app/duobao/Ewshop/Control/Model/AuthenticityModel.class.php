<?php
// +----------------------------------------------------------------------
// | 微信管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2017  All rights reserved.
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------

namespace Control\Model;
use Think\Model;

/**
 * 优惠券模型
 * @author ew_xiaochuan
 */
class AuthenticityModel extends Model{

    protected $_validate = array(
        array('sn_code', 'require', '产品SN号不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
    );

  protected $_auto = array(
       
       
    );

    /**
     * 获取商品类型详细信息
     * @param  milit   $id 优惠券ID或标识
     * @param  boolean $field 查询字段
     * @return array     商品类型详细信息
     * @author ew_xiaochuan
     */
    public function info($id, $field = true){
        $map = array();
		$map['id'] = $id;
        return $this->field($field)->where($map)->find();
    }


    /**
     * 更新商品类型详细信息
     * @return boolean 更新状态
     * @author ew_xiaochuan
     */
    public function update(){
        $data = $this->create();
        if(!$data){ //数据对象创建错误
            return false;
        }

        /* 添加或更新数据 */
        if(empty($data['id'])){
            $res = $this->add();
        }else{
            $res = $this->save();
        }

        //记录行为
        action_log('update_Authenticity', 'Authenticity', $data['id'] ? $data['id'] : $res, UID);

        return $res;
    }

    
}
