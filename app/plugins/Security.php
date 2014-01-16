<?php
 use Phalcon\Events\Event,
 	 Phalcon\Mvc\User\Plugin,
 	 Phalcon\Mvc\Dispatcher,
 	 Phalcon\Acl;
	 
 class Security extends  Plugin
 {
 	 public function getAcl()
	 {
	 	if (!isset($this->persistent->acl)) {
			 	//Acl 생성
			 	$acl = new Phalcon\Acl\Adapter\Memory();
				$acl->setDefaultAction(Phalcon\Acl::DENY);
				
				$roles = array(
						'users'  => new Phalcon\Acl\Role('Users'),
						'guests' => new Phalcon\Acl\Role('Guests')
				);
				
				foreach ($roles as $rols) {
					$acl->addRole($role);
				}
				//백엔드 리소스 정의
				$privateResource = array(
					'companies'		=> array('index', 'search','new', 'edit', 'save', 'create', 'delete'),
					'products'		=> array('index','search', 'new', 'edit', 'save'. 'create', 'delete'),
					'producttypes'	=> array('index','search', 'new', 'edit', 'save'. 'create', 'delete'),
					'invoices'		=> array('index', 'profile')
				);
				foreach ($privateResource as $resource => $actions) {
					$acl->addResource(new Phalcon\Acl\Resource($resource), $actions);
				}
				
				//프론트 리소스정의(각페이지별로 접근할수 있는 페이지 함수를 정의하는것 같다)
				$publicResources = array(
					'index' 	=> array('index'),
					'about' 	=> array('index'),
					'session'	=> array('index', 'register', 'start', 'end'),
					'contact'	=> array('index', 'send')
				);
				foreach ($publicResources as $resource => $actions) {
					$acl->addResource(new Phalcon\Acl\Resource($resource), $actions);
				}
				//public 에대한 회원과 방문자의 접속권한을 설정한다
				foreach ($roles as $role) {
					foreach ($publicResources as $resource => $actions) {
						$acl->allow($role->getName(), $resource, '*');
					}
				}
				
				foreach ($privateResource as $resource => $actions) {
					$acl->allow('Users', $resource, $actions);
				}
				  $this->persistent->acl = $acl;
		  }
		  return $this->persistent->acl;
	 }
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