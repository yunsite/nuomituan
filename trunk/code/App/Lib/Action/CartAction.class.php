<?php
/**
 * 购物车
 */
class CartAction extends CommonAction{
	//购物车
	public function index(){
	    $cart = session("cart");
        if(! $cart){
            $this -> display('','购物车');
        }
	    $id_string = implode(',', array_keys($cart));
	    $model = M("Deal");
        $deal = $model -> where("id in ({$id_string})") -> field('content,shop_connect',true)->select();
        foreach ($deal as $key => $value) {
            $deal[$key]['cart'] = $cart[$value['id']];
        }
        $this -> assign("deal",$deal);
	    $this -> display();
	}
    public function add(){
        needLogin();
        if(! $id = $this->_get('id','intval')){
            $this -> error('参数错误:missing id');
        }
        $model = M("Deal");
        $deal = $model -> where("id={$id}") -> field('content,shop_connect',true)->find();
        //未找到团购
        if(!$deal){
            $this -> error("不存在的团购哦~~");
        }
        if(! $cart = session("cart")){
            $cart = array("$id"=>0);
        }else if(! $cart[$id]){
            $cart[$id] = 0;
        }
        $new_count = $cart[$id] + 1;
        //数量超过限制
        if($new_count + $deal['brought_count'] > $deal['max_brought']){
            $this -> error("该团购已经达到最大数量限制",__URL__);
        }
        if($new_count > $deal['user_max_brought']){
            $this -> error("该团购一个用户最多只能购买{$deal['user_max_brought']}件",__URL__);
        }
        //已经结束。。更多。。。
        $cart[$id] = $new_count;
        session("cart",$cart);
        redirect(__URL__);
    }
    public function edit(){
        $id = $this -> _get('id','intval');
        $count = $this -> _get('count','intval');
        $model = M("Deal");
        $deal = $deal = $model -> where("id={$id}") -> field('content,shop_connect',true)->find();
        //一些条件。。
        if($count > $deal['user_max_brought']){
            $this->ajaxReturn('',"该团购一个用户最多只能购买{$deal['user_max_brought']}件",0);
        }
        $_SESSION['cart'][$id] = $count;
        $this -> success("更新成功");
    }
    
    public function delete(){
        $id = $this -> _get('id','intval');
        unset($_SESSION['cart'][$id]);
        $this -> ajaxReturn($id);
    }

