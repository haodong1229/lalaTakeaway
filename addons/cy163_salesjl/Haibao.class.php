<?php

class Haibao{ 

	/**
	 * 添加图片
	 * @param $path_base    原图
	 * @param $path_logo     添加图
	 * @param $imgWidth     添加图宽
	 * @param $imgHeight     添加图高
	 * @param $dst_x         在原图宽x处添加
	 * @param $dst_y         在原图高y处添加
	 * @param $new             生成图
	 * @return resource    返回图片image资源
	 */
	public function addPic($path_base,$path_logo,$imgWidth,$imgHeight,$dst_x,$dst_y,$new){

		$image_base = $this->ImgInfo($path_base);
		$image_logo = $this->ImgInfo($path_logo);

		imagecopyresampled($image_base, $image_logo, $dst_x, $dst_y, 0, 0,$imgWidth,$imgHeight,imagesx($image_logo), imagesy($image_logo));

		// 生成一个合并后的新图
		imagejpeg($image_base,$new);
		// 载入新图像资源
		$new_pic = imagecreatefromjpeg($new);
		// 生成写入文字的的新图
		imagejpeg($new_pic,$new);

	}

	/**
	 * 添加文字
	 * @param $str        要添加的文字
	 * @param $posX     在宽x处添加
	 * @param $poxY     在高y处添加
	 * @param $font     字体大小
	 * @param $color     字体颜色
	 * @param $new         生成图
	 * @return resource    返回图片image资源
	 */
	public function addWord($str,$posX,$poxY,$font,$color,$ziti,$new)
	{
		$ori_img = $new;    //原图
		$new_img = $new;    //生成水印后的图片
		
		$s_original = $this->ImgInfo($ori_img);
		$tilt = 0;    //文字的倾斜度
		$ImgColor = [                    //为一幅图像分配颜色 
			'black' => imagecolorallocate($s_original,0,0,0),
			'red' => imagecolorallocate($s_original,255,0,0),
			'hui' => imagecolorallocate($s_original,153,153,153),
			'red2' => imagecolorallocate($s_original,251,101,86),
		] ;
		
		imagettftext($s_original, $font, $tilt, $posX, $poxY, $ImgColor[$color], $ziti, $str);    
		
		$loop = imagejpeg($s_original, $new_img);    //生成新的图片(jpg格式)

	}
	
	
	
		/**
	 * 从图片文件创建Image资源
	 * @param $file 图片文件，支持url
	 * @return bool|resource    成功返回图片image资源，失败返回false
	 */
	public function ImgInfo($img){
		if(preg_match('/http(s)?:\/\//',$img)){
			$fileSuffix = $this->getNetworkImgType($img);
		}else{
			$fileSuffix = pathinfo($img, PATHINFO_EXTENSION);
		}

		if(!$fileSuffix) return false;

		switch ($fileSuffix){
			case 'jpeg':
				$theImage = @imagecreatefromjpeg($img);
				break;
			case 'jpg':
				$theImage = @imagecreatefromjpeg($img);
				break;
			case 'png':
				$theImage = @imagecreatefrompng($img);
				break;
			case 'gif':
				$theImage = @imagecreatefromgif($img);
				break;
			default:
				$theImage = @imagecreatefromstring(file_get_contents($img));
				break;
		}
	 
		return $theImage;
	}

	/**
	 * 获取网络图片类型
	 * @param $url  网络图片url,支持不带后缀名url
	 * @return bool
	 */
	public function getNetworkImgType($url){
		$ch = curl_init(); //初始化curl
		curl_setopt($ch, CURLOPT_URL, $url); //设置需要获取的URL
		curl_setopt($ch, CURLOPT_NOBODY, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //支持https
		curl_exec($ch);//执行curl会话
		$http_code = curl_getinfo($ch);//获取curl连接资源句柄信息
		curl_close($ch);//关闭资源连接
	 
		if ($http_code['http_code'] == 200) {
			$theImgType = explode('/',$http_code['content_type']);
	 
			if($theImgType[0] == 'image'){
				return $theImgType[1];
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	 * 分行连续截取字符串
	 * @param $str  需要截取的字符串,UTF-8
	 * @param int $row  截取的行数
	 * @param int $number   每行截取的字数，中文长度
	 * @param bool $suffix  最后行是否添加‘...’后缀
	 * @return array    返回数组共$row个元素，下标1到$row
	 */
	public function cn_row_substr($str,$row = 1,$number = 10,$suffix = true){
		$result = array();
		for ($r=1;$r<=$row;$r++){
			$result[$r] = '';
		}
	 
		$str = trim($str);
		if(!$str) return $result;
	 
		$theStrlen = strlen($str);
	 
		//每行实际字节长度
		$oneRowNum = $number * 3;
		for($r=1;$r<=$row;$r++){
			if($r == $row and $theStrlen > $r * $oneRowNum and $suffix){
				$result[$r] = $this->mg_cn_substr($str,$oneRowNum-6,($r-1)* $oneRowNum).'...';
			}else{
				$result[$r] = $this->mg_cn_substr($str,$oneRowNum,($r-1)* $oneRowNum);
			}
			if($theStrlen < $r * $oneRowNum) break;
		}
	 
		return $result;
	}

	/**
	 * 按字节截取utf-8字符串
	 * 识别汉字全角符号，全角中文3个字节，半角英文1个字节
	 * @param $str  需要切取的字符串
	 * @param $len  截取长度[字节]
	 * @param int $start    截取开始位置，默认0
	 * @return string
	 */
	public function mg_cn_substr($str,$len,$start = 0){
		$q_str = '';
		$q_strlen = ($start + $len)>strlen($str) ? strlen($str) : ($start + $len);
	 
		//如果start不为起始位置，若起始位置为乱码就按照UTF-8编码获取新start
		if($start and json_encode(substr($str,$start,1)) === false){
			for($a=0;$a<3;$a++){
				$new_start = $start + $a;
				$m_str = substr($str,$new_start,3);
				if(json_encode($m_str) !== false) {
					$start = $new_start;
					break;
				}
			}
		}
	 
		//切取内容
		for($i=$start;$i<$q_strlen;$i++){
			//ord()函数取得substr()的第一个字符的ASCII码，如果大于0xa0的话则是中文字符
			if(ord(substr($str,$i,1))>0xa0){
				$q_str .= substr($str,$i,3);
				$i+=2;
			}else{
				$q_str .= substr($str,$i,1);
			}
		}
		return $q_str;
	}
}

?>