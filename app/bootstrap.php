<?php

use Nette\Application\Routers\Route,
    Nette\Application\Routers\RouteList,
    Nette\Application\Routers\SimpleRouter,
    Nette\Application\Routers\CliRouter,
    Nette\Utils\Strings,
    Nette\Http\Url,
    Nette\Http\UrlScript,
    Nette\Http,
    Yourface\Application\Routers\RestRoute;

// Load Nette Framework
require __DIR__ . '/../libs/Nette/loader.php';


// Configure application
$configurator = new Nette\Config\Configurator;

// Enable Nette Debugger for error visualisation & logging
//$configurator->enableDebugger(__DIR__ . '/../log');
$email = 'nikola.badev@ec-quadrat.com';

if (isset($_SERVER['NETTE_ENVIRONMENT'])) {
	

	$productionMode= $_SERVER['NETTE_ENVIRONMENT'] == 'production' ? $configurator::PRODUCTION : $configurator::DEVELOPMENT;
	//$debugMode = $_SERVER['NETTE_ENVIRONMENT'] == 'production' ? false : true;
	$debugMode = true;
	$productionMode = $configurator::DEVELOPMENT;

	$configurator->addParameters(array(
  		"environment" => $_SERVER['NETTE_ENVIRONMENT'],
  		"productionMode" => $productionMode,
	));

	$configurator->setDebugMode($debugMode);
} else {
	$configurator->setDebugMode($configurator::PRODUCTION);
}

$configurator->enableDebugger(__DIR__ . '/../log', $email);

// Enable RobotLoader - this will load all classes automatically
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->createRobotLoader()
        ->addDirectory(__DIR__)
        ->addDirectory(__DIR__ . '/../libs/Gateway')
        ->addDirectory(__DIR__ . '/../libs/Logger')
        ->addDirectory(__DIR__ . '/../libs/Yourface')
        ->addDirectory(__DIR__ . '/../libs/NiftyGrid')
        ->addDirectory(__DIR__ . '/../libs/NetteTranslator')
        //->addDirectory(__DIR__ . '/../libs/RestClient')
        ->register();

// Create Dependency Injection container from config.neon file
$configurator->addConfig(__DIR__ . '/config.neon');

$container = $configurator->createContainer();

// FIXME nefunguje pres CLI
if ($container->params['consoleMode']) {
    /*$container->removeService('httpRequest');
    $container->addService('httpRequest', function() {
                // Podle potreby muzeme pouzit nastaveni z configu nebo vzit z parametru prikazove radky, aj.
                $uri = new UrlScript;
                $uri->scheme = 'http';
                $uri->port = Url::$defaultPorts['http'];
                $uri->host = 'localhost';
                $uri->path = '/';
                $uri->canonicalize();
                $uri->path = Strings::fixEncoding($uri->path);
                $uri->scriptPath = '/';
                return new Http\Request($uri, array(), array(), array(), array(), array(), 'GET', null, null);
            });
*/
    $container->application->allowedMethods = NULL;
    $container->router[] = new CliRouter(array('Default:execute'));
} else {
// Setup router using mod_rewrite detection
    //if (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules())) {
        $container->router[] = new Route('index.php', 'Default:default', Route::ONE_WAY);

        $container->router[] = \ApiModule\Routes::getList();
        $container->router[] = \AdminModule\Routes::getList();

        //$container->router[] = $frontRouter = new RouteList('Cron');
        $container->router[] = new Route('test/<presenter>/<action>[/<id>]', 'Default:default');
        $container->router[] = new Route('<presenter>/<action>[/<id>]', 'Admin:Default:default');
        
    /*} else {
        $container->router = new SimpleRouter('Front:Default:default');
    }*/
}

// Run the application!
$container->application->run();

