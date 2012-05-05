<?php
class UserModel extends Model {
    public $_validate = array(
        array('email','require','Email必须'),
        array('username','require','用户名必须'),
        array('password','require','密码必须'),
        array('repassword','require','确认密码必须'),
        array('repassword','password','确认密码不一致',Model::EXISTS_VAILIDATE,'confirm'),
        array('username','','帐号已经存在',Model::EXISTS_VAILIDATE,'unique',Model::MODEL_INSERT),
    );

    public $_auto = array(
        array('password','pwdHash',Model::MODEL_BOTH,'callback'),
        array('salt','getSalt',Model::MODEL_BOTH,'callback'),
        array('nike_name','username',Model::MODEL_INSERT,'field'),
        array('user_sort','1',Model::MODEL_INSERT),
    );

    protected function pwdHash() {
        if(isset($_POST['password'])){
            $password = $_POST['password'] . getSalt();
            return pwdHash($password);
        }else{
            return false;
        }
    }
    protected function getSalt(){
        if(isset($_POST['password'])){
            return getSalt();
        }else{
            return false;
        }
    }
}
