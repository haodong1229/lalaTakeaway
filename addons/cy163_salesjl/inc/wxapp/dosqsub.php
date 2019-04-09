<?php
global $_W, $_GPC;
$openid = trim($_GPC['openid']);
$member = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$openid}' AND weid = {$_W['uniacid']}");

$xqid = intval($_GPC['xqid']);
$xqmsg = $this->getxqmsg($xqid);
if(empty($xqmsg['xiaoqu'])){
	$resArr['error'] = 1;
	$resArr['message'] = "获取小区信息失败！";
	$this->result(0,"购物车", $resArr);
	exit;
}
$hdres = $xqmsg['hdres'];
if(empty($hdres)){
	$resArr['error'] = 1;
	$resArr['message'] = "获取活动信息失败！";
	$this->result(0,"购物车", $resArr);
	exit;
}
if($hdres['tqjs'] == 1){
	$resArr['error'] = 1;
	$resArr['message'] = "活动已经提前结束！";
	$this->result(0,"购物车", $resArr);
	exit;
}
if($hdres['starttime'] > TIMESTAMP){
	$resArr['error'] = 1;
	$resArr['message'] = "活动还未开始！";
	$this->result(0,"购物车", $resArr);
	exit;
}
if($hdres['endtime'] < TIMESTAMP){
	$resArr['error'] = 1;
	$resArr['message'] = "活动已经结束！";
	$this->result(0,"购物车", $resArr);
	exit;
}
$merchanthd = $xqmsg['merchanthd'];
$merchant = $xqmsg['merchant'];	
	
$cartlist = pdo_fetchall("SELECT * FROM ".tablename(BEST_CART)." WHERE weid = {$_W['uniacid']} AND openid = '{$member['openid']}' AND xqid = {$xqid}");
foreach($cartlist as $k=>$v){
	$goodsid = $v['goodsid'];
	$optionid = $v['optionid'];

	$goodsres = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE weid = {$_W['uniacid']} AND id = {$goodsid}");
	$optionres = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODSOPTION)." WHERE goodsid = {$goodsid} AND id = {$optionid}");
	if(!empty($optionres)){
		if($v['total'] > $optionres['stock']){
			$resArr['error'] = 1;
			$resArr['message'] = "商品".$goodsres['title']."[".$optionres['title']."]"."库存不足！";
			echo json_encode($resArr);
			exit;
		}
		$allprice += $optionres['normalprice']*$v['total'];
		$alllirun += ($optionres['normalprice']-$optionres['dailiprice'])*$v['total'];
	}else{
		if($v['total'] > $goodsres['total']){
			$resArr['error'] = 1;
			$resArr['message'] = "商品".$goodsres['title']."库存不足！";
			echo json_encode($resArr);
			exit;
		}
		$allprice += $goodsres['normalprice']*$v['total'];
		$alllirun += ($goodsres['normalprice']-$goodsres['dailiprice'])*$v['total'];
	}
	$allnum += $v['total'];
}
if($allnum <= 0){
	$resArr['error'] = 1;
	$resArr['message'] = "至少选择一个商品！";
	$this->result(0,"购物车", $resArr);
	exit;
}
if($allprice <= 0){
	$resArr['error'] = 1;
	$resArr['message'] = "订单总金额不得少于0元！";
	$this->result(0,"购物车", $resArr);
	exit;
}
$data['price'] = $allprice;
$data['alllirun'] = $alllirun;

$pstype = intval($_GPC['pstype']);
if($pstype != 0 && $pstype != 1){
	$resArr['error'] = 1;
	$resArr['message'] = "请选择配送方式！";
	$this->result(0,"购物车", $resArr);
	exit;
}

