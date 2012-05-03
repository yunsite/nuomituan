<?php
class SubscriptionModel extends Model{
    public $_validate = array(
        array('email','require','Email必须'),
        array('email','','你已经订阅过了哦~',Model::EXISTS_VAILIDATE,'unique',Model::MODEL_INSERT),
    );

    public $_auto       =   array(
        array('ctime','time',Model::MODEL_INSERT,'function'),
        array('mtime','time',Model::MODEL_UPDATE,'function'),
        array('is_effect','1'),
    );
}
