<?php
class DealOrderDetailModel extends RelationModel{
        protected $_link = array(
            'Deal'=> array(  
                'mapping_type'=>BELONGS_TO,
                'class_name'=>'Deal',
                'foreign_key'=>'did',
                'mapping_name'=>'deal',
                'mapping_fields' => 'title',
                'as_fields'=>'title',
            ),
    );
}
