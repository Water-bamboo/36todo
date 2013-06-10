<?php
// +----------------------------------------------------------------------
// add todos

class TodoAction extends frontendAction {
	private function isLogin() {
		return (isset($_SESSION['member']) && isset($_SESSION['userid']));
	}
	
	public function index($r=-1, $catid=-1){
		//print_r($catid);
		if (!$this->isLogin()) {
			return $this->display(":User/login");
		}

		//print_r("catid="+$catid);
		/**
		*query category
		*/
		$condition['userid'] = $_SESSION['userid'];
		$Cat = M('category');
		$this->catdata = $Cat->field(array('id', 'name'))->where($condition)->select();
		///$this->catdata[count($this->catdata)] = array('0', '默认组');
		//$this->catdata[] = catdata2;
		//dump($this->catdata);
		//print_r($this->catdata);
		/**
		* qeury todos
		*/
		$Data = M('todos'); // 实例化Data数据模型
		$condition['isvalid'] = 1;
		
		if ($r == 1) {
			$condition['readiness'] = 0;
			$_SESSION['hide_done'] = 'checked';
		}
		else if ($r == 0) {
			$_SESSION['hide_done'] = '';
		}
		else if (isset($_SESSION['hide_done']) && !strcmp($_SESSION['hide_done'], 'checked')) {
			$condition['readiness'] = 0;
		}
		else {
			$_SESSION['hide_done'] = "";
		}
		
		if ($catid == -1) {
			if (isset($_SESSION['category_id'])) {
				$catid = $_SESSION['category_id'];
			}
		}
		//when category delete, catid > 0 should query out.
		if ($catid > 0) {
			$condition['category_id'] = $catid;
		}
		$_SESSION['category_id']=$catid;
		//print_r($_SESSION['category_id']);
        $this->data = $Data->where($condition)->order('id DESC')->select();

        $this->display();
    }
	
    public function insert() {
		if (!$this->isLogin()) {
			return $this->display(":User/door");
		}
		
		$_POST['userid'] = $_SESSION['userid'];
		$Form = D('todos');
		
		$data = $Form->create();
        if($data) {
            $result =   $Form->add();
            if($result) {
                $this->success('操作成功！');
				//$this->display("sdf");
            }else{
                $this->error();
            }
        }else{
            $this->error($Form->getError());
        }
    }
	
	public function insertajax() {
		$ajaxdata['result'] = -12;
		if (!$this->isLogin()) {
			return $this->ajaxReturn($ajaxdata);
		}
		
		$_POST['userid'] = $_SESSION['userid'];
		if (isset($_SESSION['category_id'])) {
			$catid = $_SESSION['category_id'];
			if ($catid > 0) {
				$_POST['category_id'] = $_SESSION['category_id'];
			}
		}
        $Form   =   D('todos');
		//trace($Form,'create vo');
		
		$data = $Form->create();
        if($data) {
            $result =   $Form->add();
            if($result) {
                //$this->success('操作成功！');
				$ajaxdata['info'] = 'insert ok';
				$ajaxdata['id'] = $result;
				$this->ajaxReturn($ajaxdata);
            }else{
				$ajaxdata['info'] = 'insert failed';
                $this->ajaxReturn($ajaxdata);
            }
        }else{
			$ajaxdata['info'] = 'insert create failed';
			$this->ajaxReturn($ajaxdata);
            //$this->error($Form->getError());
        }
    }
	
	/**
	* return time_up item in notification table, set remind_time=null.
	* 
	* todos表:remind_type
	* 默认提醒一次(-1)、规律提醒有（每天(1)、每周(2)、每月(3)、每年(4)）.
	*/
	public function get_timeup_notification() {
		$ajaxdata = -12;
		if (!$this->isLogin()) {
			return $this->ajaxReturn($ajaxdata);
		}

		$Noties = M('todos');
		$condition['userid'] = $_SESSION['userid'];
		$condition['readiness'] = 0;
		$condition['isvalid'] = 1;
		$condition['remind_time'] = array('lt', date('Y-m-d'));
		$ajaxdata = $Noties->field('id')->where($condition)->order('id DESC')->select();//id');

//		$ajaxdata = $Noties->getLastSql();
		$this->ajaxReturn($ajaxdata);
	}
	
