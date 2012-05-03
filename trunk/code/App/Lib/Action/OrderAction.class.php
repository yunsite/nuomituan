<?php
class OrderAction extends CommonAction{
    public function index(){
        needLogin();
        $uid = getUserId();
        $order_model = D("DealOrder");
        $list  = $order_model ->where("uid='{$uid}'")->select();
        $this -> assign("list",$list);
        $this -> idisplay("我的订单");
    }
    //取消订单
    public function cancel(){
        needLogin();    
        $id = $this->_get('id','intval');
        $uid = getUserId();
        $model = D("DealOrder");
        $order = $model ->where("id={$id}")->find();
        if(! $order){
            $this -> error("参数错误：不存在的订单！");
        }
        if($order['uid'] != $uid){
            $this -> error("无权操作");
        }
        $order_status = getOrderStatus();
        //只有已下单状态的订单可以取消
        if($order['status'] == $order_status['deal']){
            $detail_model = D("DealOrderDetail");
            $detail_model -> where("oid='{$id}'") -> delete();
            $model -> where("id={$id}")->delete();
            $this -> success("取消成功",__URL__);
        }else{
            $this -> error("无法取消改订单：订单状态不允许取消。");
        }
    }
    public function view(){
        $id = $this -> _get('id','intval');
        
        $order_model = M("DealOrder");
        $detail_model = M("DealOrderDetail");
        $deal_model = M("Deal");
        $order = $order_model ->find($id);
        $detail = $detail_model ->where("oid={$order['id']}")->select();
        $coupon_model = M("Coupon");
        foreach ($detail as $key => $value) {
            $detail[$key]['title'] = $deal_model -> where("id='{$value['did']}'")->getField('title');
            $detail[$key]['money'] = $value['count'] * $value['price'];
            $detail[$key]['coupon'] = $coupon_model->where("did='{$value['did']}' and oid='{$value['oid']}'")->select();
        }
        $this -> assign("order",$order);
        $this -> assign("detail",$detail);
        $this -> idisplay("订单详情");
    }
    protected function idisplay($title){
         needLogin();
         $model = D('User');
         $uid = getUserId();
         $data = $model ->find($uid);
         $this -> assign("user",$data);
         $this ->assign("tpname",'inc:uc_order_'.ACTION_NAME);
         $this->display('User:user',$title);
     }
}
