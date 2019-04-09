<?php
global $_W,$_GPC;
$member['openid'] = trim($_GPC['openid']);
$op = trim($_GPC['op']);
if($op == ''){
	$orderid = intval($_GPC['orderid']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND weid = {$_W['uniacid']} AND status = 1 AND ztdid > 0");
	
	$allgoods = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$orderid} AND hexiaonum != total");
	foreach($allgoods as $k=>$v){
		$allgoods[$k]['left'] = $v['total'] - $v['hexiaonum'];
		$allgoods[$k]['hxnum'] = 0;
	}
	$orderres['ordermember'] = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$orderres['from_user']}'");
	$orderres['addressmsg'] = explode("|",$orderres['address']);
	$resArr['allgoods'] = $allgoods;
	$resArr['orderres'] = $orderres;
	$this->result(0,"核销", $resArr);
}
if($op == 'do'){
	$orderid = intval($_GPC['orderid']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND weid = {$_W['uniacid']} AND status = 1 AND ztdid > 0");
	if (empty($orderres)) {
		$resArr['error'] = 1;
		$resArr['message'] = '抱歉，没有该订单信息！';
		$this->result(0,"核销", $resArr);
		exit();
	}
	$merchant = pdo_fetch("SELECT id,openid FROM ".tablename(BEST_MERCHANT)." WHERE id = {$orderres['merchant_id']}");
	if($member['openid'] != $merchant['openid']){
		$hexiaoyuan = pdo_fetch("SELECT * FROM ".tablename(BEST_HEXIAOYUAN)." WHERE weid = {$_W['uniacid']} AND openid = '{$member['openid']}' AND merchant_id = {$merchant['id']}");
		if (empty($hexiaoyuan)) {
			$resArr['error'] = 1;
			$resArr['message'] = '抱歉，你不是核销员！';
			$this->result(0,"核销", $resArr);
			exit();
		}
	}
	
	
	$allgoods = $this->messistr2array($_GPC['allgoods']);
	$allhxnum = 0;
	foreach($allgoods as $k=>$v){
		$hxnum = intval($v['hxnum']);
		if($hxnum > 0){
			if($hxnum > ($v['total']-$v['hexiaonum'])){
				$resArr['error'] = 1;
				$tipmsg = $v['optionname'] == '' ? '['.$v['goodsname'].']的核销数量超过限制！' : '['.$v['goodsname'].'（'.$v['optionname'].'）]的核销数量超过限制！';
				$resArr['message'] = $tipmsg;
				$this->result(0,"核销", $resArr);
				exit();
			}
		}
		$allhxnum += $hxnum;
	}
	if($allhxnum <= 0){
		$resArr['error'] = 1;
		$resArr['message'] = "核销总数量必须大于0！";
		$this->result(0,"核销", $resArr);
		exit();
	}

	foreach($allgoods as $k=>$v){
		$hxnum = intval($v['hxnum']);
		if($hxnum > 0){
			$data['hexiaonum'] = $v['hexiaonum']+$hxnum;
			pdo_update(BEST_ORDERGOODS,$data,array('id'=>$v['id']));
		}
	}
	$isallhx = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE weid = {$_W['uniacid']} AND orderid = {$orderid} AND total != hexiaonum");
	if(empty($isallhx)){
		$data2['status'] = 4;
		pdo_update(BEST_ORDER,$data2,array('id'=>$orderid));
		if($orderres['isdmfk'] == 0 && $orderres['merchant_id'] > 0){
			//利润写进代理商数据库，同时设置可提现时间
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
		$resArr['error'] = 2;
		$resArr['message'] = '核销订单成功[全部]！';
		$this->result(0,"核销成功", $resArr);
		exit();
	}else{
		$resArr['error'] = 0;
		$resArr['message'] = '核销订单成功[部分]！';
		$this->result(0,"核销成功", $resArr);
		exit();
	}		
}
?>