<?php
class AdminMenuAction extends  CommonAction{
    protected function display(){
        $model = M("AdminMenu");
        $list = $model -> field("id,title")->select();
        $this -> assign("menu_lsit",$list);
        parent::display();
    }
}
