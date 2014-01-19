<?php

require_once __DIR__ . '/../../Logger/FileLogger.php';
require_once __DIR__ . '/../../Logger/FileLoggerFactory.php';

class FileLoggerTest extends PHPUnit_Framework_TestCase
{

	private $logger;

	public function setUp()
	{
		$factory = new Logger\FileLoggerFactory();
		$this->logger = $factory->factory(array(
			'messageTemplate' => '%level% ===> %message%' . "\n",
			'dateFormat' => 'Y',
			'logDir' => __DIR__,
			'filenameMask' => 'logFile.log',
			'granularity' => 0
		));
	}

	public function testDefaultValues()
	{
		$this->assertEquals('logFile.log', $this->logger->getFilenameMask());
		$this->assertEquals(__DIR__, $this->logger->getLogDir());
		$this->assertEquals(0, $this->logger->getGranularity());
		$this->assertEquals(Logger\ILogger::INFO, $this->logger->getMinimumLogLevel());
		$this->assertEquals('Y', $this->logger->getDateFormat());
		$this->assertEquals('%level% ===> %message%' . "\n", $this->logger->getMessageTemplate());
	}

	public function testFile()
	{
		$level = Logger\ILogger::NOTICE;
		$message = 'Message';

		$this->logger->logMessage($level, $message);
		$this->assertFileEquals(__DIR__ . '/logFile.log', __DIR__ . '/expectedLogFile.log');

		unlink(__DIR__ . '/logFile.log');
	}

	public function testSetLogLevel()
	{
		$this->logger->setDefaultLogLevel(Logger\ILogger::NOTICE);
		$this->assertEquals($this->logger->getDefaultLogLevel(), Logger\ILogger::NOTICE);

		$this->logger->setDefaultLogLevel('CRITICAL');
		$this->assertEquals($this->logger->getDefaultLogLevel(), Logger\ILogger::CRITICAL);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testInvalidLogLevel()
	{
		$this->logger->setDefaultLogLevel(-1);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetInvalidLogLevelString()
	{
		$this->logger->setDefaultLogLevel('UNKNOWN');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testInvalidMinLogLevel()
	{
		$this->logger->setMinimumLogLevel(666);
	}


	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testInvalidMinLogLevelString()
	{
		$this->logger->setMinimumLogLevel('UNKNOWN');
	}


	public function testSetGranularity()
	{
		$this->logger->setGranularity(6);
		$this->assertEquals(6, $this->logger->getGranularity());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testInvalidGranularity()
	{
		$this->logger->setGranularity(-1);
	}

}