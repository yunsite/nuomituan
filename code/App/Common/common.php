<?php
/**
 * 公共函数
 */
function createCoupon($did,$oid){
    $model = M("Coupon");
    $coupon_no = $did.rand(10000, 99999);
    if($model ->where("coupon_no='{$coupon_no}'")->count()){
        //重复 调用递归
        return createCoupon($did, $oid);
    }else{
        $coupon['coupon_no'] = $coupon_no;
        $coupon['coupon_code'] = getRandStr(5,1);
        $coupon['did'] = $did;
        $coupon['oid'] = $oid;
        $coupon['status'] = 1;
        $model -> add($coupon);
        return;
    }
}
//
function showOrderStatus($status){
    $order_status = getOrderStatus();
    switch ($status) {
        case $order_status['deal']:
            return "已下单";
            break;
        case $order_status['paid']:
            return "已付款";
            break;
        case $order_status['delivered']:
            return "已发货";
            break;
        case $order_status['success']:
            return "交易完成";
            break;
        case $order_status['closed']:
            return "已关闭";
            break;
    }
}
//获取订单状态配置
function getOrderStatus(){
    return C("ORDER_STATUS");
}
function getDealStatus(){
    return C("DEAL_STATUS");
}
//获取支付宝支付接口配置
function getAliConfig(){
    //↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
    //合作身份者id，以2088开头的16位纯数字
    $aliapy_config['partner']      = '2088702147484773';
      
    //安全检验码，以数字和字母组成的32位字符
    $aliapy_config['key']          = 'splz7kaiu4wquwtaiaxfrufg1jlrrip8';
    
    //签约支付宝账号或卖家支付宝帐户
    $aliapy_config['seller_email'] = 'money@cn-road.net';
    
    //页面跳转同步通知页面路径，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
    //return_url的域名不能写成http://localhost/create_direct_pay_by_user_php_utf8/return_url.php ，否则会导致return_url执行无效
    $aliapy_config['return_url']   = 'http://xwoo.info/cart/paymentreturn';
    
    //服务器异步通知页面路径，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
    $aliapy_config['notify_url']   = 'http://xwoo.info/cart/paymentnotify';
    
    //↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
    
    //签名方式 不需修改
    $aliapy_config['sign_type']    = 'MD5';
    
    //字符编码格式 目前支持 gbk 或 utf-8
    $aliapy_config['input_charset']= 'utf-8';
    
    //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
    $aliapy_config['transport']    = 'http';
    return $aliapy_config;
}

function getUserId(){
    if(isLogin()){
        return $_SESSION['user_info']['id'];
    }
    return 0;
}
function getCurrentUrl(){
    return "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
}
function getNavMenu(){
    return array(
        '今日团购' => __APP__.'/',
        '团购列表' => __APP__.'/Index/deallist',
        '往期团购' => __APP__.'/Index/deallist/history/1',
        '帮助' => __APP__.'/Article/articlelist/id/1',
    );
}
function getUserMenu(){
    return array(
        '我的订单' => __APP__.'/order',
        '修改密码' => __APP__.'/user/chgpwd',
        '个人资料' => __APP__.'/user/profile',
    );
}
function needLogin(){
    if(isLogin()){
        return;
    }else{
        $burl = urlencode(getCurrentUrl());
        redirect(__APP__."/User/login/?burl={$burl}");
    }
}
function isLogin(){
     if (session("user_info")) {
         return true;
     }
     return false;
}
/**
 * 获取当前团购城市
 */         
function getCurrentDealCity(){
    $model = M("City");
    if($city = $_GET['city']){
       $deal_city = $model -> where("city_py='{$city}'") -> find(); 
    }
    else if($city_id = cookie('deal_city')){
        $deal_city = $model -> where("id={$city_id}") -> find();
    }else{
        //导入Ip类
        Vendor('IP.ip');
        $ip =  get_client_ip();
        $iplocation = new iplocate();
        $address=$iplocation->getaddress($ip);
        $city_list = $model -> select();
        foreach ($city_list as $city){
            if(strpos($address['area1'],$city['city'])){
                $deal_city = $city;
                break;
            }
        }
        if(! $deal_city){
            $default = C("DEFAULT_CITY");
            $deal_city = $model -> where("id={$default}") -> find();
        }
    }
    cookie("deal_city",$deal_city['id']);
    return $deal_city;
}

function pwdHash($password, $type = 'md5') {
    return hash ( $type, $password );
}
//获取加盐字符串

function getSalt() {
    if($salt = session("salt")){
        return $salt;
    }else{
        $salt = getRandStr(C("SALT_LENGTH"));
        session("salt",$salt);
        return $salt;
    }
}
//获取随机字符串        
function getRandStr($length = 32, $mode = 0) {
    switch ($mode){
        case '1' :
            $str = '1234567890';
            break;
        case '2' :
            $str = 'abcdefghijklmnopqrstuvwxyz';
            break;
        case '3' :
            $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case '4' :
            $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            break;
        case '5' :
            $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
            break;
        case '6' :
            $str = 'abcdefghijklmnopqrstuvwxyz1234567890';
            break;
        default :
            $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
            break;
    }

    $result = '';
    $l = strlen($str);
    for ($i = 0; $i < $length; $i++) {
        $num = rand(0, $l);
        $result .= $str[$num];
    }
    return $result;
}


