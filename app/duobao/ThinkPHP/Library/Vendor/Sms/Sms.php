<?php
namespace Vendor\Sms;
class Sms {
	
	function __construct() {
		$this->Sms();
	}
	function Sms(){

    }

	//短信发送函数
	function sendcode($mobile,$content,$send_code,$mobile_code){
		if(empty($mobile)){
			exit('手机号码不能为空');
		}
		
		if(empty($_SESSION['send_code']) or $send_code!=$_SESSION['send_code']){
			//防用户恶意请求
			exit('请求超时，请刷新页面后重试');
		}

		$retult = $this->sendsms($mobile,$content);//发送短信

		if($retult > 0){//发送失败
			$return=array("status"=>0,"info"=>$retult);
		}else{//发送成功
			$_SESSION['mobile'] = $mobile;//注册session
	        $_SESSION['mobile_code'] = $mobile_code;
			//验证记录
			$verification=M("verification");
			$data['mobile']= $mobile;
			$data['create_time']=NOW_TIME;
			$data['status']=1;
			$data['group']=2;
			$data['uid']=D("member")->uid();
			$verification->create();
			$verification->add($data);
			//短信记录
			$sms=M("sms");
			$smsdata['mobile']= $mobile;
			$smsdata['content']=$content;
			$smsdata['create_time']=NOW_TIME;
			$smsdata['group']=1;//验证
			$sms->create();
			$sms->add($smsdata);
			$return=array("status"=>1,"info"=>"短信已发送");
		}
		return $return;
	}

	function sendsms($moblie,$content){
		$sn = C('SMSACCOUNT'); //提供的账号
		$pwd= strtoupper(md5(C('SMSACCOUNT').C('SMSPASSWORD')));
		$data = array(
			'sn' => $sn, //提供的账号
			'pwd' =>$pwd, //此处密码需要加密 加密方式为 md5(sn+password) 32位大写
			'mobile' => $moblie, //手机号 多个用英文的逗号隔开 post理论没有长度限制.推荐群发一次小于等于10000个手机号
			'content' =>htmlspecialchars($content), //短信内容
			//htmlspecialchars() 函数把一些预定义的字符转换为 HTML 实体。
			'ext' => '',
			'stime' => '', //定时时间 格式为2011-6-29 11:09:21
			'rrid' => '',//默认空 如果空返回系统生成的标识串 如果传值保证值唯一 成功则返回传入的值
			'msgfmt'=>''
		);

		$url = "http://sdk.entinfo.cn:8061/webservice.asmx/mdsmssend";

		$retult=$this->api_notice_increment($url,$data);

		$retult=str_replace("<?xml version=\"1.0\" encoding=\"utf-8\"?>","",$retult);
		$retult=str_replace("<string xmlns=\"http://tempuri.org/\">","",$retult);
		$retult=str_replace("</string>","",$retult);

		return $retult;
	}

	function api_notice_increment($url, $data){
		$curl = curl_init(); // 启动一个CURL会话
		curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
		curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
		curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
		curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
		$data = http_build_query($data);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
		curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
		curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回

		$lst = curl_exec($curl);
		if (curl_errno($curl)) {
			echo 'Errno'.curl_error($curl);//捕抓异常
		}
		curl_close($curl);
		return $lst;
	}


}

?>