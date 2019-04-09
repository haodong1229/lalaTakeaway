<?php
global $_W, $_GPC;
$member = $this->Mcheckmember();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

/*$sssss = pdo_fetchall("SELECT * FROM ".tablename(BEST_MERCHANTACCOUNT)." WHERE weid = {$_W['uniacid']} AND merchant_id = 0");
foreach($sssss as $k=>$v){
	$ooo = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$v['orderid']}");
	$ddd['merchant_id'] = $ooo['merchant_id'];
	$ddd['money'] = $ooo['alllirun'];
	pdo_update(BEST_MERCHANTACCOUNT,$ddd,array('id'=>$v['id']));
}*/ 

/*$sssss = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND status = 4 AND merchant_id > 0");
foreach($sssss as $k=>$v){
	$ddd['weid'] = $_W['uniacid'];
	$ddd['merchant_id'] = $v['merchant_id'];
	$ddd['money'] = $v['alllirun'];
	$ddd['time'] = $v['createtime'];
	$ddd['explain'] = "代理团结算";
	$ddd['orderid'] = $v['id'];
	$ddd['candotime'] = TIMESTAMP;
	pdo_insert(BEST_MERCHANTACCOUNT,$ddd);
}*/

if($operation == 'display'){
	$zitidianlist = pdo_fetchall("SELECT * FROM ".tablename(BEST_ZITIDIAN)." WHERE weid = {$_W['uniacid']} AND openid = '{$member['openid']}' AND ztdtype = 0");
	include $this->template('zitidian');
}elseif($operation == 'post'){
	$id = intval($_GPC['id']);
	if ($_GPC['isdo'] == 1) {
		$address = trim($_GPC['address']);
		if(empty($address)){
			$resArr['error'] = 1;
			$resArr['msg'] = '请填写自提点地址！';
			echo json_encode($resArr);
			exit();
		}
		$jingdu = trim($_GPC['jingdu']);
		$weidu = trim($_GPC['weidu']);
		/*if(empty($jingdu) || empty($weidu)){
			$resArr['error'] = 1;
			$resArr['msg'] = '请在地图上选中获取经度纬度！';
			echo json_encode($resArr);
			exit();
		}*/
		$data = array(
			'weid'=>$_W['uniacid'],
			'openid'=>$member['openid'],
			'address'=>$address,
			'jingdu'=>$jingdu,
			'weidu'=>$weidu,
		);
		if(!empty($id)){
			pdo_update(BEST_ZITIDIAN,$data,array('id'=>$id));
		}else{
			pdo_insert(BEST_ZITIDIAN,$data);
		}
		$resArr['error'] = 0;
		$resArr['msg'] = '操作成功！';
		echo json_encode($resArr);
		exit();
	}
	$zitidian = pdo_fetch("SELECT * FROM ".tablename(BEST_ZITIDIAN)." WHERE id = {$id}");
	include $this->template('zitidian');
}elseif($operation == 'delete'){
	$id = intval($_GPC['id']);
	$zitidian = pdo_fetch("SELECT * FROM ".tablename(BEST_ZITIDIAN)." WHERE id = {$id} AND weid = {$_W['uniacid']} AND openid = '{$member['openid']}'");
	if(empty($zitidian)){
		$resArr['error'] = 1;
		$resArr['msg'] = '抱歉，不存在该自提点！';
		echo json_encode($resArr);
		exit();
	}
	pdo_delete(BEST_ZITIDIAN,array('id'=>$id));
	$resArr['error'] = 0;
	$resArr['msg'] = '自提点删除成功！';
	echo json_encode($resArr);
	exit();
}
?>