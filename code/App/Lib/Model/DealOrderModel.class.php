<?php
class DealOrderModel extends Model{
    public $_validate = array(
    );

    public $_auto   =   array(
        array('ctime','time',Model::MODEL_INSERT,'function'),
        array('mtime','time',Model::MODEL_UPDATE,'function'),
    );
}
