<?php
global $_GPC, $_W;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
	$huodonglist = pdo_fetchall("SELECT * FROM ".tablename(BEST_HUODONG)." WHERE weid = {$_W['uniacid']} ORDER BY hasteamjiang DESC,time DESC");
	$hdid = intval($_GPC['hdid']);
	if($hdid > 0){
		$huodongres = pdo_fetch("SELECT endtime FROM ".tablename(BEST_HUODONG)." WHERE weid = {$_W['uniacid']} AND id = {$hdid}");
		$teamlist = pdo_fetchall("SELECT id,name,openid FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} AND istz = 1 AND tztime < {$huodongres['endtime']}");
		foreach($teamlist as $k=>$v){
			$mhdidarr = array();
			$merchanthd = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANTHD)." WHERE weid = {$_W['uniacid']} AND merchant_id = {$v['id']} AND hdid = {$hdid}");
			if(!empty($merchanthd)){
				$mhdidarr[] = $merchanthd['id'];
			}
			$xiajilist = pdo_fetchall("SELECT id FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} AND fopenid = '{$v['openid']}'");
			foreach($xiajilist as $kk=>$vv){
				$merchanthd2 = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANTHD)." WHERE weid = {$_W['uniacid']} AND merchant_id = {$vv['id']} AND hdid = {$hdid}");
				if(!empty($merchanthd2)){
					$mhdidarr[] = $merchanthd2['id'];
				}
			}

			$teamorder = pdo_fetchall("SELECT a.dlprice,a.price,a.total FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE b.status in (1,2,4) AND a.orderid = b.id AND a.mhdid in (".implode(",",$mhdidarr).")");
			
			$alldailiprice = $allsalesprice = 0;
			foreach($teamorder as $kkk=>$vvv){
				$alldailiprice += $vvv['dlprice']*$vvv['total'];
				$allsalesprice += $vvv['price']*$vvv['total'];
			}
			$teamlist[$k]['alldailiprice'] = $alldailiprice;
			$teamlist[$k]['allsalesprice'] = $allsalesprice;
		}
	}
	include $this->template('web/teamjiang');
}elseif($operation == 'detail') {
	$hdid = intval($_GPC['hdid']);
	$fopenid = trim($_GPC['fopenid']);
	$teamlist = pdo_fetchall("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} AND (fopenid = '{$fopenid}' OR openid = '{$fopenid}')");
	
	$alldailiprice = $allsaleprice = 0;
	foreach($teamlist as $k=>$v){
		$merchanthd = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANTHD)." WHERE weid = {$_W['uniacid']} AND merchant_id = {$v['id']} AND hdid = {$hdid}");		
		$teamorder = pdo_fetchall("SELECT a.* FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE b.status in (1,2,4) AND a.orderid = b.id AND a.mhdid = {$merchanthd['id']}");
		$dailiprice = $saleprice = 0;
		foreach($teamorder as $kkk=>$vvv){
			$dailiprice += $vvv['dlprice']*$vvv['total'];
			$saleprice += $vvv['price']*$vvv['total'];
			$alldailiprice += $vvv['dlprice']*$vvv['total'];
			$allsaleprice += $vvv['price']*$vvv['total'];
		}
		$teamlist[$k]['dailiprice'] = $dailiprice;
		$teamlist[$k]['saleprice'] = $saleprice;
	}
	$teamjiang = pdo_fetch("SELECT * FROM ".tablename(BEST_HUODONGTEAMJIANG)." WHERE startmoney < {$alldailiprice} AND endmoney > {$alldailiprice} AND hdid = {$hdid} ORDER BY displayerorder ASC");
	if(empty($teamjiang)){
		$bili = 0;
	}else{
		$bili = $teamjiang['jiangli'];
	}
	
	$teamjiang2 = pdo_fetch("SELECT * FROM ".tablename(BEST_HUODONGTEAMJIANG)." WHERE startmoney < {$allsaleprice} AND endmoney > {$allsaleprice} AND hdid = {$hdid} ORDER BY displayerorder ASC");
	if(empty($teamjiang2)){
		$bili2 = 0;
	}else{
		$bili2 = $teamjiang2['jiangli'];
	}

	
	$allyj1 = ($alldailiprice*$bili)/100;
	$allyj1 = floor($allyj1*100)/100;
	$allyj2 = ($allsaleprice*$bili2)/100;
	$allyj2 = floor($allyj2*100)/100;
	
	foreach($teamlist as $k=>$v){
		$teamlist[$k]['jiang'] = ($v['dailiprice']*$bili)/100;
		$teamlist[$k]['jiang2'] = ($v['saleprice']*$bili2)/100;
	}
	
	$hasff = pdo_fetch("SELECT id,fftype,ffprice FROM ".tablename(BEST_FFJIANG)." WHERE weid = {$_W['uniacid']} AND fopenid = '{$fopenid}' AND hdid = {$hdid}");
	include $this->template('web/teamjiang');
}elseif($operation == 'fafang') {
	$ftype = intval($_GPC['ftype']);
	$hdid = intval($_GPC['hdid']);
	$hdres = pdo_fetch("SELECT endtime FROM ".tablename(BEST_HUODONG)." WHERE weid = {$_W['uniacid']} AND id = {$hdid}");
	if($hdres['endtime'] > TIMESTAMP){
		message("当前活动还未结束，暂不能结算团队奖！");
	}
	$fopenid = trim($_GPC['fopenid']);
	$hasff = pdo_fetch("SELECT id FROM ".tablename(BEST_FFJIANG)." WHERE weid = {$_W['uniacid']} AND fopenid = '{$fopenid}' AND hdid = {$hdid}");
	if(!empty($hasff)){
		message("该团队奖已经发放！");
	}
	$tzmerchant = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} AND openid = '{$fopenid}'");
	$teamlist = pdo_fetchall("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} AND (fopenid = '{$fopenid}' OR openid = '{$fopenid}')");
	
	$alldailiprice = $allsaleprice = 0;
	foreach($teamlist as $k=>$v){
		$merchanthd = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANTHD)." WHERE weid = {$_W['uniacid']} AND merchant_id = {$v['id']} AND hdid = {$hdid}");
		$teamorder = pdo_fetchall("SELECT a.*,b.status FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE b.status in (1,2,4) AND a.orderid = b.id AND a.mhdid = {$merchanthd['id']}");
		foreach($teamorder as $kkk=>$vvv){
			if($vvv['status'] == 1 || $vvv['status'] == 2){
				message("当前团队订单中还有未完成的支付订单，暂不能结算团队奖！");
			}
			$alldailiprice += $vvv['dlprice']*$vvv['total'];
			$allsaleprice += $vvv['price']*$vvv['total'];
		}
	}
	$teamjiang = pdo_fetch("SELECT * FROM ".tablename(BEST_HUODONGTEAMJIANG)." WHERE startmoney < {$alldailiprice} AND endmoney > {$alldailiprice} AND hdid = {$hdid} ORDER BY displayerorder ASC");
	if(empty($teamjiang)){
		$bili = 0;
	}else{
		$bili = $teamjiang['jiangli'];
	}
	
	$teamjiang2 = pdo_fetch("SELECT * FROM ".tablename(BEST_HUODONGTEAMJIANG)." WHERE startmoney < {$allsaleprice} AND endmoney > {$allsaleprice} AND hdid = {$hdid} ORDER BY displayerorder ASC");
	if(empty($teamjiang2)){
		$bili2 = 0;
	}else{
		$bili2 = $teamjiang2['jiangli'];
	}
	
	$allyj1 = ($alldailiprice*$bili)/100;
	$allyj1 = floor($allyj1*100)/100;
	$allyj2 = ($allsaleprice*$bili2)/100;
	$allyj2 = floor($allyj2*100)/100;
	
	if($ftype == 1){
		$fafangprice = $allyj1;
	}else{
		$fafangprice = $allyj2;
	}
	
	$data['weid'] = $_W['uniacid'];
	$data['hdid'] = $hdid;
	$data['fopenid'] = $fopenid;
	$data['teamid'] = $tzmerchant['id'];
	$data['dlprice'] = $allyj1;
	$data['dlper'] = $bili;
	$data['salesprice'] = $allyj2;
	$data['salesper'] = $bili2;
	$data['fftype'] = $ftype;
	$data['ffprice'] = $fafangprice;
	$data['time'] = TIMESTAMP;
	pdo_insert(BEST_FFJIANG,$data);
	$tdjid = pdo_insertid();
	
	if($fafangprice > 0){
		$datamerchant['weid'] = $_W['uniacid'];
		$datamerchant['merchant_id'] = $tzmerchant['id'];
		$datamerchant['money'] = $fafangprice;
		$datamerchant['time'] = TIMESTAMP;
		$datamerchant['explain'] = '团队奖结算';
		$datamerchant['tdjid'] = $tdjid;
		pdo_insert(BEST_MERCHANTACCOUNT,$datamerchant);
	}
	message('团队奖发放成功！', $this->createWebUrl('teamjiang', array('op' => 'detail','hdid'=>$hdid,'fopenid'=>$fopenid)), 'success');
}
?>