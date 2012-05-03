<?php
// 用户模型
class XmUserModel extends CommonModel {
	public $_validate	=	array(
		array('password','require','密码必须'),
		array('minum','','米聊号已经存在',Model::EXISTS_VAILIDATE,'unique',1),
		array('email','','邮件已经存在',Model::EXISTS_VAILIDATE,'unique',1),
		);

	public $_auto		=	array(
            array('owner','getMemberId',Model:: MODEL_BOTH,'function'),	    
		);
}
?>