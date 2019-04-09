<?php
global $_W,$_GPC;
$member['openid'] = trim($_GPC['openid']);
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if($operation == 'display'){
	$fromopenid = trim($_GPC["fromopenid"]);
	$conditions = "weid = {$_W['uniacid']} AND isjl = 0 AND from_user = '{$fromopenid}' AND status = 1 AND pstype = 1";
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_ORDER)." WHERE ".$conditions);
	$allpage = ceil($total/10)+1;
	$page = intval($_GPC["page"]);
	$pindex = max(1, $page);
	$psize = 10;
	$orderlist = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE ".$conditions." ORDER BY createtime DESC LIMIT ".($pindex - 1)*$psize.",".$psize);
	foreach($orderlist as $k=>$v){
		$orderlist[$k]['gnum'] = pdo_fetchcolumn("SELECT SUM(total) FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$v['id']}");
		$orderlist[$k]['goodslist'] = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$v['id']}");
	}
	$data['orderlist'] = $orderlist;
	$data['allpage'] = $allpage;
	$this->result(0,"我的订单", $data);
}elseif($operation == 'hexiao'){
	$orderid = intval($_GPC['id']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND weid = {$_W['uniacid']} AND status = 1");
	if(empty($orderres)){
		$resArr['error'] = 1;
		$resArr['message'] = '抱歉，没有该订单信息！';
		$this->result(0,"不存在该订单！", $resArr);
		exit;
	}
	$member['openid'] = trim($_GPC['openid']);
	$merchant = pdo_fetch("SELECT id,openid FROM ".tablename(BEST_MERCHANT)." WHERE id = {$orderres['merchant_id']}");
	if($member['openid'] != $merchant['openid']){
		$hexiaoyuan = pdo_fetch("SELECT * FROM ".tablename(BEST_HEXIAOYUAN)." WHERE weid = {$_W['uniacid']} AND openid = '{$member['openid']}' AND merchant_id = {$merchant['id']}");
		if (empty($hexiaoyuan)) {
			$resArr['error'] = 1;
			$resArr['message'] = '抱歉，你不是核销员！';
			$this->result(0,"不存在该订单！", $resArr);
			exit;
		}
	}
	$data['status'] = 4;
	pdo_update(BEST_ORDER,$data,array('id'=>$orderid));
	if($orderres['isdmfk'] == 0){
		//利润写进团长数据库，同时设置可提现时间
		$hasmerchantaccount = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANTACCOUNT)." WHERE merchant_id = {$orderres['merchant_id']} AND orderid = {$orderres['id']}");
		if(empty($hasmerchantaccount)){
			$datamerchant['weid'] = $_W['uniacid'];
			$datamerchant['merchant_id'] = $orderres['merchant_id'];
			$datamerchant['money'] = $orderres['alllirun'];
			$datamerchant['time'] = TIMESTAMP;
			$datamerchant['explain'] = '代理团结算';
			$datamerchant['orderid'] = $orderres['id'];
			$datamerchant['candotime'] = TIMESTAMP + ($this->module['config']['dltxhour'])*3600;
			pdo_insert(BEST_MERCHANTACCOUNT,$datamerchant);
		}
	}
	$resArr['error'] = 0;
	$resArr['message'] = '核销成功！';
	$this->result(0,"不存在该订单！", $resArr);
}
?>