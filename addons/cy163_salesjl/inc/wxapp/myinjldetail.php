<?php
global $_W,$_GPC;
$openid = trim($_GPC['openid']);
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if($operation == 'display'){	
	$id = intval($_GPC['id']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$id} AND from_user = '{$openid}' AND weid = {$_W['uniacid']}");

	//商品信息
	$ordergoods = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$orderres['id']}");
	if($orderres['status'] == 0){
		$orderres['canfukuan'] = 1;
		foreach($ordergoods as $k=>$v){
			$goodsres = pdo_fetch("SELECT total FROM ".tablename(BEST_MEMBERGOODS)." WHERE id = {$v['goodsid']}");
			if($goodsres['total'] < $v['total']){
				$orderres['canfukuan'] = 0;
			}
		}
	}else{
		$orderres['canfukuan'] = 0;
	}
	if($orderres['pstype'] == 0 || $orderres['pstype'] == 3){
		$orderres['address'] = explode("|",$orderres['address']);
	}
	$orderres['time'] = date("Y-m-d H:i:s",$orderres['createtime']);
	/*if($orderres['expresscom'] != 'ZFPS' && $orderres['expresscom'] != 'SF' && $orderres['expresscom'] != ''){
		include_once(ROOT_PATH.'Express.class.php');
		$idkdn = $this->module['config']['kdnid'];
		$keykdn = $this->module['config']['kdnkey'];
		$urlkdn = "http://api.kdniao.cc/api/dist";
		$shipperCode = $orderres['expresscom'];//快递公司简称，官方有文档
		$logisticCode = $orderres['expresssn'];//快递单号//
		$a = new Express(); 
		$logisticResult = $a->getOrderTracesByJson($idkdn,$keykdn,$urlkdn,$shipperCode,$logisticCode);
		$data = json_decode($logisticResult,true);
		if($data['State'] != "0"){
			$expressres = $data['Traces'];
			rsort($expressres);
		}
	}else{*/
		$expressres = '';
	/*}*/

	$res['orderres'] = $orderres;
	$res['ordergoods'] = $ordergoods;
	$res['expressres'] = $expressres;
	$this->result(0,"团购订单详情！",$res);
}elseif($operation == 'shouhuo'){
	$orderid = intval($_GPC['id']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND weid = {$_W['uniacid']} AND status = 2 AND from_user = '{$openid}'");
	if(empty($orderres)){
		$this->result(1,"不存在该订单！", '');
	}
	$data['status'] = 4;
	pdo_update(BEST_ORDER,$data,array('id'=>$orderres['id']));
	//利润写进代理商数据库，同时设置可提现时间
	$hasmemberaccount = pdo_fetch("SELECT id FROM ".tablename(BEST_MEMBERACCOUNT)." WHERE openid = {$orderres['jlopenid']}  AND orderid = {$orderres['id']}");
	if(empty($hasmemberaccount)){
		$datamoney['weid'] = $_W['uniacid'];
		$datamoney['openid'] = $orderres['jlopenid'];
		$datamoney['money'] = $orderres['price'];
		$datamoney['time'] = TIMESTAMP;
		$datamoney['orderid'] = $orderres['id'];
		$datamoney['explain'] = "团购订单";
		$datamoney['candotime'] = TIMESTAMP + ($this->module['config']['zftxhour'])*3600;
		pdo_insert(BEST_MEMBERACCOUNT,$datamoney);
	}
	$this->result(0,"确认收货成功！", '');
}elseif($operation == 'getqrcode'){
	$account_api = WeAccount::create();
	$access_token = $account_api->getAccessToken();
	$url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=".$access_token;
	$data = array(
		"scene"=>$_GPC['scene'],
		"page"=>"cy163_salesjl/pages/zfhexiao/zfhexiao",
		"width"=>430,
	);
	$data = json_encode($data);
	$response = $this->send_post($url,$data);	
	$result=$this->data_uri($response,'image/png');
	$this->result(0,'核销码！', $result);
}elseif($operation == 'cancelorder'){
	$orderid = intval($_GPC['id']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND status = 0 AND from_user = '{$openid}' AND weid = {$_W['uniacid']}");
	if(empty($orderres)){
		$this->result(1,"不存在该订单！", '');
	}
	$data['status'] = -1;
	pdo_update(BEST_ORDER,$data,array('id'=>$orderres['id']));
	$this->result(0,"取消订单成功！", '');
}elseif($operation == 'refund'){
	$orderid = intval($_GPC['orderid']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND (status = 1 OR status = 2) AND from_user = '{$openid}'");
	$refund_price = $orderres['price']-$orderres['yunfei'];
	$resarr['refund_price'] = $refund_price;
	$this->result(0,'退款！', $resarr);
}elseif($operation == 'dorefund'){
	$orderid = intval($_GPC['orderid']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND (status = 1 OR status = 2) AND from_user = '{$openid}'");
	if(empty($orderres)){
		$this->result(1,"没有该订单！", '');
	}
	if($orderres['isdmfk'] == 1){
		$this->result(1,"该订单不能退款！", '');
	}
	
	$refund_price = $_GPC['refund_price'];
	if($refund_price <= 0){
		$this->result(1,"请填写退款金额！", '');
	}
	$can_refund_price = $orderres['price']-$orderres['yunfei'];
	if($refund_price > $can_refund_price){
		$this->result(1,"退款金额超限！", '');
	}
	
	$refund_desc = trim($_GPC['refund_desc']);
	if(empty($refund_desc)){
		$this->result(1,"请填写退款原因！", '');
	}
	
	$data['refund_desc'] = $refund_desc;
	$data['tktime'] = TIMESTAMP;
	$data['refund_price'] = $refund_price;
	$data['status'] = -2;
	pdo_update(BEST_ORDER,$data,array('id'=>$orderres['id']));

	$this->result(0,'申请退款成功！', '');
}
?>