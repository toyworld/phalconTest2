<?php

 try{
 		    $config = new \Phalcon\Config\Adapter\Ini('../app/config/config.ini');
	 	     //로더 설정
			 $loader = new \Phalcon\Loader();
			 $loader->registerDirs(array(
						 		$config->application->controllersDir,
						 		$config->application->pluginsDir,
						 		$config->application->libraryDir,
						 		$config->application->modelsDir
							))->register();
			
			//종속성계채 생성
		    $di = new Phalcon\DI\FactoryDefault();
			//세션 시작 di선언
			$di->set('session', function(){
				$session = new \Phalcon\Session\Adapter\Files();
				$session->start();
				return $session;
			});
			
			$di->set('dispatcher', function() use ($di) {
   			    $eventsManager = $di->getShared('eventsManager');
                $security = new Security($di);
				$eventsManager->attach('dispatch', $security);
			    $dispatcher = new Phalcon\Mvc\Dispatcher();
				$dispatcher->setEventsManager($eventsManager);
			    return $dispatcher;
			});
			
			//뷰서비스 실시
			$di->set('view', function(){
		        $view = new \Phalcon\Mvc\View();
		        $view->setViewsDir('../app/views/');
		        return $view;
		    });
			
			
			
			$di->set('url', function(){
			        $url = new \Phalcon\Mvc\Url();
			        $url->setBaseUri('/');
			        return $url;
	      	});
			
			//데이터베이스 생성
			$di->set('db', function() use ($config){
				return new \Phalcon\DB\Adapter\PDO\Mysql(array(
					"host"		=> $config->database->host,
					"username"	=> $config->database->username,
					"password"	=> $config->database->password,
					"dbname"	=> $config->database->name
				));
			});
			
			$application = new \Phalcon\Mvc\Application($di);
			echo $application->handle()->getContent();  
		
		
 }catch(\Phalcon\Exception $e){
	 	echo "PhalconException: ", $e->getMessage();
 }
	
	
?>