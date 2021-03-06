<?php

/**
 * AES加密解密算法
 * 
 * @category   Touzhijia
 * @package    Touzhijia_Platform_Security
 * @author     JamesQin <qinwq@touzhijia.com>
 * @copyright  (c) 2014-2016 Touzhijia Financial Information Ltd. Inc. (http://www.touzhijia.com)
 * @version    1.0.0 2016-03-30 16:43:07
 */
class Touzhijia_Platform_Security_AesCrypter
{
	private $_key     = null;
	private $_crypter = null;


	public function __construct($key, $algorithm = MCRYPT_RIJNDAEL_128, $mode = MCRYPT_MODE_CBC)
	{
		$this->_key = $key;
		$this->open($algorithm, $mode);
	}


	public function __destruct()
	{
		$this->close();
	}


	public function open($algorithm, $mode)
	{
		$this->close();
		$this->_crypter = mcrypt_module_open($algorithm, '', $mode, '');
	}


	public function encrypt($rawData)
	{
		$rawData = Touzhijia_Platform_Security_Pkcs7Encoder::encode($rawData, mcrypt_enc_get_block_size($this->_crypter));
		$iv = substr($this->_key, 0, 16);
		mcrypt_generic_init($this->_crypter, $this->_key, $iv);
		$encryptedData = mcrypt_generic($this->_crypter, $rawData);
		mcrypt_generic_deinit($this->_crypter);
		$encryptedData = base64_encode($encryptedData);

		return $encryptedData;
	}


	public function decrypt($encryptedData)
	{
		$encryptedData = base64_decode($encryptedData);
		$iv = substr($this->_key, 0, 16);
		mcrypt_generic_init($this->_crypter, $this->_key, $iv);
		$rawData = mdecrypt_generic($this->_crypter, $encryptedData);
		mcrypt_generic_deinit($this->_crypter);
		// echo Touzhijia_Util_String::strhex($rawData) . '<br>';
		$rawData = Touzhijia_Platform_Security_Pkcs7Encoder::decode($rawData);
		// echo Touzhijia_Util_String::strhex($rawData) . '<br>';

		return $rawData;
	}


	public function close()
	{
		if (!empty($this->_crypter)) {
			mcrypt_module_close($this->_crypter);
			$this->_crypter = null;
		}
	}
	
}

