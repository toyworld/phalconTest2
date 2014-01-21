<?php

	class Products extends Phalcon\Mvc\Model
	{
		public $id;
		public $product_types_id;
		public $name;
		public $price;
		public $active;
		
		public function initialze()
		{
			$this->belongsTo('product_types_id','ProductTypes', 'id', array('reusable' => TRUE));
		}
	}
?>