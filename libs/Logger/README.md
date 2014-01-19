Stackable logger
====================================

Logger allowing either log by selected logger or to stack loggers and log to each of them.

Originally comes from http://addons.nette.org/cs/logger by Jan Smitka and Martin Pecka

Example
-------

	$fileLogger = new FileLogger();
	$outputLogger = new OutputLogger();
	$stack = new Stack(array(
		'loggers' => array($fileLogger, $outputLogger)
	));

	$stack->logMessage('Just a casual informational message');
	$stack->logMessage(ILogger::ALERT, 'Alert, CPU load exceeded %d %%', 300);