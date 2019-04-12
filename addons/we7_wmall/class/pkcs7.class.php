<?php  class PKCS7Encoder 
{
	public static $block_size = 16;
	public function encode($text) 
	{
		$block_size = PKCS7Encoder::$block_size;
		$text_length = strlen($text);
		$amount_to_pad = PKCS7Encoder::$block_size - $text_length % PKCS7Encoder::$block_size;
		if( $amount_to_pad == 0 ) 
		{
			$amount_to_pad = PKCS7Encoder::block_size;
		}
		$pad_chr = chr($amount_to_pad);
		$tmp = "";
		for( $index = 0; $index < $amount_to_pad; $index++ ) 
		{
			$tmp .= $pad_chr;
		}
		return $text . $tmp;
	}
	public function decode($text) 
	{
		$pad = ord(substr($text, -1));
		if( $pad < 1 || 32 < $pad ) 
		{
			$pad = 0;
		}
		return substr($text, 0, strlen($text) - $pad);
	}
}
class Prpcrypt 
{
	public $key = NULL;
	public function __construct($k) 
	{
		$this->key = $k;
	}
	public function decrypt($aesCipher, $aesIV = "") 
	{
		try 
		{
			if( empty($aesIV) ) 
			{
				$mcrypt_mode = MCRYPT_MODE_ECB;
			}
			else 
			{
				$mcrypt_mode = MCRYPT_MODE_CBC;
			}
			$module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, "", $mcrypt_mode, "");
			@mcrypt_generic_init($module, $this->key, $aesIV);
			$decrypted = mdecrypt_generic($module, $aesCipher);
			mcrypt_generic_deinit($module);
			mcrypt_module_close($module);
		}
		catch( Exception $e ) 
		{
			return array( -41003, NULL );
		}
		try 
		{
			$pkc_encoder = new PKCS7Encoder();
			$result = $pkc_encoder->decode($decrypted);
		}
		catch( Exception $e ) 
		{
			return array( -41003, NULL );
		}
		return array( 0, $result );
	}
}
?>