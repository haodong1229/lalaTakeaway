<?php
global $_W,$_GPC;
$openid = trim($_GPC['openid']);
$member = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$openid}' AND weid = {$_W['uniacid']}");

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if($operation == 'display'){
	if(empty($member)){
		$resArr['error'] = 1;
		$resArr['message'] = "您还未授权登录！";
		$this->result(0,"购物车", $resArr);
		exit;
	}		
	$goodsid = intval($_GPC['goodsid']);
	$optionid = intval($_GPC['optionid']);
	$goodsres = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE id = {$goodsid}");
	if($goodsres['hasoption'] == 1 && $optionid == 0){
		$resArr['error'] = 1;
		$resArr['message'] = "请选择商品规格！";
		$this->result(0,"购物车", $resArr);
		exit;
	}
	$resArr['error'] = 0;
	$resArr['message'] = "已加入购物车！";
	$this->result(0,"购物车", $resArr);
}elseif($operation == 'detail'){
	$openid = trim($_GPC['openid']);
	$member = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$openid}' AND weid = {$_W['uniacid']}");	

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

	$data['goods'] = $goods;	
	$data['member'] = $member;
	$data['allprice'] = $allprice;
	$data['autofield'] = $autofield;
	$data['yijiquyu'] = pdo_fetchall("SELECT name,code FROM ".tablename(BEST_CITY)." WHERE type = 1");
	$this->result(0,"购物车", $data);
}elseif($operation == 'do'){
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
	}else{
		if($total > $goodsres['total']){
			$resArr['error'] = 1;
			$resArr['message'] = "商品".$goodsres['title']."库存不足！";
			$this->result(0,"购物车", $resArr);
			exit;
		}
		$allprice = $goodsres['normalprice']*$total;
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

	$pstype ==  0;
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

	$data['yunfei'] = 0;
	$data['address'] = $shname."|".$shphone."|".$shcity."|".$shaddress;
	$data['price'] = $data['price']+$data['yunfei'];

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
	$data['createtime'] = TIMESTAMP;
	$data['remark'] = $_GPC['remark'];
	$data['otheraddress'] = $idcard."(身份证)";
	$data['formid'] = $_GPC['formid'];
	$data['isdanmai'] = 1;
	pdo_insert(BEST_ORDER, $data);
	$orderid = pdo_insertid();

	if(!empty($optionres)){
		$datao['price'] = $optionres['normalprice'];
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
	pdo_insert(BEST_ORDERGOODS,$datao);

	$resArr['error'] = 0;
	$resArr['status'] = 0;
	$resArr['fee'] = $data['price'];
	$resArr['ordertid'] = $data['ordersn'];
	$resArr['message'] = "提交订单成功！";
	$this->result(0,"购物车", $resArr);
}
?>