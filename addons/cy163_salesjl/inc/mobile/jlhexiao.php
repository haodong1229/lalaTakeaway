<?php
global $_W, $_GPC;
$member = $this->Mcheckmember();
$orderid = intval($_GPC['orderid']);
$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND weid = {$_W['uniacid']} AND status = 1");
if (empty($orderres)) {
	$resArr['error'] = 1;
	$resArr['message'] = '抱歉，没有该订单信息！';
	echo json_encode($resArr);
	exit();
}
if($member['openid'] != $orderres['jlopenid']){
	$hexiaoyuan = pdo_fetch("SELECT * FROM ".tablename(BEST_HEXIAOYUAN)." WHERE openid = '{$member['openid']}' AND fopenid = '{$orderres['jlopenid']}'");
	if (empty($hexiaoyuan)) {
		$message = '抱歉，你不是核销员！';
		include $this->template('error');
		exit;
	}
}

$data['status'] = 4;
pdo_update(BEST_ORDER,$data,array('id'=>$orderid));
//利润写进代理商数据库，同时设置可提现时间
$hasmemberaccount = pdo_fetch("SELECT id FROM ".tablename(BEST_MEMBERACCOUNT)." WHERE openid = '{$orderres['jlopenid']}' AND orderid = {$orderres['id']}");
if(empty($hasmemberaccount)){
	$datamoney['weid'] = $_W['uniacid'];
	$datamoney['openid'] = $orderres['jlopenid'];
	$datamoney['money'] = $orderres['price'];
	$datamoney['time'] = TIMESTAMP;
	$datamoney['orderid'] = $orderres['id'];
	$datamoney['explain'] = "接龙订单";
	$datamoney['candotime'] = TIMESTAMP + ($this->module['config']['zftxhour'])*3600;
	pdo_insert(BEST_MEMBERACCOUNT,$datamoney);
}
$message = '核销订单成功！';
include $this->template('error');
?>