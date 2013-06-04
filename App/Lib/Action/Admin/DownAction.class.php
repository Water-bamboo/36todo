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

class DownAction extends CommonAction {

	public function insert() {
		$Article	=	D("Down");
		if($Article->create()) {
			$result	=	$Article->add();
			$this->success('保存成功！');
		}else{
			$this->error($Article->getError());
		}
	}

    public function read() {
        $map['module']   =  'Down';
        $map['record_id'] =  (int)$_GET['id'];
        $this->_list(D('Attach'),$map,'id',false);
        $this->display();
    }

    public function editAttach() {
		$model	=	D('Attach');
		$id     = $_REQUEST['id'];
		$vo	=	$model->getById($id);
		$this->assign('vo',$vo);
		$this->display();
    }

    public function updateAttach() {
		$model	=	D('Attach');
        if(false === $model->create()) {
        	$this->error($model->getError());
        }
		// 更新数据
		if($model->save()) {
            //成功提示
            $this->assign('jumpUrl',Cookie::get('_currentUrl_'));
            $this->success(L('_UPDATE_SUCCESS_'));
        }else {
            //错误提示
            $this->error(L('_UPDATE_FAIL_'));
        }
    }

    protected function _upload_init($upload) {
        $upload->maxSize  = C('UPLOAD_MAX_SIZE') ;
        $upload->allowExts  = explode(',',strtolower(C('TOPIC_UPLOAD_FILE_EXT')));
        $upload->savePath =  './Public/Uploads/';
        return $upload;
    }