if($pstype == 0){
	$shname = trim($_GPC['shname']);
	if(empty($shname)){
		$resArr['error'] = 1;
		$resArr['message'] = "收货人姓名不能为空！";
		$this->result(0,"购物车", $resArr);
		exit;
	}
	$shphone = trim($_GPC['shphone']);
	if(empty($shphone)){
		$resArr['error'] = 1;
		$resArr['message'] = "收货人手机不能为空！";
		$this->result(0,"购物车", $resArr);
		exit;
	}
	$shcity = trim($_GPC['shcity']);
	if(empty($shcity)){
		$resArr['error'] = 1;
		$resArr['message'] = "请选择地区！";
		$this->result(0,"购物车", $resArr);
		exit;
	}
	$shaddress = trim($_GPC['shaddress']);
	if(empty($shaddress)){
		$resArr['error'] = 1;
		$resArr['message'] = "请填写详细地址！";
		$this->result(0,"购物车", $resArr);
		exit;
	}
	if($hdres['pstype'] == 0){
		if($hdres['yfid'] > 0){
			$diquarr = explode(",",$shcity);
			$sheng = $diquarr[0];
			$shi = $diquarr[1];
			$xian = $diquarr[2];
			$yfsheng1 = pdo_fetch("SELECT * FROM ".tablename(BEST_YUNFEISHENG)." WHERE yfid = {$hdres['yfid']} AND diqutype = 3 AND name = '{$sheng}' AND city = '{$shi}' AND xian = '{$xian}'");
			$yfsheng2 = pdo_fetch("SELECT * FROM ".tablename(BEST_YUNFEISHENG)." WHERE yfid = {$hdres['yfid']} AND diqutype = 2 AND name = '{$sheng}' AND city = '{$shi}' AND xian = ''");
			$yfsheng3 = pdo_fetch("SELECT * FROM ".tablename(BEST_YUNFEISHENG)." WHERE yfid = {$hdres['yfid']} AND diqutype = 1 AND name = '{$sheng}' AND city = '' AND xian = ''");
			if(empty($yfsheng1) && empty($yfsheng2) && empty($yfsheng3)){				
				$resArr['error'] = 1;
				$resArr['message'] = "不在活动售卖区域不能提交订单！";
				$this->result(0,"购物车", $resArr);
				exit;
			}
		}
		if($data['price'] >= $hdres['manjian']){
			$data['yunfei'] = 0;
		}else{
			if(!empty($yfsheng1)){
				$data['yunfei'] = $yfsheng1['money'];
			}
			if(!empty($yfsheng2) && empty($yfsheng1)){
				$data['yunfei'] = $yfsheng2['money'];
			}
			if(!empty($yfsheng3) && empty($yfsheng1) && empty($yfsheng2)){
				$data['yunfei'] = $yfsheng3['money'];
			}
		}
	}else{
		if($data['price'] >= $merchanthd['manjian']){
			$data['yunfei'] = 0;
		}else{
			$data['yunfei'] = $merchanthd['yunfei'];
		}
		$pstype = 3;
		$data['alllirun'] = $data['alllirun'] + $data['yunfei'];
	}
	$data['address'] = $shname."|".$shphone."|".$shcity."|".$shaddress;
	$data['price'] = $data['price']+$data['yunfei'];
	$data['ztdid'] = 0;
}else{
	$shname = trim($_GPC['shname']);
	if(empty($shname)){
		$resArr['error'] = 1;
		$resArr['message'] = "取货人姓名不能为空！";
		$this->result(0,"购物车", $resArr);
		exit;
	}
	$shphone = trim($_GPC['shphone']);
	if(empty($shphone)){
		$resArr['error'] = 1;
		$resArr['message'] = "取货人手机不能为空！";
		$this->result(0,"购物车", $resArr);
		exit;
	}
	$data['address'] = $shphone."|".$shname;
	
	$ztdid = intval($_GPC['ztdid']);
	$ztdres = pdo_fetch("SELECT * FROM ".tablename(BEST_ZITIDIAN)." WHERE id = {$ztdid}");
	if(empty($ztdres)){
		$resArr['error'] = 1;
		$resArr['message'] = "请选择自提点！";
		$this->result(0,"购物车", $resArr);
		exit;
	}
	$data['yunfei'] = 0;
	$data['ztdid'] = $ztdid;
	$data['ztdaddress'] = $ztdres['address'];
	$data['ztdjingdu'] = $ztdres['jingdu'];
	$data['ztdweidu'] = $ztdres['weidu'];
	if($hdres['pstype'] == 1){
		$pstype = 4;
	}
}

