<?php
$db = require THINK_PATH.'../db.conf.php';
$config	= array(
	'VAR_PAGE'=>'pageNum',
	'PAGE_LISTROWS' => 30,
	'USER_AUTH_ON'=>true,
	'USER_AUTH_TYPE'=>1,		// 默认认证类型 1 登录认证 2 实时认证
	'USER_AUTH_KEY'=>'authId',	// 用户认证SESSION标记
    'ADMIN_AUTH_KEY'=>'administrator',
	'USER_AUTH_MODEL'=>'Admin',	// 默认验证数据表模型
	'AUTH_PWD_ENCODER'=>'md5',	// 用户认证密码加密方式
	'USER_AUTH_GATEWAY'=>'/Public/login',	// 默认认证网关
	'NOT_AUTH_MODULE'=>'Public',		// 默认无需认证模块
	'REQUIRE_AUTH_MODULE'=>'',		// 默认需要认证模块
	'NOT_AUTH_ACTION'=>'',		// 默认无需认证操作
	'REQUIRE_AUTH_ACTION'=>'',		// 默认需要认证操作
    'GUEST_AUTH_ON'=>false,    // 是否开启游客授权访问
    'GUEST_AUTH_ID'=>0,     // 游客的用户ID

    'DB_LIKE_FIELDS'=>'title|remark',
	'RBAC_ROLE_TABLE'=>'nm_role',
	'RBAC_USER_TABLE'=>'nm_role_user',
	'RBAC_ACCESS_TABLE'=>'nm_access',
	'RBAC_NODE_TABLE'=>'nm_node',
	'SITENAME'=>'小米团后台',
    'CONTACT'=>'鱼鱼',
    'COMPANY'=>'小米团',
   //前台的状态配置 
    "SALT_LENGTH" => 8,
    "DEFAULT_CITY" => 1,
    "GROUP_SORT" => array(
        'coupon' => '1',
        'goods' => '2',
    ),
    'DEAL_STATUS' => array(
        'unstart' => '1',
        'unsuccess' => '2',
        'success' => '3',
        'saleout' => '4',
        'timeout' => '5',
    ),
    'ORDER_STATUS' => array(
        'deal' => '1',
        'paid' => '2',
        'delivered' => '3',
        'success' => '4',
        'closed' => '5', 
    ),
);
return array_merge($config,$db);
?>