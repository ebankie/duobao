<?php
// +----------------------------------------------------------------------
// | 微信管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2017  All rights reserved.
// +----------------------------------------------------------------------
// | Author:
// +------------------------------------------------------------------------

namespace Control\Model;
use Think\Model;

/**
 * 优惠券模型
 * @author ew_xiaoxiao
 */
class EmailModel extends Model{

    protected $_validate = array(

        array('content', 'require', '内容不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
    
    	
    	
    );

 protected $_auto = array(
        array('status', '1', self::MODEL_INSERT),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
       
    );

    /**
     * 获取优惠券详细信息
     * @param  milit   $id ID或标识
     * @param  boolean $field 查询字段
     * @return array  
     * @author
     */
    public function info($id, $field = true){
        /* 获取信息 */
        $map = array();
        if(is_numeric($id)){ //通过ID查询
            $map['id'] = $id;
        } else { //通过标识查询
            $map['name'] = $id;
        }
        return $this->field($field)->where($map)->find();
    }

    /**
     * 获取优惠券树，指定优惠券则返回指定优惠券极其子优惠券，不指定则返回所有优惠券树
     * @param  integer $id    优惠券ID
     * @param  boolean $field 查询字段
     * @return array          优惠券树
     * @author ew_xiaoxiao
     */


    /**
     * 更新优惠券信息
     * @return boolean 更新状态
     * @author ew_xiaoxiao
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

        //更新优惠券缓存
        S('sys_brand_list', null);

        //记录行为
        action_log('update_Brand', 'brand', $data['id'] ? $data['id'] : $res, UID);

        return $res;
    }

    
}