	/**
	* return time_up item in notification table, set remind_time=null.
	* 
	* todos表:remind_type
	* 默认提醒一次(-1)、规律提醒有（每天(1)、每周(2)、每月(3)、每年(4)）.
	*/
	public function scan_timeup_notification() {
		$ajaxdata = -12;
		//if (!$this->isLogin()) {
		//	return $this->ajaxReturn($ajaxdata);
		//}
		
		/*
		if ($uid > 0) {
		}*/
		$Noties = M('todos');
		//$condition['userid'] = $uid;
		$condition['readiness'] = 0;
		$condition['isvalid'] = 1;
		$condition['remind_time'] = array('lt', date('Y-m-d'));
		$ajaxdata = $Noties->field(array('id', 'userid'))->where($condition)->select();//id');->order('id DESC')
		if (!$ajaxdata) {
			return $this->ajaxReturn(array("count"=>0));
		}
		//print_r($ajaxdata);
		
		$UserMailList = array();
		foreach ($ajaxdata as $value ) {
			//print_r($value['userid']);			
			
			if (!isset($UserMailList[$value['userid']])) {
				$findUserCond['id'] = $value['userid'];
				$User = M('user');
				$usermail = $User->field('mail')->where($findUserCond)->select();
				$UserMailList[$value['userid']] = $usermail[0];
			}
			/*else {
				print_r("exist");
			}*/

			//print_r($UserMailList[$value['userid']]);

			$findTodoCond['id'] = $value['id'];
			$body = $Noties->field('body')->where($findTodoCond)->select();
			//print_r($body[0]['body']);
			if (1) {
				//$this->sendWithMail(/*"chocolly@163.com"*/$UserMailList[$value['userid']], "[36todo.com]到期提醒", $body[0]['body']);
				$this->_mail_queue($UserMailList[$value['userid']], "36todo邮箱验证", $body[0]['body']);
			}
			else {
				$this->sendWithWeChat();
			}
			
			$Noties->where($findTodoCond)->setField('remind_time', NULL);
			/*
			if ($cnt > 3) {
				break;
			}*/
		}
//		$ajaxdata = $Noties->getLastSql();
		$this->ajaxReturn(array("count"=>count($ajaxdata)));
	}
	
	public function sendWithWeChat() {
		//
	}

	//update todo's content/body part.
	public function updateajax(){
		$ajaxdata['result'] = -12;
		if (!$this->isLogin()) {
			return $this->ajaxReturn($ajaxdata);
		}

        $Form   =   D('todos');
        if($Form->create()) {
            $result =   $Form->save();
            if($result) {
				$ajaxdata['result'] = 1;
				$ajaxdata['info'] = 'save ok';
                $this->ajaxReturn($ajaxdata);
            }else{
				$ajaxdata['result'] = 0;
				$ajaxdata['info'] = 'save failed';
                $this->ajaxReturn($ajaxdata);
            }
        }else{
			$ajaxdata['info']='create failed';
            $this->ajaxReturn($ajaxdata);
        }
    }
	
	public function updateremindetime() {
		$ajaxdata['result'] = -12;
		if (!$this->isLogin()) {
			return $this->ajaxReturn($ajaxdata);
		}
		
		//判断是否是日期
		$unixTime_1 = strtotime($_POST['time']);
		if (!is_numeric($unixTime_1)) {
			return $this->ajaxReturen($ajaxdata);
		}
		
		/*
		if (is_numeric(strtotime("00-00-00 00:00:00"))) {
			print_r("yes");
		}
		else {
			print_r("no");
		}*/

		$Todo = M('todos');
		$condition['id'] = $_POST['id'];
		$ajaxdata['result'] = $Todo->where($condition)->setField('remind_time', $_POST['time']);
		$ajaxdata['info'] = $Todo->getLastSql();
		$this->ajaxReturn($ajaxdata);
	}

	public function done($id=-1){
		$ajaxdata['result'] = -12;
		if (!$this->isLogin()) {
			return $this->ajaxReturn($ajaxdata);
		}
		
		if ($id < 0) {
			$ajaxdata['result'] = -1;
		}
		else {
			$Todo = M('todos');
			$condition['id'] = $id;
			$ajaxdata['result'] = $Todo->where($condition)->setField('readiness', 1);
		}
		$this->ajaxReturn($ajaxdata);
	}

	public function undone($id=-1){
		$ajaxdata['result'] = -12;
		if (!$this->isLogin()) {
			return $this->ajaxReturn($ajaxdata);
		}

		if ($id < 0) {
			$ajaxdata['result'] = -1;
		}
		else {
			$Todo = M('todos');
			$condition['id'] = $id;
			$ajaxdata['result'] = $Todo->where($condition)->setField('readiness', 0);
		}
		$this->ajaxReturn($ajaxdata);
	}
	
	public function delete($id=-1) {
		$ajaxdata['result'] = -12;
		if ($this->isLogin()) {		
			$Todo = M('todos');
			$condition['id'] = $id;
			$Todo->where($condition)->setField('isvalid', 0);
			$this->redirect("/Todo/");
		}
	}
}
?>