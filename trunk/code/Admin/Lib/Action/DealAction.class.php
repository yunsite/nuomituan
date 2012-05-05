<?php
class DealAction extends CommonAction{
    public function edit(){
        $user_model = M("User");
        $sell_list = $user_model ->where("user_sort=2")-> field("id,username")->select();
        $this -> assign('sell_list',$sell_list);
        parent::edit();
    }
}
