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

class CaseAction extends CommonAction {
    public function _before_insert() {
        $this->upload();
    }
    public function _before_update() {
        $this->upload();
    }

	public function _tigger_insert($model) {
        $this->saveTag($model->tags,$model->id);
	}

	public function _tigger_update($model) {
        $this->saveTag($model->tags,$model->id);
	}

    public function upload() {
        if(!empty($_FILES['pic']['name'])) {
            import("ORG.Net.UploadFile");
            $upload = new UploadFile();
            //设置上传文件大小
            $upload->maxSize  = C('UPLOAD_MAX_SIZE') ;
            //设置上传文件类型
            $upload->allowExts  = explode(',','jpg,gif,png,jpeg');
            //设置附件上传目录
            $upload->savePath =  './Public/Uploads/Case/';
            $upload->thumb  =  true;
            $upload->thumbMaxWidth =  200;
            $upload->thumbMaxHeight = 124;
            $upload->thumbPrefix   =  '';
            if(!$upload->upload()) {
                $this->error($upload->getErrorMsg());
            }else{
                $info =  $upload->getUploadFileInfo();
                $_POST['pic'] = $info[0]['savename'];
            }
        }
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
		$node = D('Case');
        if(!empty($_GET['sortId'])) {
            $map = array();
            $map['status'] = 1;
            $map['id']   = array('in',$_GET['sortId']);
            $sortList   =   $node->where($map)->order('sort asc')->select();
        }else{
            $sortList   =   $node->findAll(array(
                'condition'=>'status=1',
                'order'=>'sort asc')
                );
        }
        $this->assign("sortList",$sortList);
        $this->display();
        return ;
    }

	// 缓存文件
	public function cache($name='',$fields='') {
		$name	=	$name?	$name	:	$this->getActionName();
		$Model	=	M($name);
		$list		=	$Model->limit(3)->order('id desc')->select();
		$data		=	array();
		foreach ($list as $key=>$val){
    		$data[$val[$Model->getPk()]]	=	$val;
		}
		$savefile		=	$this->getCacheFilename($name);
		// 所有参数统一为大写
		$content		=   "<?php\nreturn ".var_export(array_change_key_case($data,CASE_UPPER),true).";\n?>";
		if(file_put_contents($savefile,$content)){
			$this->success('缓存生成成功！');
		}else{
			$this->error('缓存失败！');
		}
	}
}
?>