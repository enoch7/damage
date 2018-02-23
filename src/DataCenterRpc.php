<?php
require_once __DIR__ . "/BaseRpcClient.php";
/**
* 
*/
class DataCenterRpc extends BaseRpcClient
{
	protected $url = 'localhost:9900';

	private $cipher = 'aes-128-cbc';
	private $aesKey = 'aeskey';
	private $_privateKey = '';
	private $_publicKey = '';

	protected $method = ['getUserList'];

	protected $methodMap = [
		'getUserList' => 'User.getUserList',
	];

	public function __construct()
	{
		$this->_privateKey = dirname(__DIR__).'/keys/rsa_private_key.pem';
		$this->_publicKey = dirname(__DIR__).'/keys/rsa_public_key.pem';
	}


	public function createPostData($call,$args = [])
	{
		if (is_array($args)) {
			$args = json_encode($args);
		}
		list($args, $iv) = $this->aesEncrypt($args);
		$sign = $this->generateSign($args);
		
		$data = [
			'iv' => $iv,
			'method' => $call,
			'args' => $args,
			'sign' => $sign,
			'timestamp' => time(),
		];
		return $data;	
	}

	public function parseResult($result)
	{
		$res = json_decode($result,true);
		// $ok = $this->verifySign(base64_decode($res['args']),base64_decode($res['sign']));

		$res = $this->aesDecrypt(base64_decode($res['args']),base64_decode($res['iv']));

		return $res;
	}


	public function aesDecrypt($data,$iv)
	{
		$result = openssl_decrypt($data, $this->cipher, $this->aesKey, true,$iv);
		return $result;
	}

	public function aesEncrypt($string)
	{
		$ivlen = openssl_cipher_iv_length($this->cipher);
		$iv = openssl_random_pseudo_bytes($ivlen);
		$secretData = openssl_encrypt($string, $this->cipher, $this->aesKey, true,$iv);
		return [$secretData,$iv];
	}

	public function rsaEncrept($string)
	{


	}

	public function rsaDecrypt($data)
	{

	}

	public function generateSign($string)
	{
		$sign = '';

		$priv_key_id = openssl_get_privatekey(file_get_contents($this->_privateKey));
		openssl_sign($string, $sign, $priv_key_id, OPENSSL_ALGO_SHA256);
		return $sign;

	}

	public function verifySign($data,$sign)
	{
		$pub_key_id = openssl_get_publickey(file_get_contents($this->_publicKey));
		return openssl_verify($data, $sign, $pub_key_id,OPENSSL_ALGO_SHA256);
	}
	
}