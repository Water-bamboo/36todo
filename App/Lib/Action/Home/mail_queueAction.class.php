<?php

class mail_queueAction extends Action {

    /**
    * 填写信息
    */
    public function index() {
        $cnt = D("mail_queue")->send();
		return $this->ajaxReturn(array("count"=>$cnt));
    }
}
