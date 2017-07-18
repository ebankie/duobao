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
use Control\Model\AuthGroupModel;

/**
 * 文档基础模型
 */
class DocumentModel extends Model {

    /* 自动验证规则 */
    protected $_validate = array (
        array ('name' , '/^[a-zA-Z]\w{0,39}$/' , '编号不合法' , self::VALUE_VALIDATE , 'regex' , self::MODEL_BOTH) ,
        array ('name' , 'checkName' , '编号已经存在' , self::VALUE_VALIDATE , 'callback' , self::MODEL_BOTH) ,
        array ('title' , 'require' , '产品名称不能为空' , self::MUST_VALIDATE , 'regex' , self::MODEL_BOTH) ,
        array ('title' , '1,80' , '产品名称长度不能超过80个字符' , self::MUST_VALIDATE , 'length' , self::MODEL_BOTH) ,
        array ('title' , 'checkTitle' , '产品名称已经存在' , self::VALUE_VALIDATE , 'callback' , self::MODEL_BOTH) ,
        array ('level' , '/^[\d]+$/' , '优先级只能填正整数' , self::VALUE_VALIDATE , 'regex' , self::MODEL_BOTH) ,
        array ('description' , '1,140' , '简介长度不能超过140个字符' , self::VALUE_VALIDATE , 'length' , self::MODEL_BOTH) ,
        array ('category_id' , 'require' , '分类不能为空' , self::MUST_VALIDATE , 'regex' , self::MODEL_INSERT) ,
        array ('category_id' , 'require' , '分类不能为空' , self::EXISTS_VALIDATE , 'regex' , self::MODEL_UPDATE) ,
        array ('category_id' , 'check_category' , '该分类不允许发布内容' , self::EXISTS_VALIDATE , 'function' , self::MODEL_UPDATE) ,
        array ('model_id,pid,category_id' , 'check_category_model' , '该分类没有绑定当前模型' , self::MUST_VALIDATE , 'function' , self::MODEL_INSERT) ,
    );


    /* 自动完成规则 */
    protected $_auto = array (
        array ('uid' , 'is_login' , self::MODEL_INSERT , 'function') ,
        array ('title' , 'htmlspecialchars' , self::MODEL_BOTH , 'function') ,
        array ('description' , 'htmlspecialchars' , self::MODEL_BOTH , 'function') ,
        array ('root' , 'getRoot' , self::MODEL_BOTH , 'callback') ,
        array ('link_id' , 'getLink' , self::MODEL_BOTH , 'callback') ,
        array ('attach' , 0 , self::MODEL_INSERT) ,
        array ('view' , 0 , self::MODEL_INSERT) ,
        array ('comment' , 0 , self::MODEL_INSERT) ,
        array ('extend' , 0 , self::MODEL_INSERT) ,
        array ('create_time' , 'getCreateTime' , self::MODEL_BOTH , 'callback') ,
        array ('update_time' , NOW_TIME , self::MODEL_BOTH) ,
        array ('status' , 'getStatus' , self::MODEL_BOTH , 'callback') ,
        array ('now_price' , 'setNowPrice' , self::MODEL_INSERT , 'callback') ,
        array ('position' , 'getPosition' , self::MODEL_BOTH , 'callback') ,
        array ('deadline' , 'strtotime' , self::MODEL_BOTH , 'function') ,
    );


    public function setNowPrice(){
        $nowPrice = $_POST['start_price'];
        return $nowPrice;
    }

    /**
     * 获取详情页数据
     * @param  integer $id 文档ID
     * @return array       详细数据
     */
    public function detail($id){
        /* 获取基础数据 */
        $info = $this->field(TRUE)->find($id);
        if (!(is_array($info) || 1 !== $info['status'])) {
            $this->error = '文档被禁用或已删除！';
            return FALSE;
        }

        /* 获取模型数据 */
        $logic  = $this->logic($info['model_id']);
        $detail = $logic->detail($id); //获取指定ID的数据
        if (!$detail) {
            $this->error = $logic->getError();
            return FALSE;
        }
        $info = array_merge($info , $detail);

        return $info;
    }

