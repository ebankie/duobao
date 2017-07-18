<?php
// +----------------------------------------------------------------------
// | 微信管理系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2017  All rights reserved.
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------

namespace Control\Controller;

/**
 * 后台分类管理控制器
 * @author ew_xiaoxiao
 */
class AuthenticityController extends ControlController {

    /**
     * 产品SN号批量导入excel数据	
     * @author ew_xiaochuan
     */

    public function index(){
		if($_POST['submit']){
			$filePath='/www/web/bcy/Ewshop/Control/View/Authenticity';
			$filename = $_FILES['inputExcel']['name'];
			$tmp_name = $_FILES['inputExcel']['tmp_name'];
			$filetype = $_FILES['inputExcel']['type'];
			
			$extend=strrchr ($filename,'.');//获取上传文件的扩展名
			if($extend!='.xls' &&  $extend!='.xlsx'){//判断格式
			    $this->error("对不起，导入数据格式必须是xls或者xlsx格式文件哦，请您调整格式后重新上传，谢谢！");
			}
			
			//保存上传的文件
			$str = "";  
			//下面的路径按照你PHPExcel的路径来修改
			//require_once '/www/web/bcy/Ewshop/Control/View/Authenticity/PHPExcel_1.8.0/Classes/PHPExcel.php';
			//require_once '/www/web/bcy/Ewshop/Control/View/Authenticity/PHPExcel_1.8.0/Classes/PHPExcel/IOFactory.php';
			//require_once '/www/web/bcy/Ewshop/Control/View/Authenticity/PHPExcel_1.8.0/Classes/PHPExcel/Reader/Excel5.php';
			
			
		
			//注意设置时区
			//$time=date("y-m-d-H-i-s");//去当前上传的时间
			$uploadfile=$filePath.$filename;//上传后的文件名地址
			//move_uploaded_file() 函数将上传的文件移动到新位置。若成功，则返回 true，否则返回 false。
			$result=move_uploaded_file($tmp_name,$uploadfile);//上传到当前目录下
			if($result) //如果上传文件成功，就执行导入excel操作
			{
				
				vendor('PHPExcel');
				$PHPExcel = new \PHPExcel();
				
				$objReader = \PHPExcel_IOFactory::createReader('Excel5');//use excel2007 for 2007 format
				$objPHPExcel = $objReader->load($uploadfile);
				$sheet = $objPHPExcel->getSheet(0);
				$highestRow = $sheet->getHighestRow();           //取得总行数
				$highestColumn = $sheet->getHighestColumn(); //取得总列数
				var_dump($highestRow);

				var_dump($highestColumn);
				//循环读取excel文件,读取一条,插入一条
				for($j=2;$j<=$highestRow;$j++)                        //从第二行开始读取数据
				{
					for($k='A';$k<=$highestColumn;$k++)            //从A列读取数据
					{
						//                这种方法，以'~'合并为数组，再分割为字段值插入到数据库                实测在excel中，如果某单元格的值包含了导入的数据会为空       
						//
						$str .=$objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue().'~';//读取单元格

					}
					//echo $str; die();
					//explode:函数把字符串分割为数组。
					$str = rtrim($str,'~'); //PHP去除字符串中的最后一个'~'字符
					$strs = explode('~',$str);
					print_r($strs);

					$d['sn_code'] = $strs[0];
					M('Authenticity')->data($d)->add(); 
					//$sql ="INSERT INTO  ewshop_authenticity  (`sn_code`) VALUES('{$strs[0]}')";
					//die($sql);
					$str = "";
				} 
				unlink($uploadfile); //删除上传的excel文件
				$this->success('导入成功！');
			}else{
				$this->success('导入失败！');
			}
		}
        $this->display();
    }




}
