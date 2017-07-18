<?php
// +----------------------------------------------------------------------
// | 微信管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2017  All rights reserved.
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------
namespace Control\Controller;
use User\Api\UserApi;

/**
 * 拍卖订单管理控制器
 * @author
 */
class BiddingController extends ControlController
{
    /**
     * 订单列表
     * author
     */
    public function index(){
        //当前管理员id
        $gid = $_SESSION['onethink_admin']['user_auth']['uid'];
        $groupid = M('admin')->where(array('uid'=>$gid))->getField('groupid');
        if($groupid == 7){    //是企业分销管理员
            $userinfo = M('Join')->where(array('gid'=>$gid,'is_delete'=>0,'status'=>1))->field('uid,ratio')->find();
            $ratio = $userinfo['ratio'];
            $ids = M('Member')->where(array('parent_id'=>$userinfo['uid']))->getField('uid',true);
//            $ids[] = $uid;
            if($ids){
                $map['uid'] = array("in", $ids);
            }
        }
        $this->assign('groupid', $groupid);
        $nickname = I('nickname');
        $nickname = trim($nickname);
        if ($nickname) {
            $uid = M('Member')->field("uid")->where("nickname LIKE '%$nickname%'")->select();
            $newuid = "";
            if ($uid) {
                foreach ($uid as $b) {
                    $newuid[] = $b['uid'];
                }
                $map['uid'] = array("in", $newuid);
            } else {
                $map['uid'] = '';
            }
        }
        if (!empty($_GET['goods'])) {
            $_GET['goods'] = htmlspecialchars(trim($_GET['goods']));
            $goodsIdList = M()->query("SELECT id FROM ewshop_document WHERE title LIKE '%{$_GET['goods']}%'");
            if ($goodsIdList) {
                foreach ($goodsIdList as $k => $v) {
                    $gList[] = $v['id'];
                }
                $map['goods_id'] = array('in', $gList);
            } else {
                $map['goods_id'] = '';
            }
        }

        $map['status'] = 1;
        $list = $this->lists('WinOrder', $map, "create_time desc");
        foreach ($list as $key => $val) {
            $list[$key]['nickname'] = M('Member')->where("uid='$val[uid]'")->getField("nickname");
            $list[$key]['goods_title'] = M('Document')->getFieldById($val['goods_id'], 'title');
            $list[$key]['pay_status'] = $val['status'] == 1 ? '已支付' : '未支付';                          $list[$key]['commission'] = $val['money'] * $ratio * 0.01;
        }
        int_to_string($list);

        $this->assign('_list', $list);
        $this->meta_title = '订单列表';
        $this->display();
    }


