<?php
	function cache_image( $imageurl = '',$name ){
		$imagename = $name.'.'.get_image_extension($imageurl);
		if(file_exists('./tmp/'.$imagename)){return 'tmp/'.$imagename;} 
		$image = file_get_contents_curl($imageurl); 
		file_put_contents('tmp/'.$imagename,$image);
		return 'tmp/'.$imagename;
	}
	function file_get_contents_curl($url) {
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
	    curl_setopt($ch, CURLOPT_URL, $url);
	    $data = curl_exec($ch);
	    curl_close($ch);
	 
	    return $data;
	}
	function get_image_extension($filename) {
		$ch = curl_init($filename);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_exec($ch);
		$ext = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
		$ext = explode(";",$ext);
		$ext = $ext[0];
		$ext = explode("/",$ext);
		return end($ext);
	}
	function cleanVar($retVal,$type=''){
		switch($type){
			case 'phone':
				$retVal = preg_replace("/[^0-9]/", "", $retVal);
				break;
			case 'text':
			default:
				$retVal = urldecode($retVal);
				$retVal = preg_replace("/[^A-Za-z0-9 ,']/", "", $retVal);
				break;
		}
		return $retVal;
	}
	class TwilioMMS{
		public $sid;
		public $token;
		public  function __construct($sid,$token){
			$this->sid = $sid;
			$this->token = $token;
		}
		public function sendMessage($to,$from,$body,$murl){
			$url = "https://api.twilio.com/2010-04-01/Accounts/{$this->sid}/Messages";
			$data = array();
			$data["To"]=$to;
			$data['From']=$from;
			$data['Body']=$body;
			$data['MediaUrls']=$murl;
			$ch = curl_init();
			$timeout=5;
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
			curl_setopt($ch, CURLOPT_USERPWD, "{$this->sid}:{$this->token}");
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			$output = curl_exec($ch);
			curl_close($ch);
			$output = simplexml_load_string($output);
			return $output;
		}
	}