<?php
// +----------------------------------------------------------------------
// | 微信管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2017  All rights reserved.
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------

namespace Control\Controller;
use Control\Model\AuthGroupModel;

/**
 * 模型管理控制器
 * @author ew_xiaoxiao
 */
class ModelController extends ControlController {

    /**
     * 模型管理首页
     * @author ew_xiaoxiao
     */
    public function index(){
        $map = array('status'=>array('gt',-1));
		$map = array('extend'=>0);
        $plist = $this->lists('Model',$map);

		$list[] = array();
		//重构二级显示
		$i=0;
		foreach($plist as $id => $v) {
			$list[$i] = $v;
			$where = array('status'=>array('gt',-1));
			$where = array('extend'=>$v['id']);
			$clist = $this->lists('Model',$where);	
					
			$i++;
			foreach($clist as $submenu_id => $subv) {
				$subv['strs'] = "&nbsp;&nbsp;&nbsp;&nbsp;|__&nbsp;&nbsp;";
				$subv['title'] = "&nbsp;&nbsp;&nbsp;&nbsp;|__&nbsp;&nbsp;".$subv['title'];
				$list[$i] = $subv;
				$i++;
			}
		}			
		//print_r($list);
        int_to_string($list);
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);

        $this->assign('_list', $list);
        $this->meta_title = '模型管理';
        $this->display();
    }

    /**
     * 新增页面初始化
     * @author ew_xiaoxiao
     */
    public function add(){
		$map['extend'] = array('eq',0);
		$ex_list = M("Model")->where($map)->field(true)->order('id desc')->select();
		$this->assign('ex_list', $ex_list);
        //获取所有的模型
        $models = M('Model')->where(array('extend'=>0))->field('id,title')->select();

        $this->assign('models', $models);
        $this->meta_title = '新增模型';
        $this->display();
    }

    /**
     * 编辑页面初始化
     * @author ew_xiaoxiao
     */
    public function edit(){
        $id = I('get.id','');
        if(empty($id)){
            $this->error('参数不能为空！');
        }
		$map['extend'] = array('eq',0);
		$ex_list = M("Model")->where($map)->field(true)->order('id desc')->select();
		$this->assign('ex_list', $ex_list);

        /*获取一条记录的详细数据*/
        $Model = M('Model');
        $data = $Model->field(true)->find($id);
        if(!$data){
            $this->error($Model->getError());
        }

        $fields = M('Attribute')->where(array('model_id'=>$data['id']))->field('id,name,title,is_show')->select();
        //是否继承了其他模型
        if($data['extend'] != 0){
            $extend_fields = M('Attribute')->where(array('model_id'=>$data['extend']))->field('id,name,title,is_show')->select();
            $fields = array_merge($fields, $extend_fields);
        }

        /* 获取模型排序字段 */
        $field_sort = json_decode($data['field_sort'], true);
        if(!empty($field_sort)){
            /* 对字段数组重新整理 */
            $fields_f = array();
            foreach($fields as $v){
                $fields_f[$v['id']] = $v;
            }
            $fields = array();
            foreach($field_sort as $key => $groups){
                foreach($groups as $group){
                    $fields[$fields_f[$group]['id']] = array(
                            'id' => $fields_f[$group]['id'],
                            'name' => $fields_f[$group]['name'],
                            'title' => $fields_f[$group]['title'],
                            'is_show' => $fields_f[$group]['is_show'],
                            'group' => $key
                    );
                }
            }
            /* 对新增字段进行处理 */
            $new_fields = array_diff_key($fields_f,$fields);
            foreach ($new_fields as $value){
                if($value['is_show'] == 1){
                    array_unshift($fields, $value);
                }
            }
        }

        $this->assign('fields', $fields);
        $this->assign('info', $data);
        $this->meta_title = '编辑模型';
        $this->display();
    }

    /**
     * 删除一条数据
     * @author ew_xiaoxiao
     */
  
    public function del(){
       if(IS_POST){
            $ids = I('post.id');
            $db = D("Model");
			
            if(is_array($ids)){
                foreach($ids as $id){
                     $db->del($id);
                }
            }
            $this->success("删除模型成功！");
        }else{
			
            $id = I('get.ids');
            $db = D("Model");
			
            $status = $db->del($id);
            if ($status){
                $this->success("删除模型成功！");
            }else{
                $this->error("删除模型失败！");
            }
        } 
    }


    /**
     * 更新一条数据
     * @author ew_xiaoxiao
     */
    public function update(){
        $res = D('Model')->update();

        if(!$res){
            $this->error(D('Model')->getError());
        }else{
            $this->success($res['id']?'更新成功':'新增成功', Cookie('__forward__'));
        }
    }

    /**
     * 生成一个模型
     * @author ew_xiaoxiao
     */
    public function generate(){
        if(!IS_POST){
            //获取所有的数据表
            $tables = D('Model')->getTables();

            $this->assign('tables', $tables);
            $this->meta_title = '生成模型';
            $this->display();
        }else{
            $table = I('post.table');
            empty($table) && $this->error('请选择要生成的数据表！');
            $res = D('Model')->generate($table,I('post.name'),I('post.title'));
            if($res){
                $this->success('生成模型成功！', U('index'));
            }else{
                $this->error(D('Model')->getError());
            }
        }
    }
}
