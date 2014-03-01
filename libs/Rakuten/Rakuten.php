<?php
	namespace Rakuten;
	
	use Rakuten\Configuration, 
		\CURL, 
		Gateway\Utils;
	
	require_once(WWW_DIR . "/../libs/Rakuten/Configuration/config.inc");
	
	class Rakuten {
		protected $key;
		protected $url;
		
		protected $misc = 'misc';
		protected $key_info = 'GetKeyInfo';
		
		protected $response;
		
		public $connection; 
		
		function __construct($key, $domain){
			$config = new \Rakuten\Configuration\Rakuten_Config($key, $domain);
			$this->key = $config->getConfig('key');
			$this->url = $config->getUrl();
		}
		
		function getKeyInfo(){
			$url = str_replace('{group}', $this->misc, $this->url);
			$url = str_replace('{method}', $this->key_info, $url);
			$response = $this->doRequest(
				$url, 
				0, 
				array('key' => urlencode($this->key))
			);
			
			return $this->processResponse($response);
		}
		
		public function doRequest($url, $post = 1, $postfields = array()){
			// Get cURL resource
			$curl = curl_init();
			
			// Set some options - we are passing in a useragent too here
			if ($post == 1){
				curl_setopt_array($curl, array(
					CURLOPT_URL => $url,
				    CURLOPT_RETURNTRANSFER => true,
				    CURLOPT_POST => $post, 
				    CURLOPT_POSTFIELDS => http_build_query($postfields),
				    CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)'
				));
			}
			else{
				curl_setopt_array($curl, array(
					CURLOPT_URL => $url . '?' . http_build_query($postfields),
				    CURLOPT_RETURNTRANSFER => true,
				    CURLOPT_POST => $post, 
				    CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)'
				));	
			}
			
			// Send the request & save response to $resp
			$resp = curl_exec($curl);
			
			$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			
			if(curl_errno($curl))
				Utils::log("CURL Error: %s.", curl_error($curl));
			
			if(!$resp)
				Utils::log("CURL Error: %s.", curl_error($curl));
			
			// Close request to clear up some resources
			curl_close($curl);
			
			return $this->parseArray($resp);
		}
		
		function processResponse($curl_response){
			$this->response = $this->parseXML($curl_response);
		}
		
		function parseArray($input){
			return json_decode(json_encode((array)simplexml_load_string($input)), 1);
		}
		
		function parseXML($input){
			$xml = null;
			
			var_dump($input);
			
            // allowed types checking
            if (is_file($input) || ($input instanceof SplFileInfo)) {
                $xml = simplexml_load_file($input);
            } elseif ($input instanceof SimpleXMLElement) {
                // XML already loaded
                $xml = $input;
            } else {
                $xml = simplexml_load_string($input);
            }
			
            // XML has not been loaded
            if (!$xml) {
                throw new \Nette\IOException("Invalid input format. XML is expected.");
            }
			
            return $xml;
		}
		
		function xml_to_array($xml){
			
		}
		
		protected function getPath($type = false) {
	        // configuration loading
	        $params = \Nette\Environment::getContext()->params['gateway'];
	        $root = realpath($params['storage']['root']);
	
	        if (!isset($params['storage']['etron'])) {
	            throw new \Nette\InvalidArgumentException('Etron configuration not found in config.neon[params/gateway/storage/etron]');
	        }
	
	        $path = sprintf($root . $params['storage']['etron']['inputFolderMask'], $this->connection->name);
	        
	        if ($type) {
	            $path .= DIRECTORY_SEPARATOR . $type;
	        }
	        
	        return $path;
	    }
		
		function buildImageURL($image){
			$server = $_SERVER['SERVER_NAME']; 
			if ($server == 'localhost')
				$server = "cornholio.dyndns.biz";
			
			$image_path = $this->getPath('files') . DIRECTORY_SEPARATOR . $image;
			$image_path = str_replace('\\', '/', $image_path);
			$image_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $image_path);
			
			
			$image_url = 'http://' . $server . $image_path;
			return $image_url;
		}
	}

?>