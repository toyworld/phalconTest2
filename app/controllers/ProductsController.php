<?php
 
 use Phalcon\Tag;
 use Phalcon\Mvc\Model\Criteria;
 
 class ProductsController extends ControllerBase
 {
 	public function initialize()
	{
		$this->view->setTemplateAfter('main');
		Tag::setTitle('Manage your product types');
		parent::initialize();
	}
 	public function indexAction()
	{
		$this->persistent->searchParams = null;
		$this->view->productTypes = ProductTypes::find();
	}
	public function searchAction()
	{
		$numberPage =1;
		
		if($this->request->isPost())
		{
			$query = Criteria::formInput($this->di, "Products", $_POST);
			$this->persistent->searchParams = $query->getParams();
				
		}else{
			
			$numberPage = $this->request->getQuery("page","int");
			if($numberPage <= 0){
				$numberPage =1;
			}
		}
		
		$parameters = array();
		if($this->persistent->searchParams){
			$parameters = $this->persistent->searchParams;
		}
		
		$products = Products::find($parameters);
		if(count($products) == 0){
			$this->flash->notice("검색결과가 없습니다 죄송합니다");
			return $this->forward("products/index"); //forward는 리턴하는 함수 같다
		}
		//페이징 함수 시작
		$paginator = new Phalcon\Paginator\Adapter\Model(array(
			"data"	=> $products,
			"limit" => 5,
			"page"	=> $numberPage
		));
		
		$page = $paginator->getPaginate();
		$this->view->setVar("page", $page);
		
	}
	public function newAction()
	{
		$this->view->set("productTypes", ProductTypes::find());
	}
	public function editAction($id)
	{
		//request 객체 호출
		$request = $this->request;
		if(!$request->isPost()){
			$id = $this->filter->sanitize($id, array("int"));
			//ProductsController 에서 id 값을 조회해서 첫번째 인자를 가저온다음 테그로 출력한다
			$product = Products::findFrist('id="'.$id.'"'); 
			
			if(!$product){
				$this->flash->error("해당상품이 존재하지 않습니다.");
				return $this->forward("products/index");
			}		
			
			$this->view->setVar("id", $product->id);
			
			Tag::displayTo("id", $product->id);
			Tag::displayTo("product_types_id", $product->product_types_id);
			Tag::displayTo("name", $product->name);
			Tag::displayTo("price", $product->price);
			Tag::displayTo("active", $product->active);
			
			$this->view->setVar("productTypes", ProductTypes::find());		
		}
		
		
		
	}
	public function createAction()
	{
		$request = $this->request;
		
		//post로 넘어온 값이 없으면 상품관리 페이지로 이동한다
		if(!$request->isPost()){
			return $this->forward("products/index");
		}
		
		$products = new Products();
		$products->id = $request->getPost("id","int");
		$products->product_types_id = $request->getPost("product_types_id", "int");
		$products->name = $request->getPost("name","striptags");
		$products->price = $request->getPost("price", "double");
		$products->active = $request->getPost("active");
		
		if($products->save()){ //생성 실패시 에러 메세지를 출력후 newAction를 호출한다 
			foreach ($products->getMessage() as $message) {
				$this->flash->error((string) $message);
			}
			return $this->forward("products/new");
		}else{
			$this->flash->success("새로 생성하는데 성공했습니다. 감사합니다.");
			return $this->forward("products/index");
		}
		
	}
	public function saveAction()
	{
		$request = $this->request;
		if(!$request->isPost()){
			return $this->forward("products/index");
		}
		
		$id = $request->getPost("id");
		//해당 상품이 존하는지 일단 체크
		$products = Products::findFrist("id='$id'");
		if($products == false){
			$this->flash->error("해당상품이 존재하지 않습니다".$id);
			return $this->forward("products/index");
		}
		
		$products->id		= $request->getPost("id", "int");
		$products->product_types_id = $request->getPost("product_types_id", "int");
		$products->name 	= $request->getPost("name");
		$products->price	= $request->getPost("price");
		$products->active	= $request->getPost("active");
		
		$products->name		= strip_tags($products->name);
		
		if(!$products->save()){
			foreach ($products->getMessage() as $message) {
				$this->flash->error((string) $message);
			}
			return $this->forward("products/edit/".$products->id);
		}else{
			$this->flash->success("해당 상품을 업데이트 했습니다.");
			return $this-forward("products/index");
		}
				
	}
	//삭제 함수
	public function deleteAction($id)
	{
		//id값이 인트형식으로만 입력받는다
		$id = $this->filter->sanitize($id,array("int")); 
		$products = Porducts::findFirst('id="'.$id.'"');
		
		if(!$products){
			$this->flash->error("해당상품을 찾을수 없습니다. 감사합니다.");
			return $this->forward("products/index");
		}
		
		if(!$products->delete()){
			foreach ($products->getMessage() as $message) {
				$this->flash->error((string) $message);
			}
			return $this->forward("products/search");
		}else{
			$this->flash->success("해당상품을 삭제 했습니다");
			return $this->forward("products/index");
		}
	}
 }

?>