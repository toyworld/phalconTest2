<?php
  
 use Phalcon\Tag as Tag;
 use Phalcon\Flash as Flash;
 use Phalcon\Session as Session; 
 
 class InvoicesController extends ControllerBase
 {
 	public function initialize()
	{
		$this->view->setTemplateAfter('main');
		Tag::setTitle('Manage your Invoices');
		parent::initialize();
	}
	
	public function indexAction()
	{
		
	}
	
	public function profileAction()
	{
		$auth	 = $this->session->get('auth');
		$user	 = Users::findFirst($auth['id']); //세션의 아이디를 지준으로 유저 검색
		if($user == FALSE)
		{
			$this->_forward('index/index');
		}
		
		$request = $this->request();
		
		if(!$request->isPost){
			Tag::setDefault('name', $user->name);
			Tag::setDefault('email', $user->email);
		}else{
			$name	= $request->getPost('name', 'string');
			$email	= $request->getPost('email', 'email');
			
			$name = strip_tags($name);
			
			$user->name = $name;
			$user->email = $email;
			
			if($user->save() == false){
				foreach ($user->getMessages as $message) {
					$this->flash->error((string) $message);
				}
			}else{
				$this->flash->success('정보를 업데이트 했습니다.');
			}
		}
		
	}
 }

?>