<?php
	namespace Rakuten;
	
	use Rakuten\Configuration, 
		\CURL;
	
	require_once(WWW_DIR . "/../libs/Rakuten/Configuration/config.inc");
	
	class Rakuten {
		private $key;
		private $url;
		
		private $misc = 'misc';
		private $key_info = 'GetKeyInfo';
		
		private $response;
		
		function __construct(){
			$config = new \Rakuten\Configuration\Rakuten_Config();
			$this->key = $config->getConfig('test_key');
			$this->url = $config->getUrl();
			
			if (!$this->getKeyInfo()){
				var_dump($this->response);
				
				die('<hr />Rakuten api problem with key, exiting...');
			}
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
		
		function doRequest($url, $post = 1, $postfields = array()){
			// Get cURL resource
			$curl = curl_init();
			
			// Set some options - we are passing in a useragent too here
			curl_setopt_array($curl, array(
				CURLOPT_URL => $url . '?' . http_build_query($postfields),
			    CURLOPT_RETURNTRANSFER => true,
			    CURLOPT_POST => $post
			));
			
			//if (sizeof($postfields))
				//curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postfields));
			
			// Send the request & save response to $resp
			$resp = curl_exec($curl);
			
			if(!$resp){
			    die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
			}
			
			// Close request to clear up some resources
			curl_close($curl);
			
			if (!$this->processResponse($resp)){
				var_dump($this->response);
				
				die('<hr />Rakuten api error, exiting...<br />' . $this->response->errors->error->code . ' - ' . $this->response->errors->error->message);
			}
			
			return $resp;
		}
		
		function processResponse($curl_response){
			$result = $this->parseXML($curl_response);
			$this->response = $result;
			
			return $result->success == 1;
		}
		
		function parseXML($input){
			$xml = null;
			
            // allowed types checking
            if (is_file($input) || ($input instanceof SplFileInfo)) {
                $xml = simplexml_load_file($input);
            } elseif ($input instanceof SimpleXMLElement) {
                // XML already loaded
                $xml = $input;
            } else {
                $xml = @simplexml_load_string($input);
            }
			
            // XML has not been loaded
            if (!$xml) {
                throw new \Nette\IOException("Invalid input format. XML is expected.");
            }
			
            return $xml;
		}
	}

?>