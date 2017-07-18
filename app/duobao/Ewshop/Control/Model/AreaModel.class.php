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
 * 地区模型
 * @author ew_xiaoxiao
 */
class AreaModel extends Model{

    protected $_validate = array(
 	 array('name', 'require', '名称不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
 	 //array('areacode', 'require', '地区代码不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
 	 //array('areacode','haveareacode','地区代码已经存在','1','callback'), 
    );

 	protected $_auto = array(
   
    );

    /**
     * 获取详细信息
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
        } else { //通过名称查询
            $map['name'] = $id;
        }
        return $this->field($field)->where($map)->find();
    }
    /**
     * 判断地区代码是否存在
     * @return boolean 更新状态
     * @author ew_xiaoxiao
     */
	
	public function haveareacode(){
      $areacode = $_POST['areacode'];
	  $areainfo = M('Area');
	  $id = $_POST['id'];
	  if($id){
		  $map['id'] = array('neq',$id);
		  $map['areacode'] = array('eq',$areacode);
		  $hid = $areainfo->where($map)->select();
	  }else{
		$hid = $areainfo->getFieldByAreacode($areacode,'id');  

	  }
	 if($hid){
			return false;	
		 }else{
			  return true;
 
	}  
	
    }
	
	
    /**
     * 更新信息
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
        action_log('update_Area', 'area', $data['id'] ? $data['id'] : $res, UID);

        return $res;
    }

    
}