    /**
     * 新增或更新一个文档
     * @param array $data 手动传入的数据
     * @return boolean fasle 失败 ， int  成功 返回完整的数据
     * @author ew_xiaoxiao
     */
    public function update($data = NULL){
        /* 检查文档类型是否符合要求 */
        $res = $this->checkDocumentType(I('type' , 2) , I('pid'));
        if (!$res['status']) {
            $this->error = $res['info'];
            return FALSE;
        }

        /* 获取数据对象 */
        $data = $this->create($data);
        if (empty($data)) {
            return FALSE;
        }

        /* 添加或新增基础内容 */
        if (empty($data['id'])) { //新增数据
            $id = $this->add(); //添加基础内容
            if (!$id) {
                $this->error = '新增基础内容出错！';
                return FALSE;
            }
            $data['back_id'] = $id;
        } else { //更新数据
            $status = $this->save(); //更新基础内容
            if (FALSE === $status) {
                $this->error = '更新基础内容出错！';
                return FALSE;
            }
            $data['back_id'] = $data['id'];
        }

        /* 添加或新增扩展内容 */
        $logic = $this->logic($data['model_id']);
        $logic->checkModelAttr($data['model_id']);
        if (!$logic->update($id)) {
            if (isset($id)) { //新增失败，删除基础数据
                $this->delete($id);
            }
            $this->error = $logic->getError();
            return FALSE;
        }

        hook('documentSaveComplete' , array ('model_id' => $data['model_id']));

        //行为记录
        if ($id) {
            action_log('add_document' , 'document' , $id , UID);
        }

        //内容添加或更新完成
        return $data;
    }

    /**
     * 获取数据状态
     * @return integer 数据状态
     */
    protected function getStatus(){
        $id   = I('post.id');
        $cate = I('post.category_id');
        if (empty($id)) {    //新增
            $status = 1;
        } else {                //更新
            $status = $this->getFieldById($id , 'status');
            //编辑草稿改变状态
            if ($status == 3) {
                $status = 1;
            }
        }
        return $status;
    }

    /**
     * 获取根节点id
     * @return integer 数据id
     * @author ew_xiaoxiao
     */
    protected function getRoot(){
        $pid = I('post.pid');
        if ($pid == 0) {
            return 0;
        }
        $p_root = $this->getFieldById($pid , 'root');
        return $p_root == 0 ? $pid : $p_root;
    }

    /**
     * 创建时间不写则取当前时间
     * @return int 时间戳
     * @author ew_xiaoxiao
     */
    protected function getCreateTime(){
        $create_time = I('post.create_time');
        return $create_time ? strtotime($create_time) : NOW_TIME;
    }

    /**
     * 获取扩展模型对象
     * @param  integer $model 模型编号
     * @return object         模型对象
     */
    private function logic($model){
        $name  = parse_name(get_document_model($model , 'name') , 1);
        $class = is_file(MODULE_PATH . 'Logic/' . $name . 'Logic' . EXT) ? $name : 'Product';
        $class = MODULE_NAME . '\\Logic\\' . $class . 'Logic';
        return new $class($name);
    }

