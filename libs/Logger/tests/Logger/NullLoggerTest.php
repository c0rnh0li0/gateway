<?php

require_once __DIR__ . '/../../Logger/NullLogger.php';
require_once __DIR__ . '/../../Logger/NullLoggerFactory.php';

class NullLoggerTest extends PHPUnit_Framework_TestCase
{

	private $logger;

	public function setUp()
	{
		$factory = new Logger\NullLoggerFactory();
		$this->logger = $factory->factory();
	}

	public function testOutput()
	{
		$message = 'Message';
		$this->expectOutputString('');
		$this->logger->logMessage($message);
	}

}