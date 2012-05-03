<?php
// 后台用户模块
class AdminAction extends CommonAction {
	public function index() {
        //列表过滤器，生成查询Map对象
        $map = $this->_search ();
        
        if (method_exists ( $this, '_filter' )) {
            $this->_filter ( $map );
        }
        $name=$this->getActionName();
        $model = D ($name);
        if (! empty ( $model )) {
            $this->_list ( $model, $map );
        }
        $this->display ();
        return;
    }
	function _filter(&$map){
		$map['id'] = array('egt',2);
		$map['account'] = array('like',"%".$_POST['account']."%");
	}
	// 检查帐号
	public function checkAccount() {
        if(!preg_match('/^[a-z]\w{4,}$/i',$_POST['account'])) {
            $this->error( '用户名必须是字母，且5位以上！');
        }
		$User = M("User");
        // 检测用户名是否冲突
        $name  =  $_REQUEST['account'];
        $result  =  $User->getByAccount($name);
        if($result) {
        	$this->error('该用户名已经存在！');
        }else {
           	$this->success('该用户名可以使用！');
        }
    }
    public function add(){
        $role_model = M("role");
        $role_list = $role_model->order('id desc')->getField("id,name");
        //print_r($role_list)
        $this -> assign('role_list',$role_list);
        $this ->display();
    }
	// 插入数据
	public function insert() {
		// 创建数据对象
		$User	 =	 D("User");
		if(!$User->create()) {
			$this->error($User->getError());
		}else{
			// 写入帐号数据
			if($result	 =	 $User->add()) {
				$this->addRole($result);
				$this->success('用户添加成功！');
			}else{
				$this->error('用户添加失败！');
			}
		}
	}
	protected function addRole($userId) {
		//新增用户自动加入相应权限组
		$RoleUser = M("RoleUser");
		$RoleUser->user_id	=	$userId;
        // 默认加入网站编辑组
        $RoleUser->role_id	= isset($_POST['role']) ? $this->_post('role','intval') : 8;
		$RoleUser->add();
	}

    //重置密码
    public function resetPwd()
    {
    	$id  =  $_POST['id'];
        $password = $_POST['password'];
        if(''== trim($password)) {
        	$this->error('密码不能为空！');
        }
        $User = M('User');
		$User->password	=	md5($password);
		$User->id			=	$id;
		$result	=	$User->save();
        if(false !== $result) {
            $this->success("密码修改为$password");
        }else {
        	$this->error('重置密码失败！');
        }
    }
    public function password(){
        $id = $this->_get("id","intval");
        $this -> assign("id",$id);
        $this->display();
    }
    //修改权限
    public function editRole(){
        if($this->isPost()){
            $data['user_id'] = $this->_post("id",$id);
            $data['role_id'] = $this->_post('role','intval');
            $model = M("RoleUser");
            //清除该用户其他权限
            $model -> where("user_id={$data['user_id']}")->delete();
            if($model ->add($data)){
                $this->success("操作完成，结果：成功！");
            }else{
                $this->error(保存失败);
            }
            exit();
        }
        $id = $this->_get("id",$id);
        $this -> assign("id",$id);
        $role_user_model = M("RoleUser");
        $role_id = $role_user_model -> where("user_id={$id}")->getField("role_id");
        $role_model = M("Role");
        $role_list = $role_model->select();
        $this ->assign('role',$role_id);
        $this -> assign("list",$role_list);
        $this -> assign("id",$id);
        $this -> display();
    }
}








