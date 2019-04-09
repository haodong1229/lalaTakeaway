<?php
global $_W,$_GPC;
$openid = trim($_GPC['openid']);
if(empty($openid)){
	$this->result(1,"获取您的身份信息失败！", '');
}
$merchant = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE openid = '{$openid}' AND weid = {$_W['uniacid']}");
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if($operation == 'display'){
	$weicj = $hascj = array();
	if($merchant['xqz'] == 1){
		$hdlist = pdo_fetchall("SELECT * FROM ".tablename(BEST_HUODONG)." WHERE weid = {$_W['uniacid']} ORDER BY endtime DESC");
	}else{
		$hdlist = pdo_fetchall("SELECT * FROM ".tablename(BEST_HUODONG)." WHERE weid = {$_W['uniacid']} AND isxq = 0 ORDER BY endtime DESC");
	}
	foreach($hdlist as $k=>$v){
		$merchanthd = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANTHD)." WHERE hdid = {$v['id']} AND weid = {$_W['uniacid']} AND merchant_id = {$merchant['id']}");
		if(empty($merchanthd)){
			if($v['endtime'] > TIMESTAMP){
				$v['canjia'] = 1;
			}else{
				$v['canjia'] = 0;
			}
			$v['starttime'] = date("Y-m-d H:i:s",$v['starttime']);
			$v['endtime'] = date("Y-m-d H:i:s",$v['endtime']);
			$weicj[$k] = $v;
		}else{
			$v['starttime'] = date("Y-m-d H:i:s",$v['starttime']);
			$v['endtime'] = date("Y-m-d H:i:s",$v['endtime']);
			$v['merchanthdid'] = $merchanthd['id'];
			$hascj[$k] = $v;
		}
	}
	$res['hascj'] = $hascj;
	$res['weicj'] = $weicj;
	$this->result(0,"活动管理", $res);
}elseif($operation == 'dodohd'){
	$hdid = intval($_GPC['hdid']);
	$hdres = pdo_fetch("SELECT * FROM ".tablename(BEST_HUODONG)." WHERE weid = {$_W['uniacid']} AND id = {$hdid}");
	if(empty($hdres)){
		$this->result(1,"不存在该活动！", '');
	}
	if($hdres['endtime'] < TIMESTAMP){
		$this->result(1,"活动已经结束，不能参加！", '');
	}
	$hasmerchanthd = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANTHD)." WHERE merchant_id = {$merchant['id']} AND weid = {$_W['uniacid']} AND hdid = {$hdid}");
	if(!empty($hasmerchanthd)){
		$this->result(1,"您已参加过该活动！", '');
	}
	
	$data2['weid'] = $_W['uniacid'];
	$data2['hdid'] = $hdid;
	$data2['merchant_id'] = $merchant['id'];
	$data2['time'] = TIMESTAMP;
	$data2['sharetitle'] = $hdres['sharetitle'];
	$data2['sharethumb'] = $hdres['sharethumb'];
	$data2['sharedes'] = $hdres['sharedes'];
	$data2['canziti'] = 1;
	$data2['isxq'] = $hdres['isxq'];
	pdo_insert(BEST_MERCHANTHD,$data2);
	$mhdid = pdo_insertid();
	
	$hdgoods = pdo_fetchall("SELECT * FROM ".tablename(BEST_HUODONGGOODS)." WHERE weid = {$_W['uniacid']} AND hdid = {$hdid}");
	foreach($hdgoods as $k=>$v){
		$data = array();
		$goodsres = pdo_fetch("SELECT id,hasoption FROM ".tablename(BEST_GOODS)." WHERE weid = {$_W['uniacid']} AND id = {$v['goodsid']}");
		if($goodsres['hasoption'] == 1){
			$goodsoptions = pdo_fetchall("SELECT id FROM ".tablename(BEST_GOODSOPTION)." WHERE goodsid = {$goodsres['id']}");
			foreach($goodsoptions as $kk=>$vv){
				$data3 = array();
				$data3['weid'] = $_W['uniacid'];
				$data3['mhdid'] = $mhdid;
				$data3['optionid'] = $vv['id'];
				$data3['goodsid'] = $v['goodsid'];
				$data3['time'] = TIMESTAMP;
				pdo_insert(BEST_MERCHANTHDGOODS,$data3);
			}
		}else{
			$data['weid'] = $_W['uniacid'];
			$data['mhdid'] = $mhdid;
			$data['optionid'] = 0;
			$data['goodsid'] = $v['goodsid'];
			$data['time'] = TIMESTAMP;
			pdo_insert(BEST_MERCHANTHDGOODS,$data);
		}
	}
	$this->result(0,"参加活动成功！", $mhdid);
}elseif($operation == 'detail'){
	$id = intval($_GPC['id']);
	$merchanthd = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANTHD)." WHERE weid = {$_W['uniacid']} AND merchant_id = {$merchant['id']} AND id = {$id}");
	$merchantgoods = pdo_fetchall("SELECT * FROM ".tablename(BEST_MERCHANTHDGOODS)." WHERE mhdid = {$id} AND weid = {$_W['uniacid']}");
	foreach($merchantgoods as $k=>$v){
		$goodsres = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE weid = {$_W['uniacid']} AND id = {$v['goodsid']}");
		if($v['optionid'] > 0){
			$goodsoptions = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODSOPTION)." WHERE id = {$v['optionid']}");
			$merchantgoods[$k]['title'] = '['.$goodsoptions['title'].']'.$goodsres['title'];
			$merchantgoods[$k]['price'] = $goodsoptions['normalprice'];
			$merchantgoods[$k]['lirun'] = $goodsoptions['normalprice']-$goodsoptions['dailiprice'];
			$merchantgoods[$k]['total'] = $goodsoptions['stock'];
			$sales = pdo_fetchcolumn("SELECT SUM(a.total),b.id FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.mhdid = {$id} AND a.optionid = {$v['optionid']} AND b.status >= 1 AND a.orderid = b.id");
			$merchantgoods[$k]['sales'] = empty($sales) ? 0 : $sales;
		}else{			
			$merchantgoods[$k]['title'] = $goodsres['title'];
			$merchantgoods[$k]['price'] = $goodsres['normalprice'];
			$merchantgoods[$k]['lirun'] = $goodsres['normalprice']-$goodsres['dailiprice'];
			$merchantgoods[$k]['total'] = $goodsres['total'];
			$sales = pdo_fetchcolumn("SELECT SUM(a.total),b.id FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.mhdid = {$id} AND a.goodsid = {$v['goodsid']} AND b.status >= 1 AND a.orderid = b.id");
			$merchantgoods[$k]['sales'] = empty($sales) ? 0 : $sales;
		}
	}
	$allprice = pdo_fetchcolumn("SELECT SUM(price) FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND merchant_id = {$merchant['id']} AND mhdid = {$id} AND status >= 1");
	$allprice = empty($allprice) ? "0.00" : $allprice;
	$alllirun = pdo_fetchcolumn("SELECT SUM(alllirun) FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND merchant_id = {$merchant['id']} AND mhdid = {$id} AND status >= 1");
	$alllirun = empty($alllirun) ? "0.00" : $alllirun;
	$allordernum = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND merchant_id = {$merchant['id']} AND mhdid = {$id} AND status >= 1");
	
	$merchanthd['sharethumb2'] = $merchanthd['sharethumb'];
	$merchanthd['sharethumb'] = tomedia($merchanthd['sharethumb']);
	
	$huodong = pdo_fetch("SELECT pstype FROM ".tablename(BEST_HUODONG)." WHERE id = {$merchanthd['hdid']}");
	$merchanthd['pstype'] = $huodong['pstype'];
	$data['merhd'] = $merchanthd;
	$data['merchantgoods'] = $merchantgoods;
	$data['allprice'] = $allprice;
	$data['alllirun'] = $alllirun;
	$data['allordernum'] = $allordernum;
	$this->result(0,"活动详情", $data);
}elseif($operation == 'dodetail'){
	$id = intval($_GPC['id']);
	$data['sharetitle'] = trim($_GPC['sharetitle']);
	$data['sharedes'] = trim($_GPC['sharedes']);
	$data['sharethumb'] = trim($_GPC['sharethumb']);
	$data['yunfei'] = $_GPC['yunfei'];
	$data['manjian'] = $_GPC['manjian'];
	$data['canziti'] = intval($_GPC['canziti']);
	$data['cansonghuo'] = intval($_GPC['cansonghuo']);
	pdo_update(BEST_MERCHANTHD,$data,array('id'=>$id));
	$this->result(0,"更新活动详情成功", $data);
}
?>