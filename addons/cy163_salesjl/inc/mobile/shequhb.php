<?php
global $_W,$_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if($operation == 'display'){
	$url = trim($_GPC['url']);
	$id = intval($_GPC['goodsid']);
		
	$goods = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE id = {$id} AND weid = {$_W['uniacid']}");
	$thumb = tomedia($goods['thumb']);
	$price = $goods['normalprice'];
	$scprice = $goods['scprice'];

	$this->mkdirs(HB_ROOT_SHEQU);
	$qrcodename = HB_ROOT_SHEQU.$id.'.png';
	if(!file_exists($qrcodename)){
		include ROOT_PATH.'qrcode.class.php';    
		$value = $url; //二维码内容   
		$errorCorrectionLevel = 'L';//容错级别   
		$matrixPointSize = 6;//生成图片大小   
		//生成二维码图片
		QRcode::png($value,$qrcodename,$errorCorrectionLevel,$matrixPointSize,2); 
	}
	
	$filename = HB_ROOT_SHEQU.$id.'-hb.jpg';
	
	$data2 = array(
		'title' =>$goods['title'],
		'price_market' => '￥'.$price,
		'price_member' => '￥'.$scprice,
		'shotdes' => $goods['shotdes'],
		'goods_img' => $thumb,
		'new_img' => $filename
	);
	$this->createImg($data2,$qrcodename);
	$resarr['filename'] = $_W["siteroot"].'addons/cy163_salesjl/haibao/shequ/'.$id.'-hb.jpg?v='.TIMESTAMP;
	echo json_encode($resarr);
	exit;
}
?>