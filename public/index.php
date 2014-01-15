<?php
	/**
	 * make Date : 2014.1.15
	 * make man : chomark
	 * make tools : phalcon
	 * * */
	 $config = new Phalcon\Config\Adapter\Ini('../app/config/config.ini');
	 
	 //로더 설정
	 $load = new \Phalcon\Loader();
	 $load->registerDirs(
			 	array(
			 		$config->application->controllersDir,
			 		$config->application->pluginsDir,
			 		$config->application->libraryDir,
			 		$config->application->modelsDir,
				)
			 )->register();
	 
	$app = new \Phalcon\Mvc\Application($di);
	echo $app->handle()->getContent(); 
	
?>