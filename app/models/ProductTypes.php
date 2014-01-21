<?php

	class ProductTypes extends Phalcon\Mvc\Model
	{
		public $id;
		public $name;
		
		public function initialze()
		{
			$this->hasMany('id', 'Products', 'product_types_id', array('foreignKey' => array('message' => 'Product Type can not be deleted because it\'s used on Product')));
		}
	}
?>