<?php
class UserModel extends Model {
    //�Զ���֤  
    protected $_validate=array(
		//ÿ���ֶε���ϸ��֤����  
		//array("username", "require", "�˻�������Ϊ��"),  
		//array("username", "checkLength", "�û������Ȳ�����Ҫ��", 0, 'callback'),  
		//array("password", "require", "���벻��Ϊ��"),  
		//array("password", "checkLength", "���볤�ȵ�Ҫ����5~15λ֮��", 0, 'callback'),  
		//array("password", "repassword", "�����������벻һ��", 0, 'confirm'),    
		//array('username','','account exists',self::EXISTS_VAILIDATE,'unique',self::MODEL_INSERT),     
	);

    //�Զ����  
    protected $_auto=array(                
		array("password", "md5", 3, 'function'),  
		array("ctime", "ctime", 3, 'callback'),  
		array("remoteip", "getIp", 3, 'callback'),  			  
	);  
	
	//�Զ�����֤����������֤�û����ĳ����Ƿ�Ϸ�  
	//$date�β�  ����д�������� $AA  $bb  
	function checkLength($data){  
		//$data���ŵľ���Ҫ��֤���û�������ַ���  
		if(strlen($data) < 3 || strlen($data) > 15){  			  
			return false;  
		}else{  			  
			return true;  
		}  
	}       
          
	//���ط����ߵ�IP��ַ  
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