    /**
     * 检查标识是否已存在(只需在同一根节点下不重复)
     * @param string $name
     * @return true无重复，false已存在
     * @author ew_xiaoxiao
     */
    protected function checkName(){
        $name        = I('post.name');
        $category_id = I('post.category_id' , 0);
        $id          = I('post.id' , 0);

        $map = array ('name' => $name , 'id' => array ('neq' , $id) , 'status' => array ('neq' , -1));

        $category = get_category($category_id);
        if ($category['pid'] == 0) {
            $map['category_id'] = $category_id;
        } else {
            $parent             = get_parent_category($category['id']);
            $root               = array_shift($parent);
            $map['category_id'] = array ('in' , D("Category")->getChildrenId($root['id']));
        }

        $res = $this->where($map)->getField('id');
        if ($res) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 检查产品名称是否已存在(不重复)
     * @param string $name
     * @return true无重复，false已存在
     * @author ew_xiaoxiao
     */
    protected function checkTitle(){
        $title       = I('post.title');
        $category_id = I('post.category_id' , 0);
        $id          = I('post.id' , 0);

        $map = array ('title' => $title , 'id' => array ('neq' , $id) , 'status' => array ('neq' , -1));

        $category = get_category($category_id);
        if ($category['pid'] == 0) {
            $map['category_id'] = $category_id;
        } else {
            $parent             = get_parent_category($category['id']);
            $root               = array_shift($parent);
            $map['category_id'] = array ('in' , D("Category")->getChildrenId($root['id']));
        }

        $res = $this->where($map)->getField('id');
        if ($res) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 生成不重复的name标识
     * @author ew_xiaoxiao
     */
    private function generateName(){
        $str  = 'abcdefghijklmnopqrstuvwxyz0123456789';    //源字符串
        $min  = 10;
        $max  = 39;
        $name = FALSE;
        while (TRUE) {
            $length = rand($min , $max);    //生成的标识长度
            $name   = substr(str_shuffle(substr($str , 0 , 26)) , 0 , 1);    //第一个字母
            $name .= substr(str_shuffle($str) , 0 , $length);
            //检查是否已存在
            $res = $this->getFieldByName($name , 'id');
            if (!$res) {
                break;
            }
        }
        return $name;
    }

    /**
     * 生成推荐位的值
     * @return number 推荐位
     * @author ew_xiaoxiao
     */
    protected function getPosition(){
        $position = I('post.position');
        if (!is_array($position)) {
            return 0;
        } else {
            $pos = 0;
            foreach ($position as $key => $value) {
                $pos += $value;        //将各个推荐位的值相加
            }
            return $pos;
        }
    }


    /**
     * 删除状态为-1的数据（包含扩展模型）
     * @return true 删除成功， false 删除失败
     * @author ew_xiaoxiao
     */
    public function remove(){
        //查询假删除的基础数据
        if (is_administrator()) {
            $map = array ('status' => -1);
        } else {
            $cate_ids = AuthGroupModel::getAuthCategories(UID);
            $map      = array ('status' => -1 , 'category_id' => array ('IN' , trim(implode(',' , $cate_ids) , ',')));
        }
        $base_list = $this->where($map)->field('id,model_id')->select();
        //删除扩展模型数据
        $base_ids = array_column($base_list , 'id');
        //孤儿数据
        $orphan = get_stemma($base_ids , $this , 'id,model_id');

        $all_list = array_merge($base_list , $orphan);
        foreach ($all_list as $key => $value) {
            $logic = $this->logic($value['model_id']);
            $logic->delete($value['id']);
        }

        //删除基础数据
        $ids = array_merge($base_ids , (array) array_column($orphan , 'id'));
        if (!empty($ids)) {
            $res = $this->where(array ('id' => array ('IN' , trim(implode(',' , $ids) , ','))))->delete();
        }

        return $res;
    }

    /**
     * 获取链接id
     * @return int 链接对应的id
     * @author ew_xiaoxiao
     */
    protected function getLink(){
        $link = I('post.link_id');
        if (empty($link)) {
            return 0;
        } else if (is_numeric($link)) {
            return $link;
        }
        $res = D('Url')->update(array ('url' => $link));
        return $res['id'];
    }

    /**
     * 保存为草稿
     * @return array 完整的数据， false 保存出错
     * @author ew_xiaoxiao
     */
    public function autoSave(){
        $post = I('post.');

        /* 检查文档类型是否符合要求 */
        $res = $this->checkDocumentType(I('type' , 2) , I('pid'));
        if (!$res['status']) {
            $this->error = $res['info'];
            return FALSE;
        }

        //触发自动保存的字段
        $save_list = array ('name' , 'title' , 'description' , 'position' , 'link_id' , 'cover_id' , 'deadline' , 'create_time' , 'content');
        foreach ($save_list as $value) {
            if (!empty($post[$value])) {
                $if_save = TRUE;
                break;
            }
        }

        if (!$if_save) {
            $this->error = '您未填写任何内容';
            return FALSE;
        }

        //重置自动验证
        $this->_validate = array (
            array ('name' , '/^[a-zA-Z]\w{0,39}$/' , '文档标识不合法' , self::VALUE_VALIDATE , 'regex' , self::MODEL_BOTH) ,
            array ('name' , '' , '标识已经存在' , self::VALUE_VALIDATE , 'unique' , self::MODEL_BOTH) ,
            array ('title' , '1,80' , '标题长度不能超过80个字符' , self::VALUE_VALIDATE , 'length' , self::MODEL_BOTH) ,
            array ('description' , '1,140' , '简介长度不能超过140个字符' , self::VALUE_VALIDATE , 'length' , self::MODEL_BOTH) ,
            array ('category_id' , 'require' , '分类不能为空' , self::MUST_VALIDATE , 'regex' , self::MODEL_BOTH) ,
            array ('category_id' , 'check_category' , '该分类不允许发布内容' , self::EXISTS_VALIDATE , 'function' , self::MODEL_UPDATE) ,
            array ('category_id,type' , 'check_category' , '内容类型不正确' , self::MUST_VALIDATE , 'function' , self::MODEL_INSERT) ,
            array ('model_id,pid,category_id' , 'check_catgory_model' , '该分类没有绑定当前模型' , self::MUST_VALIDATE , 'function' , self::MODEL_INSERT) ,
            array (
                'deadline' ,
                '/^\d{4,4}-\d{1,2}-\d{1,2}(\s\d{1,2}:\d{1,2}(:\d{1,2})?)?$/' ,
                '日期格式不合法,请使用"年-月-日 时:分"格式,全部为数字' ,
                self::VALUE_VALIDATE ,
                'regex' ,
                self::MODEL_BOTH
            ) ,
            array (
                'create_time' ,
                '/^\d{4,4}-\d{1,2}-\d{1,2}(\s\d{1,2}:\d{1,2}(:\d{1,2})?)?$/' ,
                '日期格式不合法,请使用"年-月-日 时:分"格式,全部为数字' ,
                self::VALUE_VALIDATE ,
                'regex' ,
                self::MODEL_BOTH
            ) ,
        );
        $this->_auto[]   = array ('status' , '3' , self::MODEL_BOTH);

        if (!($data = $this->create())) {
            return FALSE;
        }

        /* 添加或新增基础内容 */
        if (empty($data['id'])) { //新增数据
            $id = $this->add(); //添加基础内容
            if (!$id) {
                $this->error = '新增基础内容出错！';
                return FALSE;
            }
            $data['id'] = $id;
        } else { //更新数据
            $status = $this->save(); //更新基础内容
            if (FALSE === $status) {
                $this->error = '更新基础内容出错！';
                return FALSE;
            }
        }

        /* 添加或新增扩展内容 */
        $logic = $this->logic($data['model_id']);
        if (!$logic->autoSave($id)) {
            if (isset($id)) { //新增失败，删除基础数据
                $this->delete($id);
            }
            $this->error = $logic->getError();
            return FALSE;
        }

        //内容添加或更新完成
        return $data;
    }

    /**
     * 检查指定文档下面子文档的类型
     * @param intger $type 子文档类型
     * @param intger $pid 父文档类型
     * @return array 键值：status=>是否允许（0,1），'info'=>提示信息
     * @author ew_xiaoxiao
     */
    public function checkDocumentType($type = NULL , $pid = NULL){
        $res = array ('status' => 1 , 'info' => '');
        if (empty($type)) {
            return array ('status' => 0 , 'info' => '文档类型不能为空');
        }
        if (empty($pid)) {
            return $res;
        }
        //查询父文档的类型
        $ptype = is_numeric($pid) ? $this->getFieldById($pid , 'type') : $this->getFieldByName($pid , 'type');
        //父文档为目录时
        switch ($ptype) {
            case 1: // 目录
            case 2: // 主题
                break;
            case 3: // 段落
                return array ('status' => 0 , 'info' => '段落下面不允许再添加子内容');
            default:
                return array ('status' => 0 , 'info' => '父文档类型不正确');
        }
        return $res;
    }

}