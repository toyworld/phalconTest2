<?php

	class SessionController extends ControllerBase
	{
		private function _registerSession($user)
		{
			$this->session->set('auth', array(
				'id'	=> $user->id,
				'name'	=> $user->name
			));
		}
		
		public function startAction()
		{
			if($this->request->inPost()){
				//post로 넘어온 값을 넘겨 받는다
				$email 		= $this->request->getPost('email', 'email');
				$password 	= $this->request->getPost('password');
				$password= sha1($password);
				
				//넘어온 인자값으로 데이터베이스를 조회한다
				$user	= Users::findFrist(array(
						"email = :email: AND password = :password: AND active = 'Y'",
						"bind" => array("email" => $email, "password" => $password)
				));
				
				if($user != false){
					$this->_registerSession($user);
					$this->flash->success('welcome '.$user->name);
					
					return $this->dispatcher->forward(array(
						'controller'	=> 'invoices',
						'action'		=> 'index'
					));
				}
				
				$this->flash->error('Wrong email/password');
				
			}
			
			return $this->dispatcher->forward(array(
					'controller' => 'session',
					'action'	 => 'index'
			));
		}
	}

?>