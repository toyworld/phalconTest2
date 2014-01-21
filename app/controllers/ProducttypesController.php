<?php

 use Phalcon\Tag;
 use Phalcon\Mvc\Model\Criteria;
 
 class ProductTypesController extends ControllerBase
 {
 	public function initialize()
	{
		$this->view->setTemplateAfter('main');
		Tag::setTitle('Manage your Products');
		parent::initialize();
	}	
	public function indexAction()
	{
		$this->session->conditions = null;
	}
	
	public function searchAction()
	{
		$numberPage = 1;
		$request = $this->request;
		if($request->isPost()){
			$query = Criteria::fromInput($this->di, 'ProductTypes', $_POST);
			$this->persistent->searchParams = $query->getParams();
		}else{
			$numberPage = $this->request->getQuery("page", "int");
			if($numberPage <= 0 ){
				$numberPage = 1;
			}
		}
		
		$parameters = array();
		if($this->persistent->searchParams){
			$parametars = $this->persitent->searchParams;
		}
		
		$productTypes = ProductTypes::find($parametars);
		if(count($productTypes) == 0){
			$this->flash->notice("검색하신 상품 타입이 없습니다.");
			return $this->forward("producttypes/index");
		}
		$paginator	 = new Phalcon\Paginator\Adapter\Model(array(
			"data"	=> $productTypes,
			"limit" => 10,
			"page"  => $numberPage
		));
		$page = $paginator->getPaginate();
		$this->view->setVar("page", $page);
		$this->view->setVar("producttypes", $productTypes);
		
	}
	public function newAction()
	{
		
	}
	
	public function editAction()
	{
		$request = $this->request;
		if(!$request->isPost()){
			$producttypes = ProductTypes::findFirst(array('id=:id:', 'bind' => array('id'=>$id)));
		}
		if(!$producttypes){
			$this->flash->error("수정하실 상품타입이 없습니다.");
		}
		$this->view->setVar("id", $producttypes->id);
		
		Tag::displayTo("id", $producttypes->id);
		Tag::displayTo("name", $producttypes->name);
	}
	
	public function createAction()
	{
		//전송체크
		$request = $this->request;
		if(!$request->isPost()){
			$this->flash->notice("전송된값이 없습니다.");
			return $this->forward("producttypes/index");
		}
		$producttypes = new ProductTypes();
		$producttypes->id = $request->getPost("id","int");
		$producttypes->name = $request->getPost("name");
		$producttypes->name = strip_tags($producttypes->name);
		if(!$producttypes->save()){
			foreach ($producttypes->getMessages as $message) {
				$this->flash->error((string) $message);
			}
			return $this->forward("producttypes/new");
		}else{
			$this->flash->success("요청하신 상품타입이 생성되었습니다.");
			return $this->forward("producttypes/index");			
		}
	}
	
	public function saveAction()
	{
		$request = $this->request;
		if(!$request->isPost()){
			$this->notice("넘어온 값이 없습니다.");
			return $this->forward("producttypes/index");
		}
		
		$id = $request->getPost("id","int");
		$producttypes = ProductTypes::findFirst("id='$id'");
		if($producttypes == false){
			$this->flash->error("해당하는 상품타입이 없습니다. ".$id);
			$this->forward("producttypes/index");
		}
		$producttypes->id	= $request->getPost("id", "int");
		$producttypes->name	= $request->getPost("name", "striptags");
		if(!$producttypes->save()){
			foreach ($producttypes as $message) {
				$this->flash->error((string) $message);
			}
			return $this->forward("producttypes/index");
		}
	}
	public function deleteAction($id)
	{
		$id = $this->filter->snitize($id, array("int"));
		
		$producttypes = ProductTypes::findFirst("id='$id'");
		if(!$producttypes){
			$this->flash->error("삭제하시려는 상품타입이 없습니다. 확인해주세요 ".$id);
			return $this->forward("producttypes/index");
		}
		if($producttypes->delete()){
			foreach ($producttypes->getMessages as $message) {
				$this->flash->error((string) $message);
			}
			return $this->forward("producttypes/search");
		}else{
			$this->flash->success("해당상품 타입이 삭제 되었습니다.");
			return $this->forward("producttypes/index");
		}
	}
	
 }

?>