    /**
     * 导出订单信息信息
     * @author
     */
    public function export()
    {

        $mobile = I('mobile');
        $mobile = trim($mobile);
        if ($mobile) {
            $uid = M('UcenterMember')->field("id")->where("username LIKE '%$mobile%'")->select();
            $newuid = "";
            if ($uid) {
                foreach ($uid as $b) {
                    $newuid[] = $b['uid'];
                }
                $map['uid'] = array("in", $newuid);
            }
        }
        if (!empty($_GET['goods'])) {
            $_GET['goods'] = htmlspecialchars(trim($_GET['goods']));
            $goodsIdList = M()->query("SELECT id FROM ewshop_document WHERE title LIKE '%{$_GET['goods']}%'");
            foreach ($goodsIdList as $k => $v) {
                $gList[] = $v['id'];
            }
            $map['goods_id'] = array('in', $gList);
        }


        if (!empty($_GET['time'])) {
            $startTime = strtotime($_GET['time']) - 1;
            $endTime = strtotime($_GET['time']) + 86400;
            $map['create_at'] = array(array('gt', $startTime), array('lt', $endTime));
        }
        // $map['is_get'] = 1;
        $list = $this->lists_diy('bidding_log', $map, "create_at desc");

        foreach ($list as $key => $val) {
            $list[$key]['mobile'] = M('UcenterMember')->getFieldById($val['uid'], 'mobile');
            $list[$key]['email'] = M('UcenterMember')->getFieldById($val['uid'], 'email');
            $list[$key]['goods_name'] = M('Document')->getFieldById($val['goods_id'], 'title');
            $list[$key]['start_price'] = M('Document')->getFieldById($val['goods_id'], 'start_price');
        }
        int_to_string($list);

        vendor('PHPExcel');
        $objPHPExcel = new \PHPExcel();

        // 设置文件的一些属性，在xls文件——>属性——>详细信息里可以看到这些值，xml表格里是没有这些值的
        $objPHPExcel->getProperties()//获得文件属性对象，给下文提供设置资源
        ->setCreator("admin")//设置文件的创建者
        ->setLastModifiedBy("admin")//设置最后修改者
        ->setTitle("Office 2007 XLSX Document")//设置标题
        ->setSubject("Office 2007 XLSX Document")//设置主题
        ->setDescription("Document for Office 2007 XLSX, generated using PHP classes.")//设置备注
        ->setKeywords("office 2007 openxml php")//设置标记
        ->setCategory("Result file"); //设置类别

        // 给表格添加数据（表头）
        $objPHPExcel->setActiveSheetIndex(0)//设置第一个内置表（一个xls文件里可以有多个表）为活动的
        ->setCellValue('A1', '电话')//数据格式可以为字符串
        ->setCellValue('B1', '邮箱')//数据格式可以为字符串
        ->setCellValue('C1', '拍卖品')//数据格式可以为字符串
        ->setCellValue('D1', '起拍价')//数据格式可以为字符串
        ->setCellValue('E1', '成交价')//数据格式可以为字符串
        ->setCellValue('F1', '成交时间'); //数据格式可以为字符串


        // 给表格添加数据（内容）
        $name = 2;
        foreach ($list as $k => $v) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $name, $v['mobile']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $name, $v['email']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $name, $v['goods_name']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $name, $v['start_price']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $name, $v['price']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $name, date('Y-m-d H:i:s', $v['create_at']));
            $name++;
        }

        //得到当前活动的表,注意下文教程中会经常用到$objActSheet
        $objActSheet = $objPHPExcel->getActiveSheet();
        // 给当前活动的表设置名称
        $objActSheet->setTitle('会员数据');
        //设置列的宽度
        $objActSheet->getColumnDimension('A')->setWidth(22); //30
        $objActSheet->getColumnDimension('B')->setWidth(35); //30
        $objActSheet->getColumnDimension('C')->setWidth(25); //30
        $objActSheet->getColumnDimension('D')->setWidth(50); //30
        $objActSheet->getColumnDimension('E')->setWidth(25); //30
        $objActSheet->getColumnDimension('F')->setWidth(25); //30

        /*********  浏览器输出  ********/

        // 生成2007excel格式的xlsx文件
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="订单数据.xlsx"'); //文件名称
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory:: createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }


    /**
     * 出价记录
     * author
     */
    public function bidding_log()
    {
        I('get.order_id') ? $map['id'] = I('get.order_id') : '';
        I('get.order_number') ? $map['order_number'] = I('get.order_number') : '';
        $map['status'] = 1;
        $data = M('WinOrder')->where($map)->find();
        $data['memberinfo'] = M('Member')->field('nickname')->where("uid = {$data['uid']}")->find();
        $data['goodsinfo']  = M('Document')->field('title')->where("id = {$data['goods_id']}")->find();
        $data['type']       = ($data['type'] == 1) ? '小' : '大';
        $this->assign('data',$data);
        $this->meta_title = '订单详情';
        $this->display();
    }

    /**
     * 兑换管理
     * @author
     */
    public function exchange(){
        //当前管理员id
        $gid = $_SESSION['onethink_admin']['user_auth']['uid'];
        $groupid = M('admin')->where(array('uid'=>$gid))->getField('groupid');
        if($groupid == 7){    //是企业分销管理员
            $uid = M('Join')->where(array('gid'=>$gid,'is_delete'=>0,'status'=>1))->getField('uid');
            $ids = M('Member')->where(array('parent_id'=>$uid))->getField('uid',true);
            $map['uid'] = array("in", $ids);
        }

        $nickname = I('nickname');
        //兑换码
        $exchange_number = I('exchange_number');
        $nickname = trim($nickname);
        if ($nickname) {
            $uid = M('Member')->field("uid")->where("nickname LIKE '%$nickname%'")->select();
            $newuid = "";
            if ($uid) {
                foreach ($uid as $b) {
                    $newuid[] = $b['uid'];
                }
                $map['uid'] = array("in", $newuid);
            } else {
                $map['uid'] = '';
            }
        }
        if(!empty($exchange_number)){
            $map['exchange_number'] = $exchange_number;
        }
        if (!empty($_GET['goods'])) {
            $_GET['goods'] = htmlspecialchars(trim($_GET['goods']));
            $goodsIdList = M()->query("SELECT id FROM ewshop_document WHERE title LIKE '%{$_GET['goods']}%'");
            if ($goodsIdList) {
                foreach ($goodsIdList as $k => $v) {
                    $gList[] = $v['id'];
                }
                $map['goods_id'] = array('in', $gList);
            } else {
                $map['goods_id'] = '';
            }
        }

        $map['is_virtual'] = 0;
        $list = $this->lists('WinExchange', $map, "create_time desc");
        foreach ($list as $key => $val) {
            $list[$key]['orderinfo'] = M('WinOrder')->where("id = {$val['order_id']} and status = 1")->find();
            $list[$key]['nickname'] = M('Member')->where("uid='$val[uid]'")->getField("nickname");
            $list[$key]['goods_title'] = M('Document')->getFieldById($list[$key]['orderinfo']['goods_id'], 'title');
            $list[$key]['is_exchange_val'] = ($val['is_exchange'] == 1) ? '已兑换' : '未兑换';
        }
        int_to_string($list);

        $this->assign('_list', $list);

        $this->meta_title = '兑换管理';
        $this->display();
    }

    /**
     * 设置成已兑换
     * @author
     */
    public function setExchanged(){
        $id = I('get.id');
        (empty($id)) ? $this->error('信息不存在') : '';
        $res = M('WinExchange')->where("id = {$id}")->setField('is_exchange',1);
        if($res){
            $this->success('设置成功');
        }else{
            $this->error('设置失败');
        }
    }

    /**
     * 添加备注
     * @author
     */
    public function addRemarks(){
        if(IS_POST){
            $id = $_POST['id'];
            $data['remarks'] = $_POST['remarks'];
            $res = M('WinExchange')->where("id = {$id}")->save($data);
            if($res){
                $this->success('添加成功',U('exchange'));
            }else{
                $this->error('添加失败');
            }
        }else{
            $id = I('get.id');
            (empty($id)) ? $this->error('信息不存在') : '';
            $info = M('WinExchange')->where("id = {$id}")->find();
            $this->assign('info', $info);
            $this->meta_title = '兑换管理';
            $this->display();
        }

    }




    /**
     * 开奖码管理
     * @author
     */
    public function code(){
        $map['code'] = array('neq',0);
        $list = $this->lists('WinCode', $map, "create_time desc");
//        foreach ($list as $key => $val) {
//            $list[$key]['orderinfo'] = M('WinOrder')->where("id = {$val['order_id']}")->find();
//            $list[$key]['nickname'] = M('Member')->where("uid='$val[uid]'")->getField("nickname");
//            $list[$key]['goods_title'] = M('Document')->getFieldById($list[$key]['orderinfo']['goods_id'], 'title');
//            $list[$key]['is_exchange_val'] = ($val['is_exchange'] == 1) ? '已兑换' : '未兑换';
//        }
        int_to_string($list);

        $this->assign('_list', $list);
        $this->meta_title = '开奖码管理';
        $this->display();
    }


    public function recharge(){
        //当前管理员id
        $gid = $_SESSION['onethink_admin']['user_auth']['uid'];
        $groupid = M('admin')->where(array('uid'=>$gid))->getField('groupid');
        if($groupid == 7){    //是企业分销管理员
            $uid = M('Join')->where(array('gid'=>$gid,'is_delete'=>0,'status'=>1))->getField('uid');
            $ids = M('Member')->where(array('parent_id'=>$uid))->getField('uid',true);
            $map['uid'] = array("in", $ids);
        }

        $nickname = I('nickname');
        $nickname = trim($nickname);
        if ($nickname) {
            $uid = M('Member')->field("uid")->where("nickname LIKE '%$nickname%'")->select();
            $newuid = "";
            if ($uid) {
                foreach ($uid as $b) {
                    $newuid[] = $b['uid'];
                }
                $map['uid'] = array("in", $newuid);
            } else {
                $map['uid'] = '';
            }
        }
        $map['status'] = 1;
        $list = $this->lists('RechargeOrder', $map, "create_time desc");
        foreach ($list as $key => $val) {
            $list[$key]['nickname'] = M('Member')->where("uid='$val[uid]'")->getField("nickname");
            $list[$key]['phone'] = M('UcenterMember')->where("id='$val[uid]'")->getField("username");
            $list[$key]['pay_status'] = $val['status'] == 1 ? '已支付' : '未支付';
        }
        int_to_string($list);

        $this->assign('_list', $list);
        $this->meta_title = '充值列表';
        $this->display();


    }




}