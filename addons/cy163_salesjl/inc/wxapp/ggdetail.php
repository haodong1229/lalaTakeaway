<?php
global $_W,$_GPC;
$openid = trim($_GPC['openid']);
$member = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$openid}' AND weid = {$_W['uniacid']}");
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if($operation == 'display'){	
	$id = intval($_GPC['id']);
	$xqid = intval($_GPC['xqid']);
	$xqmsg = $this->getxqmsg($xqid);
	$hdres = $xqmsg['hdres'];
		
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
	$salelist = pdo_fetchall("SELECT a.*,b.from_user FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.goodsid = {$id} AND a.orderid = b.id AND b.hdid = {$hdres['id']} AND b.status >= 1 ORDER BY b.createtime LIMIT 3");

	foreach($salelist as $kk=>$vv){
		$membersa = pdo_fetch("SELECT nickname,avatar FROM ".tablename(BEST_MEMBER)." WHERE weid = {$_W['uniacid']} AND openid = '{$vv['from_user']}'");
		$salelist[$kk]['nickname'] = $membersa['nickname'];
		$salelist[$kk]['avatar'] = $membersa['avatar'];
		$salelist[$kk]['createtime'] = date("Y-m-d H:i:s",$vv['createtime']);
	}
	$resarr['salelist'] = $salelist;
		
	$data['views'] = $goods['views']+1;
	pdo_update(BEST_GOODS,$data,array('id'=>$goods['id']));
	
	$goods['cartnum'] = pdo_fetchcolumn("SELECT SUM(total) FROM ".tablename(BEST_CART)." WHERE xqid = {$xqid} AND openid = '{$member['openid']}'");
	
	$resarr['daojishi'] = 1;
	$resarr['endtime'] = $hdres['endtime'];
	if($hdres['endtime'] < TIMESTAMP || $hdres['tqjs'] == 1){
		$resarr['daojishi'] = 1;
	}
	$resarr['imgUrls'] = $goodsthumbsarr;	
	$resarr['goods'] = $goods;
	$this->result(0,"商品详情", $resarr);
}
?>