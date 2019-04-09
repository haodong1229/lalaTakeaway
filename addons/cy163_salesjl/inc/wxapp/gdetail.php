<?php
global $_W,$_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if($operation == 'display'){	
	$id = intval($_GPC['id']);
	$goods = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE id = {$id} AND weid = {$_W['uniacid']}");
	$goodsthumbsarr[0] = tomedia($goods['thumb']);
	$thumbs = unserialize($goods['thumbs']);
	foreach($thumbs as $k=>$v){
		array_push($goodsthumbsarr,tomedia($v));
	}
	$resarr['imgUrls'] = $goodsthumbsarr;	
	$resarr['goods'] = $goods;
	$this->result(0,"商品详情", $resarr);
}
?>