    /**
     +----------------------------------------------------------
     * 文件上传功能，支持多文件上传、保存数据库、自动缩略图
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param string $module 附件保存的模块名称
     * @param integer $id 附件保存的模块记录号
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    protected function _upload($module='',$recordId='')
    {
        import("ORG.Net.UploadFile");
        $upload = new UploadFile();
       // 自定义上传规则
        $upload  = $this->_upload_init($upload);
        $uploadReplace =  false;
		$uploadFileVersion = false;
        $uploadRecord  =  true;
        // 记录上传成功ID
        $uploadId =  array();
        $savename = array();
        //执行上传操作
        if(!$upload->upload()) {
            if($this->isAjax() && isset($_POST['_uploadFileResult'])) {
                $uploadSuccess =  false;
                $ajaxMsg  =  $upload->getErrorMsg();
            }else {
                //捕获上传异常
                $this->error($upload->getErrorMsg());
            }
        }else {
			 //取得成功上传的文件信息
			$uploadList = $upload->getUploadFileInfo();
            $remark	 =	 $_POST['remark'];
			//保存附件信息到数据库
			if($uploadRecord) {
				$Attach    = D('Attach');
				//启动事务
				$Attach->startTrans();
			}
            if(!empty($_POST['_uploadFileTable'])) {
                //设置附件关联数据表
                $module =  $_POST['_uploadFileTable'];
            }
            if(!empty($_POST['_uploadRecordId'])) {
                //设置附件关联记录ID
                $recordId =  $_POST['_uploadRecordId'];
            }
            if(!empty($_POST['_uploadFileVerify'])) {
                //设置附件验证码
                $verify =  $_POST['_uploadFileVerify'];
            }
            if(!empty($_POST['_uploadUserId'])) {
                //设置附件上传用户ID
                $userId =  $_POST['_uploadUserId'];
            }else {
                $userId = isset($_SESSION[C('USER_AUTH_KEY')])?$_SESSION[C('USER_AUTH_KEY')]:0;
            }
			foreach($uploadList as $key=>$file) {
				$savename[] =  $file['savename'];
				if($uploadRecord) {
					// 附件数据需要保存到数据库
					//记录模块信息
                    unset($file['key']);
					$file['module']		=   $module;
					$file['record_id']	=   $recordId?$recordId:0;
					$file['user_id']		=   $userId;
					$file['verify']			=	$verify?$verify:'0';
					$file['remark']		=	 $remark[$key]?$remark[$key]:($remark?$remark:'');
					$file['status']		=	1;
					$file['create_time'] =   time();
                    if(empty($file['hash'])) {
                        unset($file['hash']);
                    }
                    //保存附件信息到数据库
                    $uploadId[] =  $Attach->add($file);
				}
			}
			if($uploadRecord) {
				//提交事务
				$Attach->commit();
			}
            // 更新下载的数量和最后更新时间
            $Down   =  D("Down");
            $Down->id  = $recordId;
            $Down->count = array('exp','count+'.count($uploadId));
            $Down->update_time   =  time();
            $Down->save();
            //$Down->where('id='.$recordId)->setInc('count','',);
            $uploadSuccess =  true;
            $ajaxMsg  =  '';
        }

        // 判断是否有Ajax方式上传附件
        // 并且设置了结果显示Html元素
        if($this->isAjax() && isset($_POST['_uploadFileResult']) ) {
            // Ajax方式上传参数信息
            $info = Array();
            $info['success']  =  $uploadSuccess;
            $info['message']   = $ajaxMsg;
            //设置Ajax上传返回元素Id
            $info['uploadResult'] =  $_POST['_uploadFileResult'];
            if(isset($_POST['_uploadFormId'])) {
                //设置Ajax上传表单Id
                $info['uploadFormId'] =  $_POST['_uploadFormId'];
            }
            if(isset($_POST['_uploadResponse'])) {
                //设置Ajax上传响应方法名称
                $info['uploadResponse'] =  $_POST['_uploadResponse'];
            }
            if(!empty($uploadId)) {
                $info['uploadId'] = implode(',',$uploadId);
            }
            $info['savename']   = implode(',',$savename);
            $this->ajaxUploadResult($info);
        }
        return ;
    }

    /**
     +----------------------------------------------------------
     * 默认删除附件操作
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function delAttach()
    {
        //删除指定记录
        $attach        = M("Attach");
        $id         = $_POST['id'];
	    $condition = array('id'=>array('in',$id));
        if($attach->where($condition)->delete()){
            // 更新下载数量
            $Down   =  D("Down");
            $count   =  count(explode(',',$id));
            $Down->where('id='.$_POST['record_id'])->setDec('count');
            $this->success(L('_DELETE_SUCCESS_'));
        }else {
            $this->error(L('_DELETE_FAIL_'));
        }
    }

    public function sort()
    {
		$node = M('Down');
        if(!empty($_GET['sortId'])) {
            $map = array();
            $map['status'] = 1;
            $map['id']   = array('in',$_GET['sortId']);
            $sortList   =   $node->where($map)->order('sort asc')->select();
        }else{
            $sortList   =   $node->where('status=1')->order('sort asc')->select();
        }
        $this->assign("sortList",$sortList);
        $this->display();
        return ;
    }

    public function sortDown()
    {
		$node = M('Attach');
        if(!empty($_GET['sortId'])) {
            $map = array();
            $map['status'] = 1;
            $map['id']   = array('in',$_GET['sortId']);
            $sortList   =   $node->where($map)->order('sort asc')->select();
        }else{
            $id = intval($_GET['id']);
            $sortList   =   $node->where('status=1 and module="Down" and record_id='.$id)->order('sort asc')->select();
        }
        $this->assign("sortList",$sortList);
        $this->display();
        return ;
    }

    public function saveDownSort()
    {
        $seqNoList  =   $_POST['seqNoList'];
        if(!empty($seqNoList)) {
            //更新数据对象
            $model    = M('Attach');
            $col    =   explode(',',$seqNoList);
            //启动事务
            $model->startTrans();
            foreach($col as $val) {
                $val    =   explode(':',$val);
                $model->id	=	$val[0];
                $model->sort	=	$val[1];
                $result =   $model->save();
                if(!$result) {
                    break;
                }
            }
            //提交事务
            $model->commit();
            if($result) {
                //采用普通方式跳转刷新页面
                $this->success('更新成功');
            }else {
                $this->error($model->getError());
            }
        }
    }

    function top()
    {
        //置顶指定记录
        $Attach        = D("Attach");
        $id         = intval($_REQUEST['id']);
        if(isset($id)) {
            $condition = array('id'=>array('in',$id));
            if($Attach->where($condition)->setField('is_top',array('exp','1-is_top'))){
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