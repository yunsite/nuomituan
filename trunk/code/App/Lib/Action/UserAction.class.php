<?php
class UserAction extends CommonAction{
    //用户中心
    public function index(){
        redirect(__URL__.'/profile'); 
    }
    /**
     * 注册
     */
     public function reg(){
         //reg form
         if(! $this -> isPost()){
             $this -> display('','注册');
             exit();
         }
         $model = D("User");
         if(!$model -> create()) {
            $this->error($model->getError());
         }
         if($model -> add()){
             //订阅
             if($this->_post('subscribe')){
                 $deal_city = getCurrentDealCity();
                 $sub_model = D("Subscription");
                 $sub_data = $sub_model -> create();
                 $sub_data['city'] = $deal_city['id'];
                 $sub_model -> add($sub_data);
             }
             $this -> success("注册成功！",__URL__.'/login');  
         }
         
     }
     //登陆
     public function login(){
         if(isLogin()){
             $this -> success('你已经登陆了！如果想切换账户请先退出！');
             exit();
         }    
         if(! $this->isPost()){
             $this -> display('','登陆');
             exit();
         }
         //登陆
         $username = $this->_post('username','mysql_escape_string');
         $password = $this->_post('password','mysql_escape_string');
         if(! $burl = $this->_get('burl')){
             $burl = __APP__.'/';
         }
         $model = M("User");
         $data = $model -> where( "username='{$username}' or email='{$username}'" ) -> find();
         //验证
         if($data){
             $password_hash = pwdHash($password.$data['salt']);
             if($password_hash == $data['password']){
                 //登陆成功
                 session("user_info",$data);
                 redirect($burl);
             }else{
                 $this->error("用户或密码不对");
             }
         }else{
             $this -> error("用户或密码不对");
         }
     }
     //退出登陆
     public function logout(){
         session("user_info",array());
         $this -> success("退出成功",__APP__.'/');
     }
     //修改密码
     public function chgpwd(){
         if(! $this -> isPost()){
             $this -> user('修改密码');
             exit();
         }
         $id = getUserId();
         $oldpassword = $this -> _post('oldpassword','mysql_escape_string');
         $password = $this -> _post('password','mysql_escape_string');
         $model = D("User");
         $data = $model -> where("id={$id}") -> find();
         //校验
         if ($data['password'] == pwdHash($oldpassword.$data['salt'])) {
             $data['salt'] = getSalt();
             $data['password'] = pwdHash($password.$data['salt']);
             $model ->where("id={$id}") -> save($data);
             $this -> success($model -> getError()); 
         }else{
             $this -> error("原密码错误！");
         }         
     }
     //修改个人资料
     public function profile(){
         $this -> user("个人资料");
     }
     //用户中心模版
     protected function user($title){
         needLogin();
         $model = D('User');
         $uid = getUserId();
         $data = $model ->find($uid);
         $this -> assign("user",$data);
         $this ->assign("tpname",'inc:uc_'.ACTION_NAME);
         $this -> display('user',$title);
     }
     //修改用户资料
     public function update(){
         needLogin();
         $modoel = D("User");
         if(getUserId() != $this->_post('id')){
             $this->error("无权操作");
         }
         if($modoel -> create()){
            $modoel -> save();
            $user_info = $modoel->find(getUserId());
            session("user_info",$user_info);
         }
         $this -> success("更新成功",__URL__);
         exit();
     }
     //团购券消费
     public function verifycoupon(){
         if(! $this -> isPost()){
             $this ->display('',"团购券消费");
         }
     }
}







