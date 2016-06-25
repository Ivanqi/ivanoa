<?php
	use Phalcon\Loader;
	use Phalcon\Logger;
	use Phalcon\Mvc\View;
	use Phalcon\Di\FactoryDefault;
	use Phalcon\Session\Adapter\Files as Session;
	use Phalcon\Mvc\Dispatcher;
	use Phalcon\Flash\Session as FlashSession;
	use Phalcon\Http\Response\Cookies;
	use Phalcon\Logger\Adapter\File as FileLogger;

	try{

		if(isset($_GET['_url'])){
			$_GET['_url'] = str_replace('.html','',$_GET['_url']);
		}

		define('APP_PATH',dirname(__FILE__).'/');
		define('CONF_PATH',APP_PATH.'config/');

		require APP_PATH.'library/Common.php';
		require APP_PATH.'library/functions.php';
		C(include APP_PATH.'config/config.php');

		//自动载入
		$loader = new Loader();
		$namespace = include APP_PATH.'config/namespace.php';
		$loader->registerNamespaces($namespace)->register();

		//自动载入文件夹
		$loader->registerDirs([
			APP_PATH.'controllers/',
			APP_PATH.'models/',
			APP_PATH.'views/',
			APP_PATH.'library/'
		]);

		//依赖注入
		$di = new FactoryDefault();

		//设置模板
		$di->set('view', function(){
	        $view = new \Phalcon\Mvc\View();
	        $view->setViewsDir(APP_PATH.'/views/');
	        return $view;
    	});

    	$di->set('profiler', function(){
		    return new \Phalcon\Db\Profiler();
		}, true);

		$config = C('database');

		//设置数据库连接
		$di->set('db',function () use ($di,$config){

			//新建一个事件管理器
			$eventsManager 	=	new Phalcon\Events\Manager();
			$eventsManager 		=	$di->getShared('eventsManager');

			//从di中获取共享的profiler实例
			$profiler 	=	$di->getProfiler();
			$logger 	=	new FileLogger(dirname(__FILE__).'/logs/mysql.log');

			//监听所有db事件
			$eventsManager->attach('db',function($event,$connection) use($profiler,$logger){
				//一条语句查询之前，profiler开始记录sql语句
				if($event->getType() == 'beforeQuery'){
					$profiler->startProfile($connection->getSQLStatement());
					$logger->log($connection->getSQLStatement(),Logger::INFO);
				}
				//一条语句查询结束，结束本次记录，记录结果会保存在profiler对象中
				if($event->getType() == 'afterQuery'){
					$profiler->stopProfile();
				}
			});

			$dbclass = 'Phalcon\Db\Adapter\Pdo\\'.$config['adapter'];
			$connection = new $dbclass([
				'host' 		=>	$config['host'],
				'username' 	=>	$config['username'],
				'password' 	=>	$config['password'],
				'dbname'	=>	$config['dbname'],
				'charset'	=> 	$config['charset']
			]);

			//将事件管理器绑定到db实例中
    		$connection->setEventsManager($eventsManager);

    		return $connection;
		});

		//设置sesion
		$di->set('session',function(){
			ini_set('session.gc.maxlifetime',86400);	//session的生命周期
			ini_set('session.use_cookies',1);			//使用cookie保存session id 的方式
			ini_set('session.cookie_path', '/'); 		//多主机共享保存 SESSION ID 的 COOKIE

			$session 	=	new Session();
			$session->start();
			return $session;
		});

		//设置cookie
		$di->set('cookies',function(){
			$cookies 	=	new Cookies();
			//存储cookies要加密、解密
			$cookies->useEncryption(false);
			return $cookies;
		});

		//设置flash
		$di->set('flash',function(){
			return new FlashSession([
				'error'		=>	'alter alter-danger',
				'success'	=>	'alter alter-success',
				'notice'	=>	'alter alter-info'
			]);
		});

		//设置url
		$di->set('url',function(){
			$url 	=	\Phalcon\Mvc\Url();
			return $url;
		});

		//设置dispatcher
		$di->set('dispatcher',function(){
			$dispatcher 	=	new Dispatcher();
			return $dispatcher;
		});

		//设置路由
		$di->set('router',function(){
        	$router = new \Phalcon\Mvc\Router();
        	$router->add('/',[
	            "controller"=>'index',
	            "action"=>'index'
        	]);
        	//$router->notFound(array('controller'=>'error','action'=>'404'));
        	return $router;
   		});

		//Handle the request
    	$application = new \Phalcon\Mvc\Application($di);

    	echo $application->handle()->getContent();


	}catch(\Phalcon\Exception $e){
		echo "PhalconException: ", $e->getMessage();
	}
?>