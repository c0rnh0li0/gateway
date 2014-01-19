<?php

require_once __DIR__ . '/../Stack.php';
require_once __DIR__ . '/../StackFactory.php';

class StackTest extends PHPUnit_Framework_TestCase
{

	private $factory;

	public function setUp()
	{
		$this->factory = new Logger\StackFactory();
	}

	public function testLogIteration()
	{
		$nullMock = $this->getMock('Logger\NullLogger', array('logMessage'));
		$nullMock->expects($this->once())
			->method('logMessage');

		$outputMock = $this->getMock('Logger\OutputLogger', array('logMessage'));
		$outputMock->expects($this->once())
			->method('logMessage');

		$logger = $this->factory->factory(array('loggers' => array(
				$nullMock,
				$outputMock
		)));

		$level = Logger\ILogger::NOTICE;
		$message = 'Message';

		$logger->logMessage($level, $message);
	}

	public function testLogAdd()
	{
		$nullMock = $this->getMock('Logger\NullLogger', array('logMessage'));
		$nullMock->expects($this->once())
			->method('logMessage');

		$outputMock = $this->getMock('Logger\OutputLogger', array('logMessage'));
		$outputMock->expects($this->once())
			->method('logMessage');

		$logger = $this->factory->factory(array('loggers' => array(
				$nullMock,
		)));

		$logger->addLogger($outputMock);

		$level = Logger\ILogger::NOTICE;
		$message = 'Message';

		$logger->logMessage($level, $message);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testInvalidSetLoggers()
	{
		$logger = $this->factory->factory(array('loggers' => array()));
		$logger->setLoggers(array(new stdClass()));
	}

}