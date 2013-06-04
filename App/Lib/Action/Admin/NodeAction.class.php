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

// 节点模块
class NodeAction extends CommonAction {
	public function _filter(&$map)
	{
        if(!empty($_GET['group_id'])) {
            $map['group_id'] =  $_GET['group_id'];
            $this->assign('nodeName','分组');
        }elseif(empty($_POST['search']) && !isset($map['pid']) ) {
			$map['pid']	=	0;
		}
		$_SESSION['currentNodeId']	=	$map['pid'];
		//获取上级节点
		$node  = D("Node");
        if(isset($map['pid'])) {
            if($node->getById($map['pid'])) {
                $this->assign('level',$node->level+1);
                $this->assign('nodeName',$node->name);
            }else {
                $this->assign('level',1);
            }
        }
	}

	public function _before_index() {
		$model	=	M("Group");
		$list	=	$model->getField('id,title','status=1');
		$this->assign('groupList',$list);
	}

    public function fleshMenu() {
        unset($_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]]);
        $this->success('刷新菜单数据完成');
    }

	// 获取配置类型
	public function _before_add() {
		$model	=	D("Group");
		$list	=	$model->where('status=1')->select();
		$this->assign('list',$list);
		$node	=	D("Node");
		$node->getById($_SESSION['currentNodeId']);
        $this->assign('pid',$node->id);
		$this->assign('level',$node->level+1);
	}

    public function _before_patch() {
		$model	=	D("Group");
		$list	=	$model->where('status=1')->select();
		$this->assign('list',$list);
		$node	=	D("Node");
		$node->getById($_SESSION['currentNodeId']);
        $this->assign('pid',$node->id);
		$this->assign('level',$node->level+1);
    }
	public function _before_edit() {
		$model	=	D("Group");
		$list	=	$model->where('status=1')->select();
		$this->assign('list',$list);
	}
    // 批量增加节点
    public function patchAdd() {
        $Node   =  D("Node");
        $count   =  count($_POST['name']);
        for($i=0;$i<$count;$i++) {
            if(!empty($_POST['name'][$i])) {
                $data['name'] =  $_POST['name'][$i];
                $data['title']    =  $_POST['title'][$i];
                $data['remark']   =  $_POST['remark'][$i];
                $data['status'] = $_POST['status'][$i];
                $data['group_id']     = $_POST['group_id'][$i];
                $data['level']   =  $_POST['level'];
                $data['pid']     =  $_POST['pid'];
                if($Node->create($data)) {
                    $result   =  $Node->add();
                    if(!$result) {
                        $this->error('添加失败！');
                    }
                }else{
                    $this->error($Node->getError());
                }
            }
        }
        $this->success('批量添加成功！');
    }
    /**
     +----------------------------------------------------------
     * 默认排序操作
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    public function sort()
    {
		$node = D('Node');
        if(!empty($_GET['sortId'])) {
            $map = array();
            $map['status'] = 1;
            $map['id']   = array('in',$_GET['sortId']);
            $sortList   =   $node->where($map)->order('sort asc')->select();
        }else{
            if(!empty($_GET['pid'])) {
                $pid  = $_GET['pid'];
            }else {
                $pid  = $_SESSION['currentNodeId'];
            }
            if($node->getById($pid)) {
                $level   =  $node->level+1;
            }else {
                $level   =  1;
            }
            $this->assign('level',$level);
            $sortList   =   $node->findAll(array(
                'where'=>'status=1 and pid='.$pid.' and level='.$level,
                'order'=>'sort asc')
                );
        }
        $this->assign("sortList",$sortList);
        $this->display();
        return ;
    }
}
?>