$autofield = intval($_GPC['autofield']);
$idcard = trim($_GPC['idcard']);
$idcardlen = strlen($idcard);
if($autofield == 1 && $idcardlen != 18){
	$resArr['error'] = 1;
	$resArr['message'] = "请填写18位数的身份证号！";
	$this->result(0,"购物车", $resArr);
	exit;
}

$data['weid'] = $_W['uniacid'];
$data['pstype'] = $pstype;
$data['from_user'] = $member['openid'];
$data['ordersn'] = date('Ymd').random(13,1);
$data['merchant_id'] = $merchant['id'];
$data['createtime'] = TIMESTAMP;
$data['hdid'] = $hdres['id'];
$data['mhdid'] = $merchanthd['id'];
$data['xqid'] = $xqid;
$data['remark'] = $_GPC['remark'];
$data['otheraddress'] = $idcard."(身份证)";
$data['formid'] = $_GPC['formid'];
if(isset($this->module['config']['dltkhour'])){
	$data['cantktime'] = $hdres['endtime'] - ($this->module['config']['dltkhour'])*3600;
}
pdo_insert(BEST_ORDER, $data);
$orderid = pdo_insertid();

$datam['shname'] = $shname;	
$datam['shphone'] = $shphone;	
pdo_update(BEST_MEMBER,$datam,array('openid'=>$member['openid']));

foreach($cartlist as $k=>$v){
	$goodsid = $v['goodsid'];
	$optionid = $v['optionid'];
	
	$goodsres = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE weid = {$_W['uniacid']} AND id = {$goodsid}");
	$optionres = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODSOPTION)." WHERE goodsid = {$goodsid} AND id = {$optionid}");
	if(!empty($optionres)){
		$datao['price'] = $optionres['normalprice'];
		$datao['lirun'] = ($optionres['normalprice']-$optionres['dailiprice'])*$v['total'];
			
		$datao['weid'] = $_W['uniacid'];
		$datao['optionid'] = $optionid;
		$datao['total'] = $v['total'];
		$datao['cbprice'] = $optionres['chengbenprice'];
		$datao['dlprice'] = $optionres['dailiprice'];
		$datao['goodsid'] = $goodsid;
		$datao['createtime'] = TIMESTAMP;
		$datao['orderid'] = $orderid;
		$datao['goodsname'] = $goodsres['title'];
		$datao['optionname'] = $optionres['title'];
	}else{
		$datao['price'] = $goodsres['normalprice'];
		$datao['lirun'] = ($goodsres['normalprice']-$goodsres['dailiprice'])*$v['total'];
		$datao['weid'] = $_W['uniacid'];
		$datao['optionid'] = 0;
		$datao['total'] = $v['total'];
		$datao['cbprice'] = $goodsres['chengbenprice'];
		$datao['dlprice'] = $goodsres['dailiprice'];
		$datao['goodsid'] = $goodsid;
		$datao['createtime'] = TIMESTAMP;
		$datao['orderid'] = $orderid;
		$datao['goodsname'] = $goodsres['title'];
		$datao['optionname'] = "";
	}
	$datao['hdid'] = $hdres['id'];
	$datao['mhdid'] = $merchanthd['id'];
	pdo_insert(BEST_ORDERGOODS,$datao);
	
	//清空购物车
	pdo_delete(BEST_CART,array('id'=>$v['id']));
}

$resArr['error'] = 0;
$resArr['status'] = 0;
$resArr['fee'] = $data['price'];
$resArr['ordertid'] = $data['ordersn'];
$resArr['message'] = "提交订单成功！";
$this->result(0,"购物车", $resArr);
?>