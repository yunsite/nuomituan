<?php
class CommonAction extends Action{
    public function _initialize(){
        $this -> assign("CFG",$this -> loadSysCfg());
    }
    //载入配置信息
    protected function loadSysCfg(){
        $sys_cfg_model = M("SysCfg");
        $conf = $sys_cfg_model -> select();
        $sys_cfg = array();
        foreach ($conf as $value) {
            $sys_cfg[$value['var_name']] = $value['var_value'];
        }
        return $sys_cfg;
    }
    protected function display($templateFile='',$title='',$charset='',$contentType=''){
        $this -> loadHeader($title);
        parent::display($templateFile,$charset,$contentType);
    }
    //载入网站头部需要的变量
    protected function loadHeader($title=''){
        //城市列表
        $city_model = M("City");
        $city = getCurrentDealCity();
        $this->assign("page_title",$title);
        if($title){
            $title .= "_".$city['city'].'_小米团';
        }else{
            $title = $city['city'].'_小米团';
        }
        $this -> assign("title",$title);
        $city_list = $city_model -> select();
        $this -> assign("city",$city);
        $this -> assign("city_list",$city_list);
        //菜单什么的
        //用户信息
        $user_info = session("user_info");
        $this -> assign("user_info",$user_info);
        $this -> assign('nav_menu',getNavMenu());
        $this -> assign('user_menu',getUserMenu());
    }
}
