<?php
class UserModel extends Model {
    //自动验证  
    protected $_validate=array(
		//每个字段的详细验证内容  
		//array("username", "require", "账户名不能为空"),  
		//array("username", "checkLength", "用户名长度不符合要求", 0, 'callback'),  
		//array("password", "require", "密码不能为空"),  
		//array("password", "checkLength", "密码长度的要求是5~15位之间", 0, 'callback'),  
		//array("password", "repassword", "两次密码输入不一致", 0, 'confirm'),    
		//array('username','','account exists',self::EXISTS_VAILIDATE,'unique',self::MODEL_INSERT),     
	);

    //自动填充  
    protected $_auto=array(                
		array("password", "md5", 3, 'function'),  
		array("ctime", "ctime", 3, 'callback'),  
		array("remoteip", "getIp", 3, 'callback'),  			  
	);  
	
	//自定义验证方法，来验证用户名的长度是否合法  
	//$date形参  可以写成任意如 $AA  $bb  
	function checkLength($data){  
		//$data里存放的就是要验证的用户输入的字符串  
		if(strlen($data) < 3 || strlen($data) > 15){  			  
			return false;  
		}else{  			  
			return true;  
		}  
	}       
          
	//返回访问者的IP地址  
	function getIp(){  		  
		return $_SERVER['REMOTE_ADDR'];  
	}  
  
	function ctime(){  			  
		return date("Y-m-d H:i:s");  
	}
	
	function login($name)
    {
        echo $this->fields['username'];
        $res=$this->query("select * from think_user where username='$name'");
        return $res;                  
    } 
}

?>