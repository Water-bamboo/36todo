<?php
// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2007 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

// 后台用户模块
class UserAction extends CommonAction {
	// 检查帐号
	public function checkAccount() {
        if(!Think::validate($_POST['account'],'/^[a-z]\w{4,}$/i')) {
            $this->error( '用户名必须是以字母打头，且5位以上！');
        }
		$User = D("User");
        // 检测用户名是否冲突
        $name  =  $_REQUEST['account'];
        $result  =  $User->getByAccount($name);
        if($result) {
        	$this->error('该用户名已经存在！');
        }else {
           	$this->success('该用户名可以使用！');
        }
    }

	// 插入数据
	public function insert() {
		// 创建数据对象
		$User	 =	 D("User");
		if(!$User->create()) {
			$this->error($User->getError());
		}else{
			// 写入帐号数据
			if($result	 =	 $User->add()) {
				if($this->name != 'User') {
					$Model	=	D($this->name);
					if($Model->create()) {
						$Model->user_id	=	$result;
						if(!$Model->add()){
							$this->error('数据写入失败！');
						}
					}else{
						$this->error($Model->getError());
					}
				}
				$this->addRole($result);
				$this->success('数据写入成功！');
			}else{
				$this->error('数据写入失败！');
			}
		}
	}

	protected function addRole($userId) {
		//新增用户自动加入相应权限组
		$RoleUser = D("RoleUser");
		$RoleUser->user_id	=	$userId;
        // 默认加入网站编辑组
        $RoleUser->role_id	=	3;
		$RoleUser->add();
	}

    //重置密码
    public function resetPwd()
    {
    	$id  =  $_POST['id'];
        $password = $_POST['password'];
        if(''== trim($password)) {
        	$this->error('密码不能为空！');
        }
        $User = D('User');
		$User->password	=	md5($password);
		$User->id			=	$id;
		$result	=	$User->save();
        if(false !== $result) {
            $this->success("密码修改为$password");
        }else {
        	$this->error('重置密码失败！');
        }
    }
}
?>