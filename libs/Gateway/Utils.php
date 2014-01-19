<?php

namespace Gateway;

/**
 * Description of Utils
 *
 * @author Lukas Bruha
 */
class Utils {

    /**
     * Connection logger instance.
     * 
     */        
    protected static $logger;
    
    /**
     * Connections logger path.
     * 
     * @var string
     */
    protected static $connLogDir = false;

    /**
     * Generates unique filename based on timestamp.
     * 
     * @param string $extension
     * @return string
     */
    public static function generateFilename($extension) {
        $extension = self::getFileExtension($extension);
        
        return date("YmdHis") . "." . $extension;
    }

    /**
     * Extracts filename extension.
     * 
     * @param string $fileName
     * @return string
     */
    public static function getFileExtension($fileName) {
        $parts = explode(".", $fileName);
        
        return $parts[count($parts) - 1];
    }
    
    /**
     * Gateway log wrapper.
     * 
     * @throws \Nette\IOException
     */
    public static function log() {        
        // log to gateway logger only if connection is known
        if (self::$connLogDir) {
            // callable object and method
            $method = array(self::getLogger(), 'logMessage');

            if (!is_callable($method)) {
                throw new \Nette\IOException('Defined logger method is not callable.');
            }
        } else {
            // else log to global logger
            $method = array(\Nette\Environment::getService("FileLogger"), 'logMessage');
        }

        // callback to log
        call_user_func_array($method, func_get_args());        
    }
    
    /**
     * Sets logger.
     * 
     * @param mixed $logger
     */
    public static function setLogger($logger) {
        self::$logger = $logger;
    }
    
    /**
     * Returns logger.
     * 
     * @return mixed
     */
    public static function getLogger() {
        if (!self::$logger) {
            self::$logger = \Nette\Environment::getService("GatewayLogger");
        }
        
        return self::$logger;
    }
    
    /**
     * According to connection name it creates directory
     * for connection specific log.
     * 
     * @param string $conn
     */
    public static function setConnectionLogDir($conn) {
        $connLogDir = self::getConnectionLogDir($conn);
        
        if (!is_dir($connLogDir)) {
            self::mkDir($connLogDir);
        }
         
        self::getLogger()->setLogDir($connLogDir);        
        
        self::$connLogDir = $connLogDir;
    }
    
    /**
     * According to given connection name it returns connection log string.
     * 
     * @param string $conn
     * @return string
     */
    public static function getConnectionLogDir($conn) {
        //$globalLogDir = realpath(self::getLogger()->getLogDir());
        $globalLogDir = self::getLogger()->getLogDir();
        
        $connLogDir = $globalLogDir;
        
        if (!is_dir($globalLogDir)) {
            self::mkDir($globalLogDir);
	}
        
        // avoid adding connection name multiple times
        if (!\Nette\Utils\Strings::endsWith($connLogDir, $conn)) {
            $connLogDir = $connLogDir . DIRECTORY_SEPARATOR . $conn;
            
            $connLogDir .= DIRECTORY_SEPARATOR . date('Y/m/d');
            
            if (!is_dir($connLogDir)) {
                self::mkDir($globalLogDir);
            }
        }
        
        return $connLogDir;
    }    
    
    /**
     * Creates requested directory if not exists.
     * 
     * @param string $dir
     * @throws \Nette\IOException
     */
    public static function mkDir($dir) {
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0777, true)) {
                throw new \Nette\IOException(sprintf("Cannot create folder of '%s'. Please, check permissions.", $dir));
            }
        }
    }
}
