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

class CommonAction extends Action {

    public function _initialize()
    {
        // 用户权限检查
        if(C('USER_AUTH_ON') && !in_array(MODULE_NAME,explode(',',C('NOT_AUTH_MODULE')))) {
            import('ORG.Util.RBAC');
            if(!RBAC::AccessDecision(GROUP_NAME)) {
                //检查认证识别号
                if(!$_SESSION[C('USER_AUTH_KEY')]) {
                    //跳转到认证网关
                    redirect(PHP_FILE.C('USER_AUTH_GATEWAY'));
                }
                // 没有权限 抛出错误
                if(C('RBAC_ERROR_PAGE')) {
                    // 定义权限错误页面
                    redirect(C('RBAC_ERROR_PAGE'));
                }else{
                    if(C('GUEST_AUTH_ON')){
                        $this->assign('jumpUrl',PHP_FILE.C('USER_AUTH_GATEWAY'));
                    }
                    // 提示错误信息
                    $this->error(L('_VALID_ACCESS_'));
                }
            }
        }
		// 读取系统配置参数
        if(!file_exists(DATA_PATH.'~config.php')) {
            $config		=	M("Config");
            $list			=	$config->getField('name,value');
            $savefile		=	DATA_PATH.'~config.php';
            // 所有配置参数统一为大写
            $content		=   "<?php\nreturn ".var_export(array_change_key_case($list,CASE_UPPER),true).";\n?>";
            if(!file_put_contents($savefile,$content)){
                $this->error('配置缓存失败！');
            }
        }
		C(include_once DATA_PATH.'~config.php');
    }

