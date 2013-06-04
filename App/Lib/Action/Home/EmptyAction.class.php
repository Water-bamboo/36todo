<?php
// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2008 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

class EmptyAction extends Action {
    /*
	public function _empty($method) {
        $this->assign('message','访问的页面不存在！');
        $this->display(C('TMPL_ACTION_ERROR'));
    }*/

	public function _empty($method) {
        $this->redirect("/Todo/");
    }
}
?>