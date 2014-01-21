<?php
	
	class ContactController extends ControllerBase
	{
		public function initialize()
		{
			$this->view->setTemplateAfter('main');
			Phalcon\Tag::setTitle('Contact us');
			parent::initalize();
		}
		
		public function indexAction()
		{
			
		}
		public function sendAction()
		{
			$request = $this->request;
			
			if(!$request->isPost()){
				$this->flash->error('전송된 값이 없습니다. ^^');
				$this->forward('contact/index');
			}else{
				$name 		= $request->getPost('name', array('striptags', 'string'));
				$email		= $request->getPost('email', 'email');
				$comments	= $request->getPost('comments', array('striptags', 'string'));
				
				$contact	= new Contact();
				$contact->name 		= $name;
				$contact->email 	= $email;
				$contact->comments 	= $comments;
				
				$contact->created_at	= new Phalcon\Db\RawValue('now()');
				if($contact->save() == false){
					foreach ($contact->getMessages as $message) {
						$this->flash->error((string) $message);
					}
				}else{
					$this->flash->success('등록되었습니다. 감사합니다.');
					return $this->forward('contact/index');
				}
				
				return $this->forward('contact/index');	
			}
		}
	}

?>