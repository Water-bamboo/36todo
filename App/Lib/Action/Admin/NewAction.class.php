<?php
// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

class NewAction extends CommonAction {

    function top()
    {
        //置顶指定记录
        $Blog        = D("New");
        $id         = $_REQUEST['id'];
        if(isset($id)) {
            $condition = array('id'=>array('in',$id));
            if($Blog->top($condition)){
				$this->assign("jumpUrl",$this->getReturnUrl());
				$this->success('置顶成功！');
            }else {
                $this->error('置顶失败');
            }
        }else {
        	$this->error('非法操作');
        }
    }
}
?>