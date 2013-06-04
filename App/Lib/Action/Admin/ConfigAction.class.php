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

// 配置模块
class ConfigAction extends CommonAction {


	// 批量修改配置参数
    public function saveConfig()
    {
        $Config = M("Config");
    	foreach($_POST as $key=>$val) {
            $config    = Array();
            $config['value']  =  $val;
            $where =  "name='".$key."'";
    		$Config->where($where)->save($config);
    	}
		$list			=	$Config->getField('name,value');
		$savefile		=	DATA_PATH.'~config.php';
		// 所有配置参数统一为大写
		$content		=   "<?php\nreturn ".var_export(array_change_key_case($list,CASE_UPPER),true).";\n?>";
        $this->success('配置修改成功！');
    }

    public function sort()
    {
		$config = D('Config');
        $map = array();
        if(!empty($_GET['sortId'])) {
            $map['id']   = array('in',$_GET['sortId']);
        }
        $sortList   =   $config->where($map)->order('sort asc')->select();
        $this->assign("sortList",$sortList);
        $this->display();
        return ;
    }

	// 缓存配置文件
	public function cache($name='',$field='') {
		$config		=	M("Config");
		$list			=	$config->getField('name,value');
		$savefile		=	DATA_PATH.'~config.php';
		// 所有配置参数统一为大写
		$content		=   "<?php\nreturn ".var_export(array_change_key_case($list,CASE_UPPER),true).";\n?>";
		if(file_put_contents($savefile,$content)){
			$this->success('配置缓存生成成功！');
		}else{
			$this->error('配置缓存失败！');
		}
	}
}
?>