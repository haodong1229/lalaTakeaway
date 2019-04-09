<?php
global $_W,$_GPC;
$openid = trim($_GPC['openid']);
$member = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$openid}' AND weid = {$_W['uniacid']}");
if(empty($member)){
	$this->result(1,"未获取到您的用户信息！", '');
}

$jlid = intval($_GPC['jlid']);
$memberhd = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERHUODONG)." WHERE weid = {$_W['uniacid']} AND id = {$jlid}");
if(empty($memberhd)){
	$this->result(1,"不存在该团购！", '');
}
if($member['openid'] == $memberhd['openid']){
	$this->result(1,"不能参与自己发布的团购！", '');
}
if($memberhd['owndel'] == 1 || $memberhd['admindel'] == 1){
	$this->result(1,"团购已经结束！", '');
}
if($memberhd['isxiugai'] == 1){
	$this->result(1,"团购正在修改中，暂不能下单！", '');
}
pdo_delete(BEST_CART,array('jlid'=>$jlid,'openid'=>$member['openid']));
if($memberhd['starttime'] > TIMESTAMP){
	$this->result(1,"团购还未开始！", '');
}
if($memberhd['endtime'] < TIMESTAMP){
	$this->result(1,"团购已经结束！", '');
}
$goods = $this->messistr2array($_GPC['goods']);
foreach($goods as $k=>$v){
	$total = $v['nownum'];
	if($total > 0){
		$goodsres = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERGOODS)." WHERE weid = {$_W['uniacid']} AND id = {$v['id']} AND hdid = {$jlid}");
		if(empty($goodsres)){
			$this->result(1,"有商品不属于该团购！", '');
		}
		
		if($total > $goodsres['total']){
			$this->result(1,"商品".$goodsres['title']."库存不足！", '');
		}
		
		$hasbuytotal = pdo_fetchcolumn("SELECT SUM(a.total) FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.jlid = {$jlid} AND a.goodsid = {$v} AND b.status >= 0 AND a.orderid = b.id AND b.from_user = '{$member['openid']}'");
		$latertotal = $hasbuytotal+$total;
		if($goodsres['xiangounum'] > 0 && $latertotal > $goodsres['xiangounum']){			
			$this->result(1,"商品".$goodsres['title']."每人限购".$goodsres['xiangounum'].$goodsres['optionname']."！您已购买了".$hasbuytotal.$goodsres['optionname'], '');
		}
	}
}
foreach($goods as $k=>$v){
	$total = $v['nownum'];
	if($total > 0){
		$goodsres = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERGOODS)." WHERE weid = {$_W['uniacid']} AND id = {$v['id']} AND hdid = {$jlid}");
		$hasjieti = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERGOODSJIETI)." WHERE goodsid = {$v['id']} AND jietinumstart <= {$goodsres['inpeople']} AND jietinum >= {$goodsres['inpeople']}");
		if(!empty($hasjieti)){
			$data['price'] = $hasjieti['jietiprice'];
		}else{
			$data['price'] = $goodsres['normalprice'];
		}
		$data['weid'] = $_W['uniacid'];
		$data['openid'] = $member['openid'];
		$data['jlid'] = $jlid;
		$data['goodsid'] = $v['id'];
		$data['goodsname'] = $goodsres['title'];
		$data['total'] = $total;
		$data['allprice'] = $data['total']*$data['price'];
		pdo_insert(BEST_CART,$data);
	}
}
$this->result(0,"提交成功！", '');
?>