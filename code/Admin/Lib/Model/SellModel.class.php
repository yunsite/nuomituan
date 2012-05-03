<?php
// 用户模型
class XmUserModel extends CommonModel {
	public $_validate	=	array(
	array('xmuser_id','','已经卖过了',Model::EXISTS_VAILIDATE,'unique',Model::MODEL_INSERT),
    );
	public $_auto		=	array(
	);
}
?>