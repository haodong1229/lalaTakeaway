<?php
global $_W,$_GPC;
$openid = trim($_GPC['openid']);
$id = intval($_GPC['id']);
$memberhd = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERHUODONG)." WHERE weid = {$_W['uniacid']} AND id = {$id} AND status = 1");
$hdmember = pdo_fetch("SELECT openid,rztype FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$memberhd['openid']}' AND weid = {$_W['uniacid']}");
$memberhd['time'] = date("Y-m-d H:i:s",$memberhd['time']);
$memberhd['startdate'] = date("Y-m-d",$memberhd['starttime']);
$memberhd['enddate'] = date("Y-m-d",$memberhd['endtime']);

$data2['views'] = $memberhd['views']+1;
pdo_update(BEST_MEMBERHUODONG,$data2,array('id'=>$id));
$thumbs = unserialize($memberhd['thumbs']);
$thumbs2 = array();
foreach($thumbs as $k=>$v){
	$thumbs2[] = tomedia($v);
}
$memberhd['jlimgs'] = $thumbs2;

$goodslist = pdo_fetchall("SELECT * FROM ".tablename(BEST_MEMBERGOODS)." WHERE weid = {$_W['uniacid']} AND hdid = {$id}");
foreach($goodslist as $k=>$v){
	$goodsthumbs = unserialize($v['thumbs']);
	
	$ggthumbs2 = array();
	foreach($goodsthumbs as $kk=>$vv){
		$ggthumbs2[] = tomedia($vv);
	}
	$goodslist[$k]['jlimgs'] = $ggthumbs2;
	$goodslist[$k]['imgnow'] = $k;
	
	$sublist[$k]['id'] = $v['id'];
	$sublist[$k]['nownum'] = 0;
	$goodslist[$k]['kk'] = $k;
	$goodslist[$k]['id'] = $v['id'];
	$goodslist[$k]['thumb'] = tomedia($goodsthumbs[0]);
	$goodslist[$k]['thumb1'] = tomedia($goodsthumbs[1]);
	$goodslist[$k]['thumb2'] = tomedia($goodsthumbs[2]);
	$goodslist[$k]['count'] = count($goodsthumbs);
	$hasjieti = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERGOODSJIETI)." WHERE goodsid = {$v['id']} AND jietinumstart <= {$v['inpeople']} AND jietinum >= {$v['inpeople']}");
	if(!empty($hasjieti)){
		$goodslist[$k]['normalprice'] = $hasjieti['jietiprice'];
	}else{
		$goodslist[$k]['normalprice'] = $v['normalprice'];
	}
	$goodslist[$k]['jietilistcount'] = pdo_fetchcolumn("SELECT * FROM ".tablename(BEST_MEMBERGOODSJIETI)." WHERE goodsid = {$v['id']}");
	$goodslist[$k]['jietilist'] = pdo_fetchall("SELECT * FROM ".tablename(BEST_MEMBERGOODSJIETI)." WHERE goodsid = {$v['id']} ORDER BY jietiprice DESC");
	if($v['xiangounum'] > 0){		
		$hasbuynum = pdo_fetchcolumn("SELECT SUM(a.total) FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.jlid = {$id} AND a.goodsid = {$v['id']} AND b.status >= 0 AND a.orderid = b.id AND b.from_user = '{$openid}'");
		$leftcanbuy = $v['xiangounum']-$hasbuynum;
		if($v['total'] < $leftcanbuy){
			$goodslist[$k]['canbuynum'] = $v['total'];
		}else{
			$goodslist[$k]['canbuynum'] = $leftcanbuy;
		}
	}else{
		$goodslist[$k]['canbuynum'] = $v['total'];
	}
	$goodslist[$k]['nownum'] = 0;
}

$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND isjl = 1 AND status >= 1 AND jlid = {$id}");
$psize = 10;
$allpage = ceil($total/$psize)+1;
$page = intval($_GPC["page"]);
$pindex = max(1, $page);
$canyulist = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND isjl = 1 AND status >= 1 AND jlid = {$id} ORDER BY createtime LIMIT ".($pindex - 1)*$psize.",".$psize);
if($page == 0){
	$canyunum = 1;
}else{
	$canyunum = ($page-1)*10+1;
}
foreach($canyulist as $k=>$v){
	$canyulist[$k]['canyunum'] = $canyunum;
	$canyulist[$k]['member'] = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$v['from_user']}' AND weid = {$_W['uniacid']}");
	$canyulist[$k]['total'] = pdo_fetchcolumn("SELECT SUM(total) FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$v['id']}");
	$canyulist[$k]['time'] = date("Y-m-d H:i",$v['createtime']);
	$canyunum++;
}
$jlallprice = pdo_fetchcolumn("SELECT SUM(price) FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND isjl = 1 AND status >= 1 AND jlid = {$id}");
$jlallprice = empty($jlallprice) ? "0.00" : round($jlallprice,2);

$memberhd['jlallprice'] = $jlallprice+$jlallprice*$memberhd['basicsales'];
$memberhd['sharethumb'] = !empty($memberhd['sharethumb']) ? tomedia($memberhd['sharethumb']) : tomedia($thumbs[0]);
$memberhd['inpeople'] = $memberhd['inpeople']+$memberhd['basicsales'];
$memberhd['views'] = $memberhd['views']+$memberhd['basicviews'];
$data['goodslist'] = $goodslist;
$data['sublist'] = $sublist;
$data['memberhd'] = $memberhd;
$data['canyulist'] = $canyulist;
$data['allpage'] = $allpage;
$data['nowtime'] = TIMESTAMP;
$this->result(0,"接龙详情", $data);
?>