    public function checkOut(){
        $cart = session("cart");
        if(! $cart){
           redirect(__URL__); 
        }
        $id_string = implode(',', array_keys($cart));
        $model = M("Deal");
        $deal = $model -> where("id in ({$id_string})") -> field('content,shop_connect',true)->select();
        //非post的时候 显示结算页面
        if(! $this -> isPost()){
            $need_addr = false;
            $group_sort = C('GROUP_SORT');
            foreach ($deal as $key => $value) {
                if($value['group_sort'] == $group_sort['goods']){
                    $need_addr = true;
                }
                $deal[$key]['cart'] = $cart[$value['id']];
            }
            if($need_addr){
                $this -> loadAddress();
            }
            $this -> assign("need_addr",$need_addr);
            $this -> assign("deal",$deal);
            $this -> display('','结算');
            exit();
        }
        $uid = getUserId();
        $order_model = D("DealOrder");
        $order = $order_model -> create();
        $order_detail = array();
        $money = 0;
        foreach ($deal as $key => $value) {
            $order_detail[$key]['did'] = $value['id'];
            $order_detail[$key]['count'] = $cart[$value['id']];
            $order_detail[$key]['price'] = $value['price'];
            $money += $value['price'] * $cart[$value['id']];
        }
        $money += $this ->getPaymentFee() + $this -> getDeliveryFee();
        $order['money'] = $money;
        //使用优惠券...
        $order['final_money'] = $money;
        $order['uid'] = $uid;
        $order_detail_model = M("DealOrderDetail");
        $isok = true;
        if($oid = $order_model -> add($order)){
            foreach ($order_detail as $value) {
                $value['oid'] = $oid;
                if(! $order_detail_model -> add($value)){
                    $isok = false;
                }
            }
        }else{
            $isok = false;
        }
        if($isok){
            //清空购物车
            session("cart",null);
            //支付记录生成
            $payment_notice = D("PaymentNotice");
            $data = array();
            $data['oid'] = $oid;
            $data['uid'] = $uid;
            $data['money'] = $money;
            $data['payment_id'] = '1';
            $data['ctime'] = time();
            $pid = $payment_notice -> add($data);
            redirect(__URL__."/payment/id/{$pid}");
            //print_r($payment_notice);
            //$this -> success("OK");
        }else{
            echo 'err';
        }
    }
    //载入用户地址
    protected function loadAddress(){
        $uid = getUserId();
        $model = M("UserAddress");
        $addr_list = $model -> where("uid={$uid}") ->select();
        $this -> assign("addr_list",$addr_list);
        return;
    }
    //配送的费用
    protected function getDeliveryFee(){
        return 0;
    }
    //支付的手续费
    protected function getPaymentFee(){
        return 0;
    }
    //更新统计数量
    protected function updateDeal($detail){
        $deal_model = M("Deal");
        $deal_order_model = M("DealOrder");
        $deal_order_detail_model = M("DealOrderDetail");
        $uid = getUserId();
        //更新购买数量
        $orders = $deal_order_model ->where('uid',$uid)->field('id')->select();
        foreach ($orders as $key => $value) {
            $orders[$key] = $value['id'];
        }
        $ids = implode("','", $orders);
        $count = $deal_order_detail_model -> where("oid in ('{$ids}') and did={$detail['did']}")->count();
        $deal = $deal_model->where("id={$detail['did']}") -> field("id,user_count,brought_count,status,min_brought,max_brought")->find();
        print_r($count);
        if (! $count) {
            $deal['user_count'] ++;
        }
        //更新订单状态
        $deal_status = getDealStatus();
        $deal['brought_count'] += $detail['count'];
        if($deal['status'] == $deal_status['unsuccess'] && $deal['brought_count'] > $deal['min_brought']){
            $deal['status'] = $deal_status['success'];
            $deal['success_time'] = time();
        }else if ($deal['status'] == $deal_status['success'] && $deal['brought_count'] > $deal['max_brought']){
            $deal['status'] = $deal_status['saleout'];
        }
        $deal_model -> save($deal);
        return;
    }
    //付款
    public function payment(){
        $id = $this -> _get('id','intval');
        $payment_notice = M("PaymentNotice");
        $payment = $payment_notice -> find($id);
        $this -> assign("list",$payment);
        $this -> display('','在线支付');
    }
    //支付宝付款
    public function alipayto(){
        import("@.Alipay.alipay_service");
        $aliapy_config = getAliConfig();
        
        /**************************请求参数**************************/
        //必填参数//
        //请与贵网站订单系统中的唯一订单号匹配
        $out_trade_no = $this->_get('oid');
        //订单名称，显示在支付宝收银台里的“商品名称”里，显示在支付宝的交易管理的“商品名称”的列表里。
        $subject      = $this->_get('subject');
        //订单描述、订单详细、订单备注，显示在支付宝收银台里的“商品描述”里
        $body         = $this ->_get('body');
        //订单总金额，显示在支付宝收银台里的“应付总额”里
        $total_fee    = $this -> _get('money');
        
        //扩展功能参数——默认支付方式//
        
        //默认支付方式，取值见“即时到帐接口”技术文档中的请求参数列表
        $paymethod    = '';
        //默认网银代号，代号列表见“即时到帐接口”技术文档“附录”→“银行列表”
        $defaultbank  = '';
        
        
        //扩展功能参数——防钓鱼//
        
        //防钓鱼时间戳
        $anti_phishing_key  = '';
        //获取客户端的IP地址，建议：编写获取客户端IP地址的程序
        $exter_invoke_ip = '';
        //注意：
        //1.请慎重选择是否开启防钓鱼功能
        //2.exter_invoke_ip、anti_phishing_key一旦被使用过，那么它们就会成为必填参数
        //3.开启防钓鱼功能后，服务器、本机电脑必须支持SSL，请配置好该环境。
        //示例：
        //$exter_invoke_ip = '202.1.1.1';
        //$ali_service_timestamp = new AlipayService($aliapy_config);
        //$anti_phishing_key = $ali_service_timestamp->query_timestamp();//获取防钓鱼时间戳函数
        
        
        //扩展功能参数——其他//
        
        //商品展示地址，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
        $show_url           = 'http://www.xwoo.info/order/';
        //自定义参数，可存放任何内容（除=、&等特殊字符外），不会显示在页面上
        $extra_common_param = '';
        
        //扩展功能参数——分润(若要使用，请按照注释要求的格式赋值)
        $royalty_type       = "";           //提成类型，该值为固定值：10，不需要修改
        $royalty_parameters = "";
        //注意：
        //提成信息集，与需要结合商户网站自身情况动态获取每笔交易的各分润收款账号、各分润金额、各分润说明。最多只能设置10条
        //各分润金额的总和须小于等于total_fee
        //提成信息集格式为：收款方Email_1^金额1^备注1|收款方Email_2^金额2^备注2
        //示例：
        //royalty_type      = "10"
        //royalty_parameters= "111@126.com^0.01^分润备注一|222@126.com^0.01^分润备注二"
        
        /************************************************************/
        
        //构造要请求的参数数组
        $parameter = array(
                "service"           => "create_direct_pay_by_user",
                "payment_type"      => "1",
                
                "partner"           => trim($aliapy_config['partner']),
                "_input_charset"    => trim(strtolower($aliapy_config['input_charset'])),
                "seller_email"      => trim($aliapy_config['seller_email']),
                "return_url"        => trim($aliapy_config['return_url']),
                "notify_url"        => trim($aliapy_config['notify_url']),
                
                "out_trade_no"      => $out_trade_no,
                "subject"           => $subject,
                "body"              => $body,
                "total_fee"         => $total_fee,
                
                "paymethod"         => $paymethod,
                "defaultbank"       => $defaultbank,
                 
                "anti_phishing_key" => $anti_phishing_key,
                "exter_invoke_ip"   => $exter_invoke_ip,
                
                "show_url"          => $show_url,
                "extra_common_param"=> $extra_common_param,
                
                "royalty_type"      => $royalty_type,
                "royalty_parameters"=> $royalty_parameters
        );
        
        //构造即时到帐接口
        $alipayService = new AlipayService($aliapy_config);
        $html_text = $alipayService->create_direct_pay_by_user($parameter);
        echo $html_text;
    }
    //支付完成返回接口
    public function paymentReturn(){
        import("@.Alipay.alipay_notify");
        $aliapy_config = getAliConfig();
        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify($aliapy_config);
        $verify_result = $alipayNotify->verifyReturn();
        //$verify_result = true;
        if($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
            $out_trade_no   = $_GET['out_trade_no'];    //获取订单号
            $trade_no       = $_GET['trade_no'];        //获取支付宝交易号
            $total_fee      = $_GET['total_fee'];       //获取总价格
        
            if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
                $this -> paySuccess($out_trade_no,$trade_no,$total_fee);
            }
            else {
              echo "trade_status=".$_GET['trade_status'];
            }
            $this -> success("支付成功",__APP__.'/order');
        }
        else {
            //验证失败
            //如要调试，请看alipay_notify.php页面的verifyReturn函数，比对sign和mysign的值是否相等，或者检查$responseTxt有没有返回true
            echo "验证失败";
        }
        
    }
    //支付宝异步消息处理
    public function paymentNotify(){
        import("@.Alipay.alipay_notify");
        $aliapy_config = getAliConfig();
        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify($aliapy_config);
        $verify_result = $alipayNotify->verifyNotify();
        if($verify_result) {//验证成功
            $out_trade_no   = $_POST['out_trade_no'];       //获取订单号
            $trade_no       = $_POST['trade_no'];           //获取支付宝交易号
            $total_fee      = $_POST['total_fee'];          //获取总价格
            if($_POST['trade_status'] == 'TRADE_FINISHED') {
                $this -> paySuccess($out_trade_no,$trade_no,$total_fee);
            }
            else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                $this -> paySuccess($out_trade_no,$trade_no,$total_fee);
            }
            echo "success";     //请不要修改或删除
        }
        else {
            //验证失败
            echo "fail";
            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
    }
    //支付成功处理
    protected function paySuccess($out_trade_no,$trade_no,$total_fee){
        $payment_notice = D("PaymentNotice");
        $payment = $payment_notice ->where("oid='{$out_trade_no}'")-> find();
        if($payment['is_paid']){
            if($payment['notice_no'] == $trade_no){
                //return;
            }else{
                //此款项为多余支付 需要处理，不写了。
            }
        }else{
            //更新支付记录表支付状态
            //如果订单不存在。。。
            //金额校验。。。懒得写了
            $data = array();
            $data['notice_sn'] = $trade_no;
            $data['pay_time'] = time();
            $data['is_paid'] = '1';
            $data['id'] = $payment['id'];
            $payment_notice -> save($data);
        }
        //更新订单的状态
        $deal_order = D("DealOrder");
        $detail_model = D("DealOrderDetail");
        $order = $deal_order ->where("id='{$out_trade_no}'")->find();
        $order_status = getOrderStatus();
        $data = array('id'=>$order['id']);
        if($order['stutus'] = $order_status['deal']){
            $data['status'] = $order_status['paid'];
            $data['pay_time'] = time();
        }
        //echo $order['id'];
        //更新购买数量
        $detail = $detail_model -> where("oid='{$order['id']}'") ->select();
        foreach ($detail as $value) {
            $this -> updateDeal($value);
            //生成团购券
            $deal_sort = C("GROUP_SORT");
            $deal_model = M("Deal");
            $deal = $deal_model ->find($detail['did']);
            if($deal['group_sort'] == $deal_sort['coupon']){
                $coupon = $this -> saveCoupon($value);
            }
        }
        $deal_order -> save($data);
        return ;
    }
    protected function saveCoupon($detail){
        $coupon = array();
        for($i = 0;$i<$detail['count'];$i++){
            createCoupon($detail['did'],$detail['oid']);
        }
    }
    public function test(){
        //$this -> saveCoupon(array('did'=>12,'oid' => 445554,'count'=>2));
    }
}












