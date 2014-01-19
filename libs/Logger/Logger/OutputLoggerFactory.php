<?php

namespace Logger;

/**
 * Factory for OutputLogger
 *
 * @version    0.7
 * @package    Logger
 *
 * @author     Matěj Humpál <finwe@finwe.info>
 * @copyright  Copyright (c) 2011 Matěj Humpál
 */
class OutputLoggerFactory implements \Logger\ILoggerFactory
{
	/**
	 * @param array $options
	 * @return Logger\OutputLogger
	 */
	public static function factory(\Nette\DI\Container $container, $options = array())
	{
		return new OutputLogger($options);
	}
}
