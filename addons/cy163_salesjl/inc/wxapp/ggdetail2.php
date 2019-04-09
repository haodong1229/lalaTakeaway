<?php
global $_W,$_GPC;
$openid = trim($_GPC['openid']);
$member = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$openid}' AND weid = {$_W['uniacid']}");
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if($operation == 'display'){	
	$id = intval($_GPC['id']);
		
	$goods = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE id = {$id} AND weid = {$_W['uniacid']}");
	if($goods['hasoption'] == 1){
		$goods['options'] = pdo_fetchall("SELECT * FROM ".tablename(BEST_GOODSOPTION)." WHERE goodsid = {$goods['id']}");
	}
	$goodsthumbsarr[0] = $goods['sharethumb'] = tomedia($goods['thumb']);
	$thumbs = unserialize($goods['thumbs']);
	foreach($thumbs as $k=>$v){
		array_push($goodsthumbsarr,tomedia($v));
	}

	$goods['price'] = $goods['normalprice'];		
	$data['views'] = $goods['views']+1;
	pdo_update(BEST_GOODS,$data,array('id'=>$goods['id']));

	$resarr['imgUrls'] = $goodsthumbsarr;	
	$resarr['goods'] = $goods;
	$this->result(0,"商品详情", $resarr);
}
?>