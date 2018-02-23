<?php

/**
* 
*/
class BaseRpcClient
{
	public static $instance = [];
	protected $method = [];
	protected $methodMap = [];
	protected $url = '';
	public $curl_timeout = 30;

	public function __call($method, $params)
	{
		if (!in_array($method, $this->method)) {
			throw new \Exception("undefined method: $method");
		}
		$data = $this->createPostData($method,!empty($params[0])?$params[0]:[]);
		$result = $this->curl($this->url,$data);
		return $this->parseResult($result);
	}

	public function curl($url, $postData)
	{
		//初始化curl
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		$result = curl_exec($ch);
		curl_close($ch);
		// $result = json_decode($result,true);
		return $result;
	}

	public function createPostData($method,$params = [])
	{
		return $params;
	}

	public function parseResult($result)
	{
		return $result;
	}

	public static function instance()
	{
		$class = get_called_class();
		if (!empty(self::$instance[$class])) {
			return self::$instance[$class];
		}
		self::$instance[$class] =  new $class();
		return self::$instance[$class];
	}
	
}