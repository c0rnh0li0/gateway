<?php
	namespace Rakuten\Configuration;
	
	class Rakuten_Config {
		private $config;
		
		function __construct(){
			$this->config = array(
				//'url' => 'http://webservice.rakuten.de',
				'url' => 'webservice.rakuten.de',
				'url_patern' => '{url}/{version}/merchants/{group}/{method}', 
				'version' => 'v2.05', 
				'test_key' => '123456789a123456789a123456789a12', 
				'key' => '' 
			);
			
		}
		
		function setConfig($key, $value){
			if (array_key_exists($key, Rakuten_Config::config))
				$this->config[$key] = $value;
		}
		
		function getConfig($key = null){
			if ($key)
				return $this->config[$key];
			
			return $this->config;
		}
		
		function getUrl(){
			$url = str_replace('{url}', $this->config['url'], $this->config['url_patern']);
			//$url = str_replace('{version}', $this->config['version'], $url);
			$url = str_replace('{version}', '', $url);
			$url = str_replace('//', '/', $url);
			
			return $url;
		}
	}
?>