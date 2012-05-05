<?php
class DealOrderAction extends CommonAction{
    public function test(){
        $t = 1336039488;
        echo date("Y-m-d H:m:s",$t);
    }
    public function index(){
        parent::index(true);
    }
}
