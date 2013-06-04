<?php
// +----------------------------------------------------------------------
// add todos

class CategoryAction extends Action {//BaseAction{
	private function isLogin() {
		return (isset($_SESSION['member']) && isset($_SESSION['userid']));
	}
	
	/**/
	public function add_category() {
		if (!$this->isLogin()) {
			return $this->display(":User/login");
		}

		$_POST['userid'] = $_SESSION['userid'];
		$Cat = D('category');
		
		$data = $Cat->create();
        if($data) {
            $result = $Cat->add();
            if($result) {
				return $this->redirect("/Todo/");
            }else{
                $this->error();
            }
        }else{
            $this->error($Cat->getError());
        }
	}
	
	/**/
	public function management() {
		if (!$this->isLogin()) {
			return $this->display(":User/login");
		}

		//query category
		$Cat = M('category');
		$this->catdata = $Cat->field(array('id', 'name'))->where('userid', $_SESSION['userid'])->select();
		return $this->display();
	}
	
	public function rename() {
		if (!$this->isLogin()) {
			return $this->display(":User/login");
		}
		
		if (!isset($_POST['name']) || strlen($_POST['name']) < 1) {
			return $this->display("management");
		}
		
		$Cat = M('Category');
		$condition['id'] = $_POST['id'];
		$ajaxdata['result'] = $Todo->where($condition)->setField('name', $_POST['name']);
		$ajaxdata['info'] = $Todo->getLastSql();
		$this->ajaxReturn($ajaxdata);
	}
}
?>