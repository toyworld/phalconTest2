<?php

 class AboutController extends ControllerBase
 {
 	 public function initialize()
	 {
		 $this->view->setTemplateAfter('main');
		 Phalcon\Tag::setTitle('이용안내');
		 parent::initialize();
	 }
	 public function indexAction()
	 {
	 	
	 }
 }

?>