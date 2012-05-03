<?php
define('SITE_ROOT',substr(dirname(__FILE__),0,-8));
$db = require THINK_PATH.'../db.conf.php';
$config =  array(
    'URL_MODEL' => '2',
    'URL_CASE_INSENSITIVE' =>true,
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
return array_merge($db,$config);
?>