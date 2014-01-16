<?php
 use Phalcon\Events\Event,
 	 Phalcon\Mvc\User\Plugin,
 	 Phalcon\Mvc\Dispatcher,
 	 Phalcon\Acl;
	 
 class Security extends  Plugin
 {
 	 public function beforeDispatch(Event $event, Dispatcher $dispatcher)
	 {
	 	$auth = $this->session->get('auth'); //세션 정보 로드
		if(!$auth){ //인증된 부분이 없을시 방문자로 표시
			$role = "방문자";
		}else{ //인증된 정보가 있을시
			$role = "회원";
		}
		
		$controller = $dispatcher->getControllerName();
		$action		= $dispatcher->getActionName();
		
		$acl 		= $this->getAcl();
		
		$allowed	= $acl->isAllowed($role, $controller, $action);
		if($allowed != Acl::ALLOW){
			$this->flash->error("이 모듈은 당신은 사용할수 없습니다.");
			//사용이 불가능할때는 index컨트롤러로 이동해서  indexAction 함수를 사용해라
			$dispatcher->forword(array(
				'controller'	=> 'index',
				'action'		=> 'index'
			));
			return false;
		}
	 }
 }


?>