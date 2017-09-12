<?php

/**
 * My Application bootstrap file.
 */
use Nette\Application\Routers\Route,
	Nette\Diagnostics\Debugger,
	Nette\Forms\Container;


// Load Nette Framework or autoloader generated by Composer
require LIBS_DIR . '/autoload.php';


// Configure application
$configurator = new Nette\Config\Configurator;

// Enable Nette Debugger for error visualisation & logging
$configurator->setDebugMode("auto");
$configurator->enableDebugger(__DIR__ . '/../log');
Debugger::$strictMode = TRUE;
// Enable RobotLoader - this will load all classes automatically
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->createRobotLoader()
	->addDirectory(APP_DIR)
	->addDirectory(LIBS_DIR)
	->register();
Container::extensionMethod('addDatePicker', function (Container $container, $name, $label = NULL) {
	  return $container[$name] = new JanTvrdik\Components\DatePicker($label);
});
Nette\Forms\Controls\CheckboxList::register();
// Create Dependency Injection container from config.neon file
$configurator->addConfig(__DIR__ . '/config/config.neon');
$container = $configurator->createContainer();

// Setup router
$container->router[] = new Route('index.php', 'Homepage:default', Route::ONE_WAY);
$container->router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');

// Configure and run the application!
$container->application->run();