	// 缓存文件
	public function cache($name='',$fields='') {
		$name	=	$name?	$name	:	$this->getActionName();
		$Model	=	M($name);
		$list		=	$Model->select();
		$data		=	array();
		foreach ($list as $key=>$val){
			if(empty($fields)) {
				$data[$val[$Model->getPk()]]	=	$val;
			}else{
				// 获取需要的字段
				if(is_string($fields)) {
					$fields	=	explode(',',$fields);
				}
				if(count($fields)==1) {
					$data[$val[$Model->getPk()]]	 =	 $val[$fields[0]];
				}else{
					foreach ($fields as $field){
						$data[$val[$Model->getPk()]][]	=	$val[$field];
					}
				}
			}
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

	protected function getCacheFilename($name='') {
		$name	=	$name?	$name	:	$this->getActionName();
		return	 DATA_PATH.'~'.strtolower($name).'.php';
	}

	public function index()
    {
        //列表过滤器，生成查询Map对象
        $map = $this->_search();
        if(method_exists($this,'_filter')) {
            $this->_filter($map);
        }
		$model        = M($this->getActionName());
        if(!empty($model)) {
        	$this->_list($model,$map);
        }
		$this->display();
        return;
    }

    /**
     +----------------------------------------------------------
     * 验证码显示
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function verify()
    {
        import("ORG.Util.Image");
       	Image::buildImageVerify();
    }

    /**
     +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param string $name 数据对象名称
     +----------------------------------------------------------
     * @return HashMap
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    protected function _search($name='')
    {
        //生成查询条件
		if(empty($name)) {
			$name	=	$this->getActionName();
		}
		$model	=	M($name);
		$map	=	array();
        foreach($model->getDbFields() as $key=>$val) {
            if(substr($key,0,1)=='_') continue;
            if(isset($_REQUEST[$val]) && $_REQUEST[$val]!='') {
                $map[$val]	=	$_REQUEST[$val];
            }
        }
        return $map;
    }

    /**
     +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param Model $model 数据对象
     * @param HashMap $map 过滤条件
     * @param string $sortBy 排序
     * @param boolean $asc 是否正序
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    protected function _list($model,$map=array(),$sortBy='',$asc=false)
    {
        //排序字段 默认为主键名
        if(isset($_REQUEST['_order'])) {
            $order = $_REQUEST['_order'];
        }else {
            $order = !empty($sortBy)? $sortBy: $model->getPk();
        }
        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        if(isset($_REQUEST['_sort'])) {
            $sort = $_REQUEST['_sort']?'asc':'desc';
        }else {
            $sort = $asc?'asc':'desc';
        }
        //取得满足条件的记录数
        $count      = $model->where($map)->count('id');
        import("ORG.Util.Page");
        //创建分页对象
        if(!empty($_REQUEST['listRows'])) {
            $listRows  =  $_REQUEST['listRows'];
        }else {
            $listRows  =  '';
        }
        $p          = new Page($count,$listRows);
        //分页查询数据
        $list     = $model->where($map)->order($order.' '.$sort)->limit($p->firstRow.','.$p->listRows)->select();
        //分页跳转的时候保证查询条件
        foreach($map as $key=>$val) {
            if(!is_array($val)) {
            $p->parameter   .=   "$key=".urlencode($val)."&";
            }
        }

        //分页显示
        $page       = $p->show();
        //列表排序显示
        $sortImg    = $sort ;                                   //排序图标
        $sortAlt    = $sort == 'desc'?'升序排列':'倒序排列';    //排序提示
        $sort       = $sort == 'desc'? 1:0;                     //排序方式
        //模板赋值显示
        $this->assign('list',       $list);
        $this->assign('sort',       $sort);
        $this->assign('order',      $order);
        $this->assign('sortImg',    $sortImg);
        $this->assign('sortType',   $sortAlt);
        $this->assign("page",       $page);
        Cookie::set('_currentUrl_',__SELF__);
        return ;
    }

    function insert()
    {
		$model	=	D($this->getActionName());
        if(false === $model->create()) {
        	$this->error($model->getError());
        }
        //保存当前数据对象
        if($result = $model->add()) { //保存成功
            // 回调接口
            if(method_exists($this,'_tigger_insert')) {
                $model->id =  $result;
                $this->_tigger_insert($model);
            }
            //成功提示
            $this->assign('jumpUrl',Cookie::get('_currentUrl_'));
            $this->success(L('新增成功'));
        }else {
            //失败提示
            $this->error(L('新增失败'));
        }
    }

	public function add() {
		$this->display();
	}

	function read() {
		$this->edit();
	}

	function edit() {
		$model	=	M($this->getActionName());
		$id     = $_REQUEST[$model->getPk()];
		$vo	=	$model->find($id);
		$this->assign('vo',$vo);
		$this->display();
	}

	function update() {
		$model	=	D($this->getActionName());
        if(false === $model->create()) {
        	$this->error($model->getError());
        }
		// 更新数据
		if(false !== $model->save()) {
            // 回调接口
            if(method_exists($this,'_tigger_update')) {
                $this->_tigger_update($model);
            }
            //成功提示
            $this->assign('jumpUrl',Cookie::get('_currentUrl_'));
            $this->success(L('更新成功'));
        }else {
            //错误提示
            $this->error(L('更新失败'));
        }
	}

    /**
     +----------------------------------------------------------
     * 默认列表选择操作
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    protected function select($fields='id,name',$title='')
    {
        $map = $this->_search();
        //创建数据对象
        $Model = M($this->getActionName());
        //查找满足条件的列表数据
        $list     = $Model->where($map)->getField($fields);
		$this->assign('selectName',$title);
        $this->assign('list',$list);
        $this->display();
        return;
    }
    public function delete()
    {
        //删除指定记录
        $model        = M($this->getActionName());
        if(!empty($model)) {
			$pk	=	$model->getPk();
            $id         = $_REQUEST[$pk];
            if(isset($id)) {
                $condition = array($pk=>array('in',explode(',',$id)));
                if(false !== $model->where($condition)->delete()){
                    $this->success(L('删除成功'));
                }else {
                    $this->error(L('删除失败'));
                }
            }else {
                $this->error('非法操作');
            }
        }
    }

    // 通过审核
    public function pass() {
        //删除指定记录
        $model        = D($this->getActionName());
        if(!empty($model)) {
			$pk	=	$model->getPk();
            if(isset($_REQUEST[$pk])) {
                $id         = $_REQUEST[$pk];
                $condition = array($pk=>array('in',explode(',',$id)));
                if(false !== $model->where($condition)->setField('status',1)){
                    $this->assign("jumpUrl",$this->getReturnUrl());
                    $this->success('审核通过！');
                }else {
                    $this->error('审核失败！');
                }
            }else {
                $this->error('非法操作');
            }
        }
    }

    /**
     +----------------------------------------------------------
     * 默认禁用操作
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    public function forbid()
    {
		$model	=	D($this->getActionName());
		$pk	=	$model->getPk();
        $id         = $_GET[$pk];
        $condition = array($pk=>array('in',$id));
        if($model->forbid($condition)){
            $this->assign("jumpUrl",$this->getReturnUrl());
            $this->success('状态禁用成功！');
        }else {
            $this->error('状态禁用失败！');
        }
    }

    public function recycle()
    {
		$model	=	D($this->getActionName());
		$pk	=	$model->getPk();
        $id         = $_GET[$pk];
        $condition = array($pk=>array('in',$id));
        if($model->recycle($condition)){
            $this->assign("jumpUrl",$this->getReturnUrl());
            $this->success('状态还原成功！');
        }else {
            $this->error('状态还原失败！');
        }
    }

    public function recycleBin() {
        $map = $this->_search();
        $map['status'] = -1;
		$model        = D($this->getActionName());
        if(!empty($model)) {
        	$this->_list($model,$map);
        }
		$this->display();
    }

    // 检查是否是当前作者
    protected function checkAuthor($name='') {
        if($_SESSION[C('USER_AUTH_KEY')]!=1) {
            $id = $_GET['id'];
            $name   = empty($name)?$this->getActionName():$name;
            $Model  =  M($name);
            $Model->find((int)$id);
            if($Model->member_id != $_SESSION[C('USER_AUTH_KEY')]) {
                $this->error('没有权限！');
            }
        }
    }

    /**
     +----------------------------------------------------------
     * 默认恢复操作
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    function resume()
    {
        //恢复指定记录
		$model	=	D($this->getActionName());
		$pk	=	$model->getPk();
        $id         = $_GET[$pk];
        $condition = array($pk=>array('in',$id));
        if($model->resume($condition)){
            $this->assign("jumpUrl",$this->getReturnUrl());
            $this->success('状态恢复成功！');
        }else {
            $this->error('状态恢复失败！');
        }
    }

    function recommend()
    {
		$model	=	D($this->getActionName());
		$pk	=	$model->getPk();
        $id         = $_GET[$pk];
        $condition = array($pk=>array('in',$id));
        if($model->recommend($condition)){
            $this->assign("jumpUrl",$this->getReturnUrl());
            $this->success('推荐成功！');
        }else {
            $this->error('推荐失败！');
        }
    }

    function unrecommend()
    {
		$model	=	D($this->getActionName());
		$pk	=	$model->getPk();
        $id         = $_GET[$pk];
        $condition = array($pk=>array('in',$id));
        if($model->unrecommend($condition)){
            $this->assign("jumpUrl",$this->getReturnUrl());
            $this->success('取消推荐成功！');
        }else {
            $this->error('取消推荐失败！');
        }
    }

    /**
     +----------------------------------------------------------
     * 取得操作成功后要返回的URL地址
     * 默认返回当前模块的默认操作
     * 可以在action控制器中重载
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    function getReturnUrl()
    {
        return __URL__.'?'.C('VAR_MODULE').'='.MODULE_NAME.'&'.C('VAR_ACTION').'='.C('DEFAULT_ACTION');
    }

    /**
     +----------------------------------------------------------
     * 默认上传操作
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
	public function upload() {
		if(!empty($_FILES)) {//如果有文件上传
			// 上传附件并保存信息到数据库
			$this->_upload(MODULE_NAME);
			//$this->forward();
		}
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
				$Attach    = M('Attach');
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
            if(!empty($_POST['_uploadFileId'])) {
                //设置附件记录ID
                $id =  $_POST['_uploadFileId'];
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
				$savename[] =  $file['savepath'].$file['savename'];
                $sourcename[]    = $file['name'];
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
					if($upload->uploadReplace ) {
						if(!empty($id)) {
							$vo  =  $Attach->getById($id);
						}else{
							$vo  =  $Attach->where("module='".$module."' and record_id=".$recordId)->find();
						}
						if($vo) {
							// 如果附件为覆盖方式 且已经存在记录，则进行替换
							$id	=	$vo[$Attach->getPk()];
							if($uploadFileVersion) {
								// 记录版本号
								$file['version']	 =	 $vo['version']+1;
								// 备份旧版本文件
								$oldfile	=	$vo['savepath'].$vo['savename'];
								if(is_file($oldfile)) {
									if(!is_dir(dirname($oldfile).'/_version/')) {
										mkdir(dirname($oldfile).'/_version/');
									}
									$bakfile	=	dirname($oldfile).'/_version/'.$id.'_'.$vo['version'].'_'.$vo['savename'];
									$result = rename($oldfile,$bakfile);
								}
							}
							// 覆盖模式
							$Attach->where("id=".$id)->save($file);
							$uploadId[]   = $id;

						}else {
							$uploadId[] = $Attach->add($file);
						}
					}else {
						//保存附件信息到数据库
						$uploadId[] =  $Attach->add($file);
					}
				}
			}
			if($uploadRecord) {
				//提交事务
				$Attach->commit();
			}
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
            $info['name']   = implode(',',$sourcename);
            $this->ajaxUploadResult($info);
        }
        return ;
    }

    /**
     +----------------------------------------------------------
     * Ajax上传页面返回信息
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param array $info 附件信息
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    protected function ajaxUploadResult($info)
    {
        // Ajax方式附件上传提示信息设置
        // 默认使用mootools opacity效果
        $show   = '<script language="JavaScript" src="'.WEB_PUBLIC_PATH.'/Js/mootools.js"></script><script language="JavaScript" type="text/javascript">'."\n";
        $show  .= ' var parDoc = window.parent.document;';
        $show  .= ' var result = parDoc.getElementById("'.$info['uploadResult'].'");';
        if(isset($info['uploadFormId'])) {
   	        $show  .= ' parDoc.getElementById("'.$info['uploadFormId'].'").reset();';
        }
        $show  .= ' result.style.display = "block";';
        $show .= " var myFx = new Fx.Style(result, 'opacity',{duration:600}).custom(0.1,1);";
        if($info['success']) {
            // 提示上传成功
            $show .=  'result.innerHTML = "<div style=\"color:#3333FF\"><IMG SRC=\"'.APP_PUBLIC_PATH.'/images/ok.gif\" align=\"absmiddle\" BORDER=\"0\"> 文件上传成功！</div>";';
            // 如果定义了成功响应方法，执行客户端方法
            // 参数为上传的附件id，多个以逗号分割
            if(isset($info['uploadResponse'])) {
                $show  .= 'window.parent.'.$info['uploadResponse'].'("'.$info['uploadId'].'","'.$info['name'].'","'.$info['savename'].'");';
            }
        }else {
            // 上传失败
            // 提示上传失败
            $show .=  'result.innerHTML = "<div style=\"color:#FF0000\"><IMG SRC=\"'.APP_PUBLIC_PATH.'/images/update.gif\" align=\"absmiddle\" BORDER=\"0\"> 上传失败：'.$info['message'].'</div>";';
        }
        $show .= "\n".'</script>';
        //$this->assign('_ajax_upload_',$show);
        header("Content-Type:text/html; charset=utf-8");
        exit($show);
        return ;
   	}

      /**
     +----------------------------------------------------------
     * 下载附件
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    public function download()
    {
        $id         =   $_GET['id'];
        $Attach        =   M("Attach");
        if($Attach->getById($id)) {
            $filename   =   $Attach->savepath.$Attach->savename;
            if(is_file($filename)) {
                $showname = auto_charset($Attach->name,'utf-8','gbk');
				$Attach->where('id='.$id)->setInc('download_count');
		        import("ORG.Net.Http");
                Http::download($filename,$showname);
            }
        }
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
		$pk	=	$attach->getPk();
        $id         = $_REQUEST[$pk];
	    $condition = array($pk=>array('in',$id));
        if($attach->where($condition)->delete()){
            $this->ajaxReturn($id,L('_DELETE_SUCCESS_'),1);
        }else {
            $this->error(L('_DELETE_FAIL_'));
        }
    }


    function saveTag($tags,$id,$module=MODULE_NAME)
    {
        if(!empty($tags) && !empty($id)) {
            $Tag = M("Tag");
            $Tagged   = M("Tagged");
            // 记录已经存在的标签
            $exists_tags  = $Tagged->where("module='{$module}' and record_id={$id}")->getField("id,tag_id");
            $Tagged->where("module='{$module}' and record_id={$id}")->delete();
            $tags = explode(' ',$tags);
            foreach($tags as $key=>$val) {
                $val  = trim($val);
                if(!empty($val)) {
                    $tag =  $Tag->where("module='{$module}' and name='{$val}'")->find();
                    if($tag) {
                        // 标签已经存在
                        if(!in_array($tag['id'],$exists_tags)) {
							$Tag->where('id='.$tag['id'])->setInc('count');
                        }
                    }else {
                        // 不存在则添加
						$tag = array();
                        $tag['name'] =  $val;
                        $tag['count']  =  1;
                        $tag['module']   =  $module;
                        $result  = $Tag->add($tag);
                        $tag['id']   =  $result;
                    }
                    // 记录tag关联信息
                    $t = array();
                    $t['user_id'] = Session::get(C('USER_AUTH_KEY'));
                    $t['module']   = $module;
                    $t['record_id'] =  $id;
                    $t['create_time']  = time();
                    $t['tag_id']  = $tag['id'];
                    $Tagged->add($t);
                }
            }
        }
    }

    /**
     +----------------------------------------------------------
     * 生成树型列表XML文件
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
	public function tree() {
		$Model	=	M($this->getActionName());
		$title		=	$_REQUEST['title']?		$_REQUEST['title']		:'选择';
		$caption	=	$_REQUEST['caption']?	$_REQUEST['caption']	:'name';
		$list   =  $Model->where('status=1')->order('sort')->findAll();
		$tree		=	toTree($list);
		header("Content-Type:text/xml; charset=utf-8");
		$xml	 =  '<?xml version="1.0" encoding="utf-8" ?>'."\n";
		$xml	.=  '<tree caption="'.$title.'" >'."\n";
		$xml  .= $this->_toTree($tree,$caption);
		$xml	.= '</tree>';
		exit($xml);
	}

    /**
     +----------------------------------------------------------
     * 把树型列表数据转换为XML节点
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
	private function _toTree($list,$caption) {
		foreach ($list as $key=>$val){
			$tab	=	str_repeat("\t",$val['level']);
			if(isset($val['_child'])) {
				// 有子节点
				$xml	.= $tab.'<level'.$val['level'].' id="'.$val['id'].'" level="'.$val['level'].'" parentId="'.$val['pid'].'" caption="'.$val[$caption].'" >'."\n";
				$xml  .= $this->_toTree($val['_child'],$caption);
				$xml  .= $tab.'</level'.$val['level'].'>'."\n";
			}else{
				$xml	.= $tab.'<level'.$val['level'].' id="'.$val['id'].'" level="'.$val['level'].'" parentId="'.$val['pid'].'" caption="'.$val[$caption].'" />'."\n";
			}
		}
		return $xml;
	}

    public function saveSort()
    {
        $seqNoList  =   $_POST['seqNoList'];
        if(!empty($seqNoList)) {
            //更新数据对象
            $model    = M($this->getActionName());
            $col    =   explode(',',$seqNoList);
            //启动事务
            $model->startTrans();
            foreach($col as $val) {
                $val    =   explode(':',$val);
                $model->id	=	$val[0];
                $model->sort	=	$val[1];
                $result =   $model->save();
                if(false === $result) {
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

	protected function getAttach($module='') {
        $module = empty($module)?$this->getActionName():$module;
        //读取附件信息
		$id	=	$_REQUEST['id'];
        $Attach = M('Attach');
        $attachs = $Attach->where("module='".$module."' and record_id=$id")->select();
		//模板变量赋值
		$this->assign("attachs",$attachs);
	}

    // 查看某个模块的标签相关的记录
    public function tag()
    {
        $Tag = M("Tag");
        $name=trim($_GET['tag']);
        $Stat  = $Tag->where('module="'.$this->getActionName().'" and name="'.$name.'"')->field("id,count")->find();
        $tagId  =  $Stat['id'];
        $count   =  $Stat['count'];
       // import("Think.Util.Page");
        $p          = new Page($count);
        $Model = M($this->getActionName());
        $Tagged = M("Tagged");
        $recordIds  =  $Tagged->where("module='".$this->getActionName()."' and tag_id=".$tagId)->getField('id,record_id');
        if($recordIds) {
            $map['id']   = array('IN',$recordIds);
            $this->_list($Model,$map);
            $this->display('index');
        }else{
            $this->error('标签没有对应的文章！');
        }

    }

}
?>