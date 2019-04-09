<?php
global $_W, $_GPC;
$member = $this->Mcheckmember();
$merchant = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} AND openid = '{$member['openid']}'");
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
	if($merchant['istz'] == 1){
		$message = "你已经是团长了！";
		include $this->template('error');
		exit;
	}
	pdo_delete(BEST_TZORDER,array('openid'=>$merchant['openid'],'status'=>0));
	include $this->template('sqtz');
}elseif($operation == 'dosq'){
	$wxqrcode = $_GPC['wxqrcode'];
	if(empty($wxqrcode)){
		$resArr['error'] = 1;
		$resArr['message'] = "请上传微信二维码！";
		echo json_encode($resArr);
		exit;
	}
	$sqprice = $this->module['config']['tuanzhangfee'];
	if($sqprice <= 0){
		$data['istz'] = 1;
		$data['tztime'] = TIMESTAMP;
	}
	$data['wxqrcode'] = $wxqrcode;
	pdo_update(BEST_MERCHANT,$data,array('openid'=>$merchant['openid']));
	
	$datatzorder = array(
		'weid'=>$_W['uniacid'],
		'openid'=>$merchant['openid'],
		'ordersn'=>date('Ymd').random(11,1),
		'time'=>TIMESTAMP,
		'price'=>$sqprice,
	);
	if($sqprice <= 0){
		$datatzorder['status'] = 1;
	}
	pdo_insert(BEST_TZORDER,$datatzorder);

	$resArr['price'] = $sqprice;
	$resArr['error'] = 0;
	$resArr['message'] = "申请团长成功！";
	echo json_encode($resArr);
	exit;
}
?>