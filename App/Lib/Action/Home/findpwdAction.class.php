<?php

class findpwdAction extends frontendAction {

    /**
    * 填写信息
    */
    public function index() {
        if (IS_POST) {
            $captcha = $this->_post('captcha', 'trim');
			//print_r($captcha);
			//print_r(session('captcha'));
            session('captcha') != md5($captcha) && $this->error(L('captcha_failed'));
            $tpl_data = array();
            $tpl_data['username'] = $username = $this->_post('username','trim');
            //验证
            $user = M('user')->where(array('username'=>$username))->find();
            !$user && $this->error(L('user_not_exists'));
            //生成随机码
            $time = time();
            $activation = md5($user['ctime'] . substr($user['password'], 10) . $time);
			print_r($activation);
            $url_args = array('username'=>$user['username'], 'activation'=>$activation, 't'=>$time);
            $tpl_data['reset_url'] = U('findpwd/verify_mailbox', $url_args, '', '', true);
			//print_r(
            //解析邮件模板
			//print_r($tpl_data);
            $mail_body = D('message_tpl')->get_mail_info('findpwd', $tpl_data);
			//print_r($mail_body);
            //发送邮件
            $this->_mail_queue($user['mail'], "36todo邮箱验证", $mail_body);
            $this->success("认证链接已发送至邮箱");//L('findpwd_mail_sended'));
			//$this->ajaxReturn($mail_body);
        } else {
            //$this->_config_seo();
            $this->display();
        }
    }

	/**
     * 重置密码
     */
    public function verify_mailbox() {
        //检测链接合法性
        $username = $this->_get('username', 'trim');
		//print_r($username);
        $activation = $this->_get('activation', 'trim');
		//print_r($activation);
        $t = $this->_get('t', 'intval');
		//print_r($t);
        if (!$username || !$activation || !$t) {
            $this->redirect('Todo/index');
        }
        //判断是否已经过期
        $time = time();
        ($time - $t) > 3600 && $this->error(L('findpwd_link_expired'), U('findpwd/index'));
        //验证用户
        $user = M('user')->field('id,ctime,password')->where(array('username'=>$username))->find();
		//print_r($user);
        !$user && $this->error(L('username').L('not_exist'), U('Todo/index'));
        if ($activation != md5($user['ctime'] . substr($user['password'], 10) . $t)) {
            $this->error(L('findpwd_link_error'), U('Todo/index'));
        }
        
		//连接用户中心
		/*
		$passport = $this->_user_server();
		$result = $passport->edit($user['id'], '', array('password'=>$password), true);
		if (!$result) {
			$this->error($passport->get_error());
		}
		$this->success(L('reset_password_success'), U('user/login'));
		*/
		$M_user = M('user');
		$result = $M_user->where(array('id'=>$user['id']))->setField('infoverified', 1);
		if ($result) {
			$this->success("认证完成", U('Todo/index'));
		}
		else {
			print_r($M_user->getLastSql());
			$this->error($result);
		}
    }
	
    /**
     * 重置密码
     */
    public function reset() {
        //检测链接合法性
        $username = $this->_get('username', 'trim');
        $activation = $this->_get('activation', 'trim');
        $t = $this->_get('t', 'intval');
        if (!$username || !$activation || !$t) {
            $this->redirect('index/index');
        }
        //判断是否已经过期
        $time = time();
        ($time - $t) > 3600 && $this->error(L('findpwd_link_expired'), U('findpwd/index'));
        //验证用户
        $user = M('user')->field('id,reg_time,password')->where(array('username'=>$username))->find();
        !$user && $this->error(L('username').L('not_exist'), U('index/index'));
        if ($activation != md5($user['reg_time'] . substr($user['password'], 10) . $t)) {
            $this->error(L('findpwd_link_error'), U('index/index'));
        }
        if (IS_POST) {
            $captcha = $this->_post('captcha', 'trim');
            session('captcha') != md5($captcha) && $this->error(L('captcha_failed'));
            
            $password   = $this->_post('password','trim');
            $repassword = $this->_post('repassword','trim');
            !$password && $this->error(L('no_new_password'));
            $password != $repassword && $this->error(L('inconsistent_password'));
            $passlen = strlen($password);
            if ($passlen < 6 || $passlen > 20) {
                $this->error('password_length_error');
            }
            //连接用户中心
            $passport = $this->_user_server();
            $result = $passport->edit($user['id'], '', array('password'=>$password), true);
            if (!$result) {
                $this->error($passport->get_error());
            }
            $this->success(L('reset_password_success'), U('user/login'));
        } else {
            //$this->_config_seo();
            $this->display();
        }
    }
}