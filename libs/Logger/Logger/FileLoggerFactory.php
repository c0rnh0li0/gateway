<?php

namespace Logger;

/**
 * Factory for FileLogger
 *
 * @version    0.7
 * @package    Logger
 *
 * @author     Matěj Humpál <finwe@finwe.info>
 * @copyright  Copyright (c) 2011 Matěj Humpál
 */
class FileLoggerFactory implements \Logger\ILoggerFactory
{
	/**
	 * @param array $options
	 * @return Logger\FileLogger
	 */
	public static function factory(\Nette\DI\Container $container, $options = array())
	{
		return new FileLogger($options);
	}
}
