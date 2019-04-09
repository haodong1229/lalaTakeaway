<?php
global $_W,$_GPC;
$merchant = $this->checkmergentauth();
$id = intval($_GPC['id']);
$hdorders = pdo_fetchall("SELECT id FROM ".tablename(BEST_ORDER)." WHERE isjl = 0 AND merchant_id = {$merchant['id']} AND mhdid = {$id} AND status >= 1");
$hdordersids = array();
$hdordersids[] = 0;
foreach($hdorders as $k=>$v){
	$hdordersids[] = $v['id'];
}
$hdordergoods = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE jlid = 0 AND orderid in (".implode(',',$hdordersids).") ORDER BY mhdid ASC");
foreach($hdordergoods as $k=>$v){
	$goods = pdo_fetch("SELECT ftitle FROM ".tablename(BEST_GOODS)." WHERE id = {$v['goodsid']}");
	$hdordergoods[$k]['goodsname'] = $goods['ftitle'];
}
$goodsarr = array();
foreach($hdordergoods as $k=>$v){
	$kk = $v['goodsid'].$v['optionid'];
	if(empty($v['optionname'])){
		$goodsarr[$kk]['goodsname'] = $v['goodsname'];
	}else{
		$goodsarr[$kk]['goodsname'] = $v['goodsname'].'('.$v['optionname'].')';
	}
	$hastotal = empty($goodsarr[$kk]['total']) ? 0 : $goodsarr[$kk]['total'];
	$goodsarr[$kk]['total'] = $hastotal+$v['total'];
}


$hdorders2 = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE isjl = 0 AND merchant_id = {$merchant['id']} AND mhdid = {$id} AND status >= 1 AND ztdid = 0");
$peisongarr = array();
$pak = 0;
foreach($hdorders2 as $k=>$v){	
	$canin = 1;
	foreach($peisongarr as $kk=>$vv){
		if($v['address'] == $vv['address']){
			$canin = 0;
		}
	}
	if($canin == 1){
		$peisongarr[$pak]['address'] = $v['address'];
		$memberres = pdo_fetch("SELECT nickname FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$v['from_user']}' AND weid = {$_W['uniacid']}");
		$peisongarr[$pak]['nickname'] = $memberres['nickname'];
		$pak++;
	}
}

if(!empty($peisongarr)){
	foreach($peisongarr as $k=>$v){
		$psdorder = pdo_fetchall("SELECT id FROM ".tablename(BEST_ORDER)." WHERE isjl = 0 AND merchant_id = {$merchant['id']} AND mhdid = {$id} AND address = '{$v['address']}' AND status >= 1");
		$psordersids = array();
		$psordersids[] = 0;
		foreach($psdorder as $ka=>$va){
			$psordersids[] = $va['id'];
		}		
		$psordergoods = pdo_fetchall("SELECT goodsid,optionname,total,optionid FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid in (".implode(',',$psordersids).") ORDER BY mhdid ASC");
		foreach($psordergoods as $kk=>$vv){
			$goods = pdo_fetch("SELECT ftitle FROM ".tablename(BEST_GOODS)." WHERE id = {$vv['goodsid']}");
			$psordergoods[$kk]['goodsname'] = $goods['ftitle'];
		}
		$psgoodsarr = array();
		foreach($psordergoods as $kk=>$vv){
			$kkps = $vv['goodsid'].$vv['optionid'];
			if(empty($vv['optionname'])){
				$psgoodsarr[$kkps]['goodsname'] = $vv['goodsname'];
			}else{
				$psgoodsarr[$kkps]['goodsname'] = $vv['goodsname'].'('.$vv['optionname'].')';
			}
			$hastotal = empty($psgoodsarr[$kkps]['total']) ? 0 : $psgoodsarr[$kkps]['total'];
			$psgoodsarr[$kkps]['total'] = $hastotal+$vv['total'];
		}
		$peisongarr[$k]['goodslist'] = $psgoodsarr;
	}
}
$hdorders3 = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE isjl = 0 AND merchant_id = {$merchant['id']} AND mhdid = {$id} AND status >= 1 AND ztdid > 0");
$zitiarr = array();
$zak = 0;
foreach($hdorders3 as $k=>$v){
	$canin2 = 1;
	foreach($zitiarr as $kk=>$vv){
		if($v['address'] == $vv['address']){
			$canin2 = 0;
		}
	}
	if($canin2 == 1){
		$zitiarr[$zak]['address'] = $v['address'];
		$zitiarr[$zak]['ztdaddress'] = $v['ztdaddress'];
		$memberres = pdo_fetch("SELECT nickname FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$v['from_user']}' AND weid = {$_W['uniacid']}");
		$zitiarr[$zak]['nickname'] = $memberres['nickname'];
		$zak++;
	}
}

if(!empty($zitiarr)){
	foreach($zitiarr as $k=>$v){
		$ztdorder = pdo_fetchall("SELECT id FROM ".tablename(BEST_ORDER)." WHERE isjl = 0 AND merchant_id = {$merchant['id']} AND mhdid = {$id} AND address = '{$v['address']}' AND status >= 1");
		$ztordersids = array();
		$ztordersids[] = 0;
		foreach($ztdorder as $ka=>$va){
			$ztordersids[] = $va['id'];
		}
		$ztordergoods = pdo_fetchall("SELECT goodsid,optionname,total,optionid FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid in (".implode(',',$ztordersids).") ORDER BY mhdid ASC");
		foreach($ztordergoods as $kk=>$vv){
			$goods = pdo_fetch("SELECT ftitle FROM ".tablename(BEST_GOODS)." WHERE id = {$vv['goodsid']}");
			$ztordergoods[$kk]['goodsname'] = $goods['ftitle'];
		}
		$ztgoodsarr = array();
		foreach($ztordergoods as $kk=>$vv){
			$kkzt = $vv['goodsid'].$vv['optionid'];
			if(empty($vv['optionname'])){
				$ztgoodsarr[$kkzt]['goodsname'] = $vv['goodsname'];
			}else{
				$ztgoodsarr[$kkzt]['goodsname'] = $vv['goodsname'].'('.$vv['optionname'].')';
			}
			$hastotalzt = empty($ztgoodsarr[$kkzt]['total']) ? 0 : $ztgoodsarr[$kkzt]['total'];
			$ztgoodsarr[$kkzt]['total'] = $hastotalzt+$vv['total'];
		}
		$zitiarr[$k]['goodslist'] = $ztgoodsarr;
	}
}
include $this->template('hdpeihuodan');
?>