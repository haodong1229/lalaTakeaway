<?php
global $_W,$_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if($operation == 'display'){
	$openid = trim($_GPC['openid']);
	$member = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$openid}' AND weid = {$_W['uniacid']}");
	if(empty($member)){
		$resArr['error'] = 1;
		$resArr['message'] = "您还未授权登录！";
		$this->result(0,"购物车", $resArr);
		exit;
	}	
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
	$merchanthd = $xqmsg['merchanthd'];
		
	$goodsid = intval($_GPC['goodsid']);
	$optionid = intval($_GPC['optionid']);
	$goodsres = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE id = {$goodsid}");
	if($goodsres['hasoption'] == 1 && $optionid == 0){
		$resArr['error'] = 1;
		$resArr['message'] = "请选择商品规格！";
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

	$resArr['error'] = 0;
	$resArr['message'] = "已加入购物车！";
	$this->result(0,"购物车", $resArr);
}elseif($operation == 'detail'){
	$openid = trim($_GPC['openid']);
	$member = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$openid}' AND weid = {$_W['uniacid']}");
	$xqid = intval($_GPC['xqid']);
	$xqmsg = $this->getxqmsg($xqid);
	$xiaoqu = $xqmsg['xiaoqu'];
	$merchant = $xqmsg['merchant'];
	$hdres = $xqmsg['hdres'];
	$merchanthd = $xqmsg['merchanthd'];
	
	if($hdres['pstype'] == 1){
		$hdres['cansonghuo'] = $merchanthd['cansonghuo'];
		$hdres['canziti'] = $merchanthd['canziti'];
		$hdres['manjian'] = $merchanthd['manjian'];
	}else{
		$hdres['cansonghuo'] = 1;
		$hdres['canziti'] = 0;
	}
	if($hdres['canziti'] == 1 && $hdres['cansonghuo'] == 0){
		$hdres['pstype'] = 1;
	}else{
		$hdres['pstype'] = 0;
	}
	
	if($hdres['cansonghuo'] == 1){
		$hdres['shzt'] = '满'.$hdres['manjian'].'元免运费';
	}else{
		$hdres['shzt'] = '免运费';
	}

	$allprice = 0;
	$autofield = 0;
	$goodsid = intval($_GPC['goodsid']);
	$optionid = intval($_GPC['optionid']);
	$goods = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE id = {$goodsid}");
	if($goods['autofield'] == 1){
		$autofield = 1;
	}
	$goods['thumb'] = tomedia($goods['thumb']);
	$goods['now'] = 1;
	if($optionid > 0){
		$goods['option'] = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODSOPTION)." WHERE id = {$optionid}");
		$goods['price'] = $goods['option']['normalprice'];
		$goods['canbuy'] = $goods['total'] = $goods['option']['stock'];
	}else{
		$goods['price'] = $goods['normalprice'];
		$goods['canbuy'] = $goods['total'];
	}
	$allprice = $goods['price'];
	$ztdlist = pdo_fetchall("SELECT * FROM ".tablename(BEST_ZITIDIAN)." WHERE openid = '{$merchant['openid']}' AND weid = {$_W['uniacid']} AND ztdtype = 1");
	if(empty($ztdlist)){
		$ztdlist[0]['id']=0;
	}
	
	$data['goods'] = $goods;	
	$data['hdres'] = $hdres;
	$data['ztdlist'] = $ztdlist;
	$data['member'] = $member;
	$data['allprice'] = $allprice;
	$data['autofield'] = $autofield;
	$data['yijiquyu'] = pdo_fetchall("SELECT name,code FROM ".tablename(BEST_CITY)." WHERE type = 1");
	$this->result(0,"购物车", $data);
}elseif($operation == 'dosqsub'){
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
	
	$total = intval($_GPC['total']);
	$goodsid = intval($_GPC['goodsid']);
	$optionid = intval($_GPC['optionid']);
	
	
	$goodsres = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE weid = {$_W['uniacid']} AND id = {$goodsid}");
	$optionres = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODSOPTION)." WHERE goodsid = {$goodsid} AND id = {$optionid}");
	if(!empty($optionres)){
		if($total > $optionres['stock']){
			$resArr['error'] = 1;
			$resArr['message'] = "商品".$goodsres['title']."[".$optionres['title']."]"."库存不足！";
			$this->result(0,"购物车", $resArr);
			exit;
		}
		$allprice = $optionres['normalprice']*$total;
		$alllirun = ($optionres['normalprice']-$optionres['dailiprice'])*$total;
	}else{
		if($total > $goodsres['total']){
			$resArr['error'] = 1;
			$resArr['message'] = "商品".$goodsres['title']."库存不足！";
			$this->result(0,"购物车", $resArr);
			exit;
		}
		$allprice = $goodsres['normalprice']*$total;
		$alllirun = ($goodsres['normalprice']-$goodsres['dailiprice'])*$total;
	}
	$allnum = $total;
		
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

	if(!empty($optionres)){
		$datao['price'] = $optionres['normalprice'];
		$datao['lirun'] = ($optionres['normalprice']-$optionres['dailiprice'])*$total;
			
		$datao['weid'] = $_W['uniacid'];
		$datao['optionid'] = $optionid;
		$datao['total'] = $total;
		$datao['cbprice'] = $optionres['chengbenprice'];
		$datao['dlprice'] = $optionres['dailiprice'];
		$datao['goodsid'] = $goodsid;
		$datao['createtime'] = TIMESTAMP;
		$datao['orderid'] = $orderid;
		$datao['goodsname'] = $goodsres['title'];
		$datao['optionname'] = $optionres['title'];
	}else{
		$datao['price'] = $goodsres['normalprice'];
		$datao['lirun'] = ($goodsres['normalprice']-$goodsres['dailiprice'])*$total;
		$datao['weid'] = $_W['uniacid'];
		$datao['optionid'] = 0;
		$datao['total'] = $total;
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

	$resArr['error'] = 0;
	$resArr['status'] = 0;
	$resArr['fee'] = $data['price'];
	$resArr['ordertid'] = $data['ordersn'];
	$resArr['message'] = "提交订单成功！";
	$this->result(0,"购物车", $resArr);
}
?>