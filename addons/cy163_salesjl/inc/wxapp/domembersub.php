<?php
global $_W,$_GPC;
$openid = trim($_GPC['openid']);
$member = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$openid}' AND weid = {$_W['uniacid']}");

$jlid = intval($_GPC['jlid']);
$memberjl = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERHUODONG)." WHERE id = {$jlid}");
$list = pdo_fetchall("SELECT * FROM ".tablename(BEST_CART)." WHERE weid = {$_W['uniacid']} AND jlid = {$jlid} AND openid = '{$member['openid']}'");
if(empty($list)){
	$this->result(1,"没有商品可以结算！", '');
}
$allprice = pdo_fetchcolumn("SELECT SUM(allprice) FROM ".tablename(BEST_CART)." WHERE weid = {$_W['uniacid']} AND jlid = {$jlid} AND openid = '{$member['openid']}'");
if($allprice >= $memberjl['manjian'] && $memberjl['manjian'] > 0){
	$yunfei = 0;
}else{
	$yunfei = $memberjl['yunfei'];
}
$pstype = intval($_GPC['pstype']);
if($pstype == 1){
	$yunfei = 0;
	$shname = $shcity = $shaddress = '';
	$shphone = trim($_GPC['shphone2']);
	if(!$this->isMobile($shphone)){
		$this->result(1,"请填写正确的手机号码！", '');
	}
	$ztdid = intval($_GPC['ztdid']);
	$ztdres = pdo_fetch("SELECT * FROM ".tablename(BEST_ZITIDIAN)." WHERE id = {$ztdid}");
	if(empty($ztdres)){
		$this->result(1,"请选择自提点！", '');
	}
	$data['remark'] = $_GPC['remark2'];
}else{
	$shname = trim($_GPC['shname']);
	$shphone = trim($_GPC['shphone']);
	$shcity = trim($_GPC['shcity']);
	$shaddress = trim($_GPC['shaddress']);
	if(empty($shname)){
		$this->result(1,"请填写收货人姓名！", '');
	}
	if(!$this->isMobile($shphone)){
		$this->result(1,"请填写正确的收货人手机号码！", '');
	}
	if(empty($shcity)){
		$this->result(1,"请选择省市！", '');
	}
	if(empty($shaddress)){
		$this->result(1,"请填写详细地址！", '');
	}
	$data['remark'] = $_GPC['remark'];
}
$allprice2 = $allprice+$yunfei;
if($pstype == 0){
	$data['address'] = $shname."|".$shphone."|".$shcity."|".$shaddress;
	$datam['shname'] = $shname;
	$datam['shphone'] = $shphone;
	$datam['shcity'] = $shcity;
	$datam['shaddress'] = $shaddress;
	pdo_update(BEST_MEMBER,$datam,array('openid'=>$member['openid']));
}else{
	$data['address'] = $shphone;
	$data['ztdid'] = $ztdid;
	$data['ztdaddress'] = $ztdres['address'];
	$data['ztdjingdu'] = $ztdres['jingdu'];
	$data['ztdweidu'] = $ztdres['weidu'];
}
$data['price'] = $allprice2;
$data['yunfei'] = $yunfei;
$data['weid'] = $_W['uniacid'];
$data['from_user'] = $member['openid'];
$data['ordersn'] = date('Ymd').random(14,1);
$data['createtime'] = TIMESTAMP;
$data['jlid'] = $jlid;
$data['isjl'] = 1;
$data['jlopenid'] = $memberjl['openid'];
$data['pstype'] = $pstype;
if(isset($this->module['config']['zftkhour'])){
	$data['cantktime'] = $memberjl['endtime'] - ($this->module['config']['zftkhour'])*3600;
}
$data['formid'] = $_GPC['formid'];
pdo_insert(BEST_ORDER, $data);
$orderid = pdo_insertid();
foreach($list as $k=>$v){
	$datao['weid'] = $_W['uniacid'];
	$datao['total'] = $v['total'];
	$datao['price'] = $v['price'];
	$datao['goodsid'] = $v['goodsid'];
	$datao['createtime'] = TIMESTAMP;
	$datao['orderid'] = $orderid;
	$datao['goodsname'] = $v['goodsname'];
	$datao['jlid'] = $jlid;
	pdo_insert(BEST_ORDERGOODS,$datao);
}
pdo_delete(BEST_CART,array('jlid'=>$jlid,'openid'=>$member['openid']));


/*
if($this->module['config']['tools'] == 1){
	pdo_update(BEST_ORDER,array('status'=>1),array('ordersn'=>$data['ordersn']));
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE ordersn = '{$data['ordersn']}' AND weid = {$_W['uniacid']}");
				
	$memberhd = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERHUODONG)." WHERE id = {$orderres['jlid']} AND weid = {$_W['uniacid']}");
	$datamemberhd['inpeople'] = $memberhd['inpeople']+1;
	pdo_update(BEST_MEMBERHUODONG,$datamemberhd,array('id'=>$orderres['jlid']));
	
	$ordergoods = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$orderres['id']}");
	foreach($ordergoods as $k=>$v){
		$goodsres = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERGOODS)." WHERE id = {$v['goodsid']} AND weid = {$_W['uniacid']}");
		$datagoods['total'] = $goodsres['total']-$v['total'];
		$datagoods['inpeople'] = $goodsres['inpeople']+1;
		pdo_update(BEST_MEMBERGOODS,$datagoods,array('id'=>$v['goodsid']));
	}
	$this->result(0,"提交订单成功！", 100);
}else{
	$this->result(0,"提交订单成功！", $data['ordersn']);
}
*/
$this->result(0,"提交订单成功！", $data['ordersn']);
?>