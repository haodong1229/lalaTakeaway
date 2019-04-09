<?php
global $_W,$_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if($operation == 'display'){
	$url = trim($_GPC['url']);
	$xqid = intval($_GPC['xqid']);
	
	$this->mkdirs(HB_ROOT_SHEQUINDEX);
	$qrcodename = HB_ROOT_SHEQUINDEX.$xqid.'.png';
	
	if(!file_exists($qrcodename)){
		include ROOT_PATH.'qrcode.class.php';    
		$value = $url; //二维码内容   
		$errorCorrectionLevel = 'L';//容错级别   
		$matrixPointSize = 6;//生成图片大小   
		//生成二维码图片
		QRcode::png($value,$qrcodename,$errorCorrectionLevel,$matrixPointSize,2); 
	}
	
	$filename = HB_ROOT_SHEQUINDEX.$xqid.'-hb.jpg';
	$data2 = array(
		'xqid' =>$xqid,
		'new_img' => $filename
	);
	$this->createindexImg($data2,$qrcodename);

	$resarr['filename'] = $_W["siteroot"].'addons/cy163_salesjl/haibao/shequindex/'.$xqid.'-hb.jpg?v='.TIMESTAMP;
	echo json_encode($resarr);
	exit;
}
?>