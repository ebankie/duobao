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
 * 微信菜单模型
 * @author ew_xiaoxiao
 */
class WxmenuModel extends Model{

    protected $_validate = array(    
        array('name', 'require', '名称不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),   
    	array('name', '', '名称已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
    );

  	protected $_auto = array(

       
    );

    /**
     * 获取菜单详细信息
     * @param  milit   $id 菜单ID或菜单名称
     * @param  boolean $field 查询字段
     * @return array     菜单信息
     * @author ew_xiaoxiao
     */
    public function info($id, $field = true){
        /* 获取菜单信息 */
        $map = array();
        if(is_numeric($id)){ //通过ID查询
            $map['id'] = $id;
        } else { //通过标识查询
            $map['name'] = $id;
        }
        return $this->field($field)->where($map)->find();
    }

    /**
     * 更新菜单信息
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

        //记录行为
        action_log('update_Wx_menu', 'Wx_menu', $data['id'] ? $data['id'] : $res, UID);

        return $res;
    }

    
}
