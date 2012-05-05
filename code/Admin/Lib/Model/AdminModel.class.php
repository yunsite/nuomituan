<?php
// 用户模型
class AdminModel extends CommonModel {
	public $_validate	=	array(
		array('account','require','帐号格式错误'),
		array('password','require','密码必须'),
		array('repassword','require','确认密码必须'),
		array('repassword','password','确认密码不一致',Model::EXISTS_VAILIDATE,'confirm'),
		array('account','','帐号已经存在',Model::EXISTS_VAILIDATE,'unique',Model::MODEL_INSERT),
		);

	public $_auto		=	array(
		array('password','pwdHash1',Model::MODEL_BOTH,'callback'),
		array('create_time','time',Model::MODEL_INSERT,'function'),
		array('update_time','time',Model::MODEL_UPDATE,'function'),
		array('fid','getMemberId',Model::MODEL_INSERT,'function'),
		);

	protected function pwdHash1() {
		if(isset($_POST['password'])) {
			return pwdHash1($_POST['password']);
		}else{
			return false;
		}
	}
}
