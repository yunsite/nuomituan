<?php
class DealOrderModel extends RelationModel{
    protected $_link = array(
        'DealOrderDetail'=> array(  
            'mapping_type'=>HAS_MANY,
            'class_name'=>'DealOrderDetail',
            'foreign_key'=>'oid',
            'mapping_name'=>'detail',
        ),
        'User'=> array(  
                'mapping_type'=>BELONGS_TO,
                'class_name'=>'User',
                'foreign_key'=>'uid',
                'mapping_fields' => 'username,phone',
                'as_fields' => 'username,phone',
            ),
    );
    
}
