<?php
 
 use Phalcon\Tag,
     Phalcon\Mvc\Model\Criteria,
     Phalcon\Form\Form,
     Phalcon\Form\Element\Text,
     Phalcon\Form\Element\Hidden;
 
 class CompaniesController extends ControllerBase
 {
 	public function initialize()
	{
		$this->view->setTemplateAfter('main');
		Tag::setTitle("Mange your companise");
		parent::initalize();
	}
	protected function getForm($entiy=null, $edit=false)
	{
		$form = new Form($entiy);
		if(!$edit)
		{
			$form->add(new Text("id", array("size" => 10, "maxlength" => 10)));
		}else{
			$form->add(new Hidden("id"));
		}
		$form->add(new Text("name", array("size" => 24, "maxlength" => 70)));
		$form->add(new Text("Telephone", array("size" => 10, "maxlength" => 30)));
		$form->add(new Text("address", array("size" => 70, "maxlength" => 255)));
		$form->add(new Text("city", array("size" => 10, "maxlength" => 20)));
		return $form;
	}
	public function indexAction(){
		$this->session->conditions = null;
		$this->view->form = $this->getForm();
	}
	public function searchAction(){
		$numberPage = 1;
		if($this->request->isPost()){
			$query = Criteria::formInput($this->di, "Companies", $_POST);
			$this->persistent->searchParams = $query->getParams();
		}else{
			$numberPage = $this->request->getQuery("page", "int");
			if($numberPage <= 0){
				$numberPage = 1;
			}
		}
		
		$companies = Companies::find($parameters);
		if(count($companies) == 0){ // 검색결과가 없을경우
			$this->flash->notice("검색결과가 없습니다.");
		    return $this->forward("companies/index");
		}
		
		//페이징 시작
		$paginator = new Phal\Paginator\Adapter\Model(array(
			"data"	=> $companies,
			"limit" => 10,
			"page"  => $numberPage
		));
		$page = $paginator->getPaginate();
		$this->view->setVar("page", $page);
		$this->view->setVar("companies", $companies);
		
	}
	public function createAction()
	{
		$request = $this->request;
		if(!$request->isPost()){
			return $this->forward("companies/index");
		}	
		
		$companies = new Companies();
		$companies->name		= $request->getPost("name","striptags");
		$companies->telephone	= $request->getPost("telephone","striptags");
		$companies->address		= $request->getPost("address", "striptags");
		$companies->city		= $request->getPost("city", "striptags");
		
		if(!$companies->save()){
			foreach ($companies->getMessages() as $message) {
				$this->flash->error((string) $message);
			}
			return $this->forward("companies/new");
		}
		$this->flash->success("Company was created successfully");
		return $this->forward("companies/index");
	}
	public function saveAction()
	{
		$request = $this->request;
		if(!$request->isPost()){ //전송값 체크
			return $this->forward("companies/index");
		}
		
		$id = $request->getPost("id", "int");
		$companies = Companies::findFirstById($id);
		if($companies == FALSE){
			$this->flash->error("Company does not exit ".$id);
			return $this->forward("companies/index");
		}
		$companies->id			= $request->getPost("id", "int");
		$companies->name		= $request->getPost("name", "striptags");
		$companies->address		= $request->getPost("address", "striptags");
		$companies->telephone	= $request->getPost("telephone", "striptags");
		$companies->city		= $request->getPost("city", "striptags");
		
		if(!$companies->save()){
			foreach ($companies->getMessages() as $message) {
				$this->flash->error((string) $message);
			}
			return $this->forward("companies/edit/".$companies->id);
		}
		
		$this->flash->success("Company was updated successfully");
		return $this->forward("companies/index");
	}	
	
	public function delete($id){
		$companies = Companies::findFirstById($id);
		if(!$companies){
			$this->flash->error("Company is not found");
			return $this->forward("companies/search");
		}
		$this->flash->success("Company was delete");
		return $this->forward("companies/index");
	}
	
 }
?>