<?php
class IndexAction extends CommonAction {
    /**
     * 系统主页
     */
    public function index(){
        //读取团购
        $deal_model = M("Deal");
        $deal = $deal_model -> order("ctime") -> find();
        $this -> assign("deal",$deal);
        //分享处理
        $share['url'] = urlencode(getCurrentUrl());
        $share['content'] = urlencode($deal['title']);
        $this -> assign("share",$share);
        $this->display();
    }
    /**
     * 团购列表
     */
     public function dealList(){
         $this -> display();
     }
     //订阅
     public function subscribe(){
         if(! $this->isPost()){
             $this -> display('',"邮件订阅");
             exit();
         }
         $model = D("Subscription");
         if(! $model -> create()){
             $this -> error($model -> getError());
         }
         $model -> add();
         $this -> success("订阅成功");
     }
}