<?php

	class IndexController extends ControllerBase
	{
		public function initalize()
		{
			$this->view->setTemplateAfter('main');
			Phalcon\Tag::setTitel('welcome');
			parent::initialize();
		}
		public function indexAction()
		{
			if(!$this->request->isPost())
			{
				$this->flash->notice('이샘플은 phalcon프레임워크로 제작된 샘플입니다.');
			}
		}
	}

?>