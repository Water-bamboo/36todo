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

class MenuAction extends CommonAction {

	public function _filter(&$map)
	{
		if(!isset($map['pid']) ) {
			$map['pid']	=	0;
		}
		$_SESSION['currentMenuId']	=	$map['pid'];
		//获取上级节点
		$Menu  = D("Menu");
		if($Menu->getById($map['pid'])) {
			$this->assign('level',$Menu->level+1);
			$this->assign('columnName',$Menu->name);
		}else {
			$this->assign('level',1);
		}
	}

	public function add()
	{
		$Menu	=	D("Menu");
		$Menu->getById($_SESSION['currentMenuId']);
        $this->assign('pid',$Menu->id);
		$this->assign('level',$Menu->level+1);
		$this->display();
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
		$node = M('Menu');
        if(!empty($_GET['sortId'])) {
            $map = array();
            $map['status'] = 1;
            $map['id']   = array('in',$_GET['sortId']);
            $sortList   =   $node->where($map)->order('sort asc')->select();
        }else{
            if(!empty($_GET['pid'])) {
                $pid  = $_GET['pid'];
            }else {
                $pid  = $_SESSION['currentMenuId'];
            }
            if($node->getById($pid)) {
                $level   =  $node->level+1;
            }else {
                $level   =  1;
            }
            $this->assign('level',$level);
            $sortList   =   $node->where('status=1 and pid='.$pid.' and level='.$level)->order('sort asc')->select();
        }
        $this->assign("sortList",$sortList);
        $this->display();
        return ;
    }

	// 缓存配置文件
	public function cache() {
		$Menu		=	M("Menu");
		$list			=	$Menu->field('name,title,link,target')->where('status=1 and level=1')->order('sort')->select();
		$savefile		=	DATA_PATH.'~menu.php';
		// 所有配置参数统一为大写
		$content		=   "<?php\nreturn ".var_export($list,true).";\n?>";
		if(file_put_contents($savefile,$content)){
			$this->success('缓存生成成功！');
		}else{
			$this->error('缓存失败！');
		}
	}
}
?>