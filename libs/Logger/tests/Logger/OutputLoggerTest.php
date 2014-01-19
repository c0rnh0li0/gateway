<?php

require_once __DIR__ . '/../../Logger/OutputLogger.php';
require_once __DIR__ . '/../../Logger/OutputLoggerFactory.php';

class OutputLoggerTest extends PHPUnit_Framework_TestCase
{

	private $logger;

	public function setUp()
	{
		$factory = new Logger\OutputLoggerFactory();
		$this->logger = $factory->factory(array(
			'minimumLogLevel' => Logger\ILogger::NOTICE,
			'defaultLogLevel' => Logger\ILogger::ERROR
		));
	}

	public function testOutput()
	{
		$level = Logger\ILogger::NOTICE;
		$message = 'Message';
		$this->expectOutputRegex('~^[0-9-+:T]+ NOTICE mem\(real/peak\):[0-9\./MB]+ ===> Message$~');

		$this->logger->logMessage($level, $message);
	}

	public function testOutputImplicitLevel()
	{
		$message = 'Message';
		$this->expectOutputRegex('~^[0-9-+:T]+ ERROR mem\(real/peak\):[0-9\./MB]+ ===> Message$~');

		$this->logger->logMessage($message);
	}

	public function testOutputMessageBeginningWithInt()
	{
		$message = '5 Message';
		$this->expectOutputRegex('~^[0-9-+:T]+ ERROR mem\(real/peak\):[0-9\./MB]+ ===> 5 Message$~');

		$this->logger->logMessage($message);
	}

	public function testLevelLessThanMinimal()
	{
		$level = Logger\ILogger::DEBUG;
		$message = 'Message';
		$this->expectOutputString('');

		$this->logger->logMessage($level, $message);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testMissingMessageIntLevel()
	{
		$this->logger->logMessage(Logger\ILogger::NOTICE);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testMissingMessageStringLevel()
	{
		$this->logger->logMessage('6');
	}
}