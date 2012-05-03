<?php
// 用户模型
class XmUserAllotModel extends CommonModel {
	public $_validate	=	array(
		);

	public $_auto		=	array(
	   array('batch','getBatch',Model:: MODEL_BOTH,'function'),    
	);
}
?>