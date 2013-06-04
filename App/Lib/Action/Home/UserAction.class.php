<?php
class UserAction extends Action {
    public function insert()
	{
		header('Content-Type:text/html;charset=utf-8');		
		
		$_POST['username'] = $_POST['mail'];
		$username=$_POST['username'];
		
		/*
		if(md5($_POST['verify']) != $_SESSION['verify']){			
            $this->error("verify falied");  
        } */
		
		//实例化自定义模型  M('User')实例化基础模型  
        $user=D("User");  
              
		if($user->create()){
			//执行插入操作，执行成功后，返回新插入的数据库的ID  
			$result = $user->add();
		    if($result){
				return $this->checklogin();
				/*$this->redirect('/Todo/checklogin');/*
				$_SESSION['member'] = $username; 
				$_SESSION['userid'] = $user->getField('id');*/
				//$this->redirect('/Todo/');
			}else{
				$this->error("register failed");      
			}
		}else{  
			//把错误信息提示给用户看  
			$this->error($user->getError());  
		}
	}
	
	//生成图片验证码  
    function verify(){
        //导入图形处理类库  
        import("ORG.Util.Image");  
		//import("@.ORG.Util.Image");
        /* 生成图形验证码  
			length：验证码的长度，默认为4位数 
			mode：验证字符串的类型，默认为数字，其他支持类型有0 字母 1 数字 2 大写字母 3 小写字母 4中文 5混合（去掉了容易混淆的字符oOLl和数字01）  
			type：验证码的图片类型，默认为png  
			width：验证码的宽度，默认会自动根据验证码长度自动计算 
			height：验证码的高度，默认为22 
			verifyName：验证码的SESSION记录名称，默认为verify            
        */  
		/* 整了半天，不加这行显示不出来 */
		ob_end_clean();
        //实现英文验证码  
        image::buildImageVerify(4,1,'gif',60,22,'verify');  
        //实现中文验证码  
        //image::GBVerify();  
    }
	
	// 登陆
	public function checklogin(){
		$Uname = $_POST['username'];
		$Pwd = $_POST['password'];
		if(empty($Uname)){
			$this->success("账户名不能为空~");
		} else if(empty($Pwd)){
			$this->error("密码不能为空~");
		}
		
		$User = M("user");//change from LoginModel to UserModel by shuizhu
		$condition['password'] = md5($Pwd);
		
		$Result = $this->checkEmail($Uname);
		if ($Result) {
			//执行用户登录    	
			$condition['mail'] = $Uname;
		} else {
			//执行用户登录    	
			$condition['username'] = $Uname;						
		}
		$Result = $User->where($condition)->select();
		//dump($Result);
		if ($Result) {
			$_SESSION['member'] = $Result[0]['username'];
			$_SESSION['userid'] = $Result[0]['id'];
			$this->redirect('/Todo/');
			//$this->display('/Todo/');
		}
		else {
			$this->error("账户名或密码出错~");
		}
	}
	
	public function checkEmail($inAddress)
	{
		return (ereg("^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+",$inAddress));
	}
	
	//是否有这个邮箱
	public function retrievepwd(){
		$email = $_POST['email'];
		$User = M("user");
		$condition['mail'] = $email;

		$result=$User->where($condition)->select();			
		if($result){
			//可以用MD5加密或者base64_decode/base64_encode加解密
			$mailsubject = "账号找回密码";//邮件主题
			$mainbody = "我们收到了此邮箱的密码重置请求。请点击该链接重置您的密码：\n";//邮件内容
			$mainbody.="http://www.36todo.com/36todo/index.php/User/passwordreset?id=".$result[0]['id']."\n";
			$mainbody.="该链接会在24小时后失效。\n";
			$mainbody.="如果您要放弃以上修改，或未曾申请密码重置，请忽略并/或删除本邮件。\n";
			$mainbody.="\n";
			$mainbody.="此致,\n";
			$mainbody.="36todo客服团队";
			$this->Send_Mail($email, $mailsubject, $mainbody);
			return $this->display(":User/mailsend");			
		}else{
			$this->error("邮箱不存在~");
		}
	}
	
	public function Send_Mail($address,$title,$message)
	{
		vendor('PHPMailer.class#phpmailer');
		$mail=new PHPMailer();
		// 设置PHPMailer使用SMTP服务器发送Email
		$mail->IsSMTP();
		// 设置邮件的字符编码，若不指定，则为'UTF-8'
		$mail->CharSet='UTF-8';
		// 添加收件人地址，可以多次使用来添加多个收件人
		$mail->AddAddress($address);
		// 设置邮件正文
		$mail->Body=$message;
		// 设置邮件头的From字段。
		$mail->From="noreply@36todo.com";
		// 设置发件人名字
		$mail->FromName='36todo客服团队';
		// 设置邮件标题
		$mail->Subject=$title;
		// 设置SMTP服务器。
		$mail->Host="mail.36todo.com";
		// 设置为“需要验证”
		$mail->SMTPAuth=true;
		// 设置用户名和密码。
		$mail->Username="noreply@36todo.com";
		$mail->Password="norePly@1";
		// 发送邮件。
		return($mail->Send());
	}
	
	//修改密码
	public function passwordreset(){
		$id=$_GET['id'];
		
		$User = M("user");

		$result=$User->where(array('id'=>$id))->find();
		//echo $data;
		$this->assign('result', $result);
		return $this->display(":User/resetpwd"); 
	}
	
	public function updatepwd(){
		$id					= $_POST['id'];

		$data['password']	= $_POST['password'];
		$data['repassword']	= $_POST['repassword'];

		$User = D("User");
		$result=$User->where(array('id'=>$id))->find();
		if($result){
			if($data['password']=$data['repassword']){
				$arr['password']=md5($data['password']);
				$User->where(array('id'=>$id))->save($arr);
				//echo $model->getLastSql();
				
				$this->success("密码修改完成",("login"));
			}
			else{
				$this->error("密码不相符");
			}
		}else{
			$this->error("不存在此用户");
		}
	}
	
	public function changepwd(){
		$id = $_SESSION['userid'];
		
		$User = D("User");
		$result=$User->where(array('id'=>$id))->find();
		if($result){
			$this->assign('result', $result);
			return $this->display(":User/changepwd");
		}else{
			$this->error("不存在此用户");
		}
	}
	
	public function edit()
	{
		$userid = $_SESSION['userid'];
		$User = D("User");
		
		$result=$User->where(array('id'=>$userid))->find();
		if($result){
			$this->assign('result', $result);
			return $this->display(":User/edit");
		}else{
			$this->error("不存在此用户");
		}			
	}
	
	// 用户登出
    public function logout()
    {
        if(isset($_SESSION['member'])) {
			unset($_SESSION['member']);
			unset($_SESSION['userid']);
			unset($_SESSION);
			session_destroy();
			$this->redirect("/Todo/");
            //$this->assign("jumpUrl",__URL__.'/Todo');
            //$this->success('登出成功！');
        }else {
            $this->error('已经登出！');
        }
    }
	
	public function login() {
		$this->display("login");
	}
	
	public function generate_verify_mail() {
		//1.generate a random number
		//2. save random number to verify table
		//3. send mail with this verify code
		
		//验证
        $user = M('user')->where(array('id'=>$_SESSION['userid']))->find();

		//生成随机码
        $time = time();
        $activation = md5($user['ctime'] . substr($user['password'], 10) . $time);
		$url_args = array('username'=>$user['username'], 'activation'=>$activation, 't'=>$time);

		$tpl_data['reset_url'] = U('User/click_verify_mail', $url_args, '', '', true);
		
		$ForVerify = D('forverify');
		
		if ($ForVerify->create()) {
			
		}

		$this->Send_Mail();
		print_r($tpl_data);
		$this->ajaxReturn(L('findpwd_mail_sended'));
	}

	public function click_verify_mail() {
		
	}
}

?>
