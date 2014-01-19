<?php

namespace Logger;

/**
 * Factory for FileLogger
 *
 * @version    0.7
 * @package    Logger
 *
 * @author Daniel Milde <daniel@milde.cz>
 */
interface ILoggerFactory
{
	/**
	 * @param array $options
	 * @return Logger\ILogger
	 */
	public static function factory(\Nette\DI\Container $container, $options = array());
}
