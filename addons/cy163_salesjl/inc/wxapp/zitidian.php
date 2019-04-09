<?php
global $_W,$_GPC;
$openid = trim($_GPC['openid']);
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if($operation == 'display'){	
	$zitidianlist = pdo_fetchall("SELECT * FROM ".tablename(BEST_ZITIDIAN)." WHERE weid = {$_W['uniacid']} AND openid = '{$openid}' AND ztdtype = 0");
	$this->result(0,"自提点", $zitidianlist);
}elseif($operation == 'edit'){
	$id = intval($_GPC['id']);
	$zitidian = pdo_fetch("SELECT * FROM ".tablename(BEST_ZITIDIAN)." WHERE id = {$id} AND openid = '{$openid}'");
	$this->result(0,"自提点详情", $zitidian);
}elseif($operation == 'doedit'){
	$id = intval($_GPC['id']);
	$address = trim($_GPC['address']);
	if(empty($address)){
		$this->result(1,"请填写自提点地址！", '');
	}
	$jingdu = trim($_GPC['jingdu']);
	$weidu = trim($_GPC['weidu']);
	if(empty($jingdu) || empty($weidu)){			
		$this->result(1,"请在地图上选中获取经度纬度！", '');
	}
	$data = array(
		'weid'=>$_W['uniacid'],
		'openid'=>$openid,
		'address'=>$address,
		'jingdu'=>$jingdu,
		'weidu'=>$weidu,
		'ztdtype'=>intval($_GPC['ztdtype']),
	);
	if(!empty($id)){
		pdo_update(BEST_ZITIDIAN,$data,array('id'=>$id));
	}else{
		pdo_insert(BEST_ZITIDIAN,$data);
	}
	$formid = $_GPC['formid'];
	if($formid != ""){
		$data2['weid'] = $_W['uniacid'];
		$data2['formvalue'] = $formid;
		$data2['time'] = TIMESTAMP+7*3600*24;
		pdo_insert(BEST_FORMID,$data2);
	}
	$this->result(0,"操作成功！", '');
}elseif($operation == 'delete'){
	$id = intval($_GPC['id']);
	$zitidian = pdo_fetch("SELECT * FROM ".tablename(BEST_ZITIDIAN)." WHERE id = {$id} AND weid = {$_W['uniacid']} AND openid = '{$openid}'");
	if(empty($zitidian)){
		$this->result(1,"抱歉，不存在该自提点！", '');
	}
	pdo_delete(BEST_ZITIDIAN,array('id'=>$id));
	$this->result(0,"自提点删除成功！", '');
}
?>