<?php
global $_W, $_GPC;
$merchant = $this->checkmergentauth();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$express = array(
	99=>array(
		'pinyin'=>'ZFPS',
		'value'=>'自发配送',
	),
	0=>array(
		'pinyin'=>'SF',
		'value'=>'顺丰速运',
	),
	1=>array(
		'pinyin'=>'HTKY',
		'value'=>'百世快递',
	),
	2=>array(
		'pinyin'=>'ZTO',
		'value'=>'中通快递',
	),
	3=>array(
		'pinyin'=>'STO',
		'value'=>'申通快递',
	),
	4=>array(
		'pinyin'=>'YTO',
		'value'=>'圆通速递',
	),
	5=>array(
		'pinyin'=>'YD',
		'value'=>'韵达速递',
	),
	6=>array(
		'pinyin'=>'YZPY',
		'value'=>'邮政快递包裹',
	),
	7=>array(
		'pinyin'=>'EMS',
		'value'=>'EMS',
	),
	8=>array(
		'pinyin'=>'HHTT',
		'value'=>'天天快递',
	),
	9=>array(
		'pinyin'=>'JD',
		'value'=>'京东物流',
	),
	10=>array(
		'pinyin'=>'QFKD',
		'value'=>'全峰快递',
	),
	11=>array(
		'pinyin'=>'GTO',
		'value'=>'国通快递',
	),
	12=>array(
		'pinyin'=>'UC',
		'value'=>'优速快递',
	),
	13=>array(
		'pinyin'=>'DBL',
		'value'=>'德邦',
	),
	14=>array(
		'pinyin'=>'FAST',
		'value'=>'快捷快递',
	),
	15=>array(
		'pinyin'=>'ZJS',
		'value'=>'宅急送',
	),
);
if ($operation == 'display') {
	$status = intval($_GPC['status']);
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND merchant_id = {$merchant['id']} AND status = {$status}");
	$allpage = ceil($total/10)+1;
	$page = intval($_GPC["page"]);
	$pindex = max(1, $page);
	$psize = 10;
	$merchantorderlist = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND merchant_id = {$merchant['id']} AND status = {$status} ORDER BY createtime DESC LIMIT ".($pindex - 1)*$psize.",".$psize);
	$isajax = intval($_GPC['isajax']);
	if($isajax == 1){	
		$html = '';
		foreach($merchantorderlist as $k=>$v){
			if($v['status'] == 0){
				$statustext = '待付款';
			}
			if($v['status'] == 1){
				$statustext = $v['pstype'] == 0 || $v['pstype'] == 3 ? '待发货' :'待自提';
			}
			if($v['status'] == 2){
				$statustext = '待收货';
			}
			if($v['status'] == 4){
				$statustext = '已完成';
			}
			$html .= '<div class="orderitem">
						<div class="name flex">
							<div class="storename">店铺：'.$merchant['name'].'</div>
							<div class="orderstatus text-r">'.$statustext.'</div>
						</div>
						<div class="ordersn">订单编号：'.$v['ordersn'].'</div>
						<div class="price flex">
							<div class="orderprice">合计：<span>￥'.$v['price'].'</span></div>
							<div class="orderbutton text-r"><a href="'.$this->createMobileUrl('merchantorder',array('op'=>'detail','id'=>$v['id'])).'">订单详情</a></div>
						</div>
					</div>';
		}
		echo $html;
		exit;
	}else{
		include $this->template('merchantorder');
	}
}elseif ($operation == 'search') {
	$status = intval($_GPC['status']);
	$keyword = trim($_GPC['keyword']);
	if($keyword == ''){
		$resArr['error'] = 1;
		$resArr['message'] = "请输入订单号或用户信息搜索！";
		echo json_encode($resArr);
		exit;
	}
	$merchantorderlist = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND merchant_id = {$merchant['id']} AND status = {$status} AND (ordersn like '%{$keyword}%' OR address like '%{$keyword}%') ORDER BY createtime DESC");
	if(empty($merchantorderlist)){
		$html = '<div class="nodata text-c">
					<div class="iconfont">&#xe67c;</div>
					<div class="text">( ⊙ o ⊙ )啊哦，没有搜索记录啦~</div>
				</div>';
	}else{
		$html = '';
		foreach($merchantorderlist as $k=>$v){
			if($v['status'] == 0){
				$statustext = '待付款';
			}
			if($v['status'] == 1){
				$statustext = $v['pstype'] == 0 || $v['pstype'] == 3 ? '待发货' :'待自提';
			}
			if($v['status'] == 2){
				$statustext = '待收货';
			}
			if($v['status'] == 4){
				$statustext = '已完成';
			}
			$html .= '<div class="orderitem">
						<div class="name flex">
							<div class="storename">店铺：'.$merchant['name'].'</div>
							<div class="orderstatus text-r">'.$statustext.'</div>
						</div>
						<div class="ordersn">订单编号：'.$v['ordersn'].'</div>
						<div class="price flex">
							<div class="orderprice">合计：<span>￥'.$v['price'].'</span></div>
							<div class="orderbutton text-r"><a href="'.$this->createMobileUrl('merchantorder',array('op'=>'detail','id'=>$v['id'])).'">订单详情</a></div>
						</div>
					</div>';
		}
	}
	$resArr['error'] = 0;
	$resArr['html'] = $html;
	echo json_encode($resArr);
	exit;
	
}elseif ($operation == 'cancelorder') {
	$ordersn = trim($_GPC['ordersn']);
	$order = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE ordersn = '{$ordersn}' AND status = 0 AND merchant_id = {$merchant['id']}");
	if(empty($order)){
		$resArr['error'] = 1;
		$resArr['message'] = '不存在该订单！';
		echo json_encode($resArr);
		exit();
	}else{
		$datacancel['status'] = -1;
		pdo_update(BEST_ORDER,$datacancel,array('id'=>$order['id']));
		$resArr['error'] = 0;
		$resArr['message'] = '取消订单成功！';
		echo json_encode($resArr);
		exit();
	}
}elseif($operation == 'detail') {
	$id = intval($_GPC['id']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$id} AND merchant_id = {$merchant['id']} AND weid = {$_W['uniacid']}");
	if (empty($orderres)) {
		$message = '抱歉，您没有该订单！';
		include $this->template('error');
		exit;
	}
	$member = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$orderres['from_user']}'");
	//商品信息
	$ordergoods = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$orderres['id']}");
	$address = explode("|",$orderres['address']);
	if($orderres['expresscom'] != 'ZFPS' && $orderres['expresscom'] != 'SF' && $orderres['expresscom'] != ''){
		include_once(ROOT_PATH.'Express.class.php');
		$idkdn = $this->module['config']['kdnid'];
		$keykdn = $this->module['config']['kdnkey'];
		$shipperCode = $orderres['expresscom'];//快递公司简称，官方有文档
		$logisticCode = $orderres['expresssn'];//快递单号//
		$a = new Express(); 
		$logisticResult = $a->getOrderTracesByJson($idkdn,$keykdn,KDN_URL,$shipperCode,$logisticCode);
		$data = json_decode($logisticResult,true);
		if($data['State'] != "0"){
			$expressres = $data['Traces'];
			rsort($expressres);
		}
	}
	include $this->template('merchantorderdetail');
}elseif($operation == 'fahuo') {
	if (empty($_GPC['expresscom']) || empty($_GPC['express']) || empty($_GPC['expresssn'])) {
		$resArr['error'] = 1;
		$resArr['message'] = '请选择选择快递公司并且输入快递单号！';
		echo json_encode($resArr);
		exit();
	}
	$id = intval($_GPC['id']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$id} AND merchant_id = {$merchant['id']} AND weid = {$_W['uniacid']}");
	if($orderres['status'] != 1){
		$resArr['error'] = 1;
		$resArr['message'] = '订单当前状态不能使用发货操作！';
		echo json_encode($resArr);
		exit();
	}
	$data['status'] = 2;
	$data['express'] = $_GPC['express'];
	$data['expresscom'] = $_GPC['expresscom'];
	$data['expresssn'] = $_GPC['expresssn'];
	$data['fhtime'] = TIMESTAMP;
	pdo_update(BEST_ORDER,$data,array('id' => $id));
	if($this->module['config']['istplon'] == 1){
		$or_paysuccess_redirect = $_W["siteroot"].'app/'.str_replace("./","",$this->createMobileUrl("myorder",array('statuss'=>2)));
		$postdata = array(
			'first' => array(
				'value' => "发货通知！",
				'color' => '#ff510'
			),
			'keyword1' => array(
				'value' => $orderres['ordersn'],
				'color' => '#ff510'
			),
			'keyword2' => array(
				'value' => $_GPC['express'],
				'color' => '#ff510'
			),
			'keyword3' => array(
				'value' => $_GPC['expresssn'],
				'color' => '#ff510'
			),
			'Remark' => array(
				'value' => "" ,
				'color' => '#ff510'
			),							
		);
		$account_api = WeAccount::create();
		$account_api->sendTplNotice($orderres['from_user'],$this->module['config']['tpl_or_fahuo'],$postdata,$or_paysuccess_redirect,'#FF5454');		
	}
	$resArr['error'] = 0;
	$resArr['message'] = '发货成功！';
	echo json_encode($resArr);
	exit();
}elseif($operation == 'shouhuo') {
	$orderid = intval($_GPC['orderid']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND merchant_id = {$merchant['id']} AND weid = {$_W['uniacid']}");
	if($orderres['status'] != 2){
		$resArr['error'] = 1;
		$resArr['message'] = '订单当前状态不能使用发货操作！';
		echo json_encode($resArr);
		exit();
	}
	$nowtime = TIMESTAMP;
	$canshtime = $orderres['fhtime']+$this->module['config']['qrshhour2']*3600;
	if($canshtime > $nowtime){
		$resArr['error'] = 1;
		$resArr['message'] = date("Y-m-d H:i:s",$canshtime).'后才能主动完成收货！';
		echo json_encode($resArr);
		exit();
	}
	$data['status'] = 4;
	pdo_update(BEST_ORDER,$data,array('id' => $orderid));
	if($orderres['isdmfk'] == 0){
		//利润写进代理商数据库，同时设置可提现时间
		$hasmerchantaccount = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANTACCOUNT)." WHERE merchant_id = {$orderres['merchant_id']} AND orderid = {$orderres['id']}");
		if(empty($hasmerchantaccount)){
			$datamerchant['weid'] = $_W['uniacid'];
			$datamerchant['merchant_id'] = $orderres['merchant_id'];
			$datamerchant['money'] = $orderres['alllirun'];
			$datamerchant['time'] = TIMESTAMP;
			$datamerchant['explain'] = '代理团结算';
			$datamerchant['orderid'] = $orderres['id'];
			$datamerchant['candotime'] = TIMESTAMP + ($this->module['config']['dltxhour'])*3600;
			pdo_insert(BEST_MERCHANTACCOUNT,$datamerchant);
		}
	}
	$resArr['error'] = 0;
	$resArr['message'] = '确认收货成功！';
	echo json_encode($resArr);
	exit();
}elseif($operation == 'refund'){
	$orderid = intval($_GPC['orderid']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND (status = 1 OR status = 2) AND merchant_id = {$merchant['id']}");
	if(empty($orderres)){
		$message = '没有该订单！';
		include $this->template('error');
		exit;
	}
	if($orderres['isdmfk'] == 1){
		$message = '该订单不能退款！';
		include $this->template('error');
		exit;
	}
	if($orderres['cantktime'] < TIMESTAMP){
		$message = '已经超过允许的退款时间！';
		include $this->template('error');
		exit;
	}
	include $this->template('merchantorderrefund');
}elseif($operation == 'dorefund'){
	$orderid = intval($_GPC['orderid']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND (status = 1 OR status = 2) AND merchant_id = {$merchant['id']}");
	if(empty($orderres)){
		$resArr['error'] = 1;
		$resArr['message'] = '没有该订单！';
		echo json_encode($resArr);
		exit();
	}
	if($orderres['isdmfk'] == 1){
		$resArr['error'] = 1;
		$resArr['message'] = '该订单不能退款！';
		echo json_encode($resArr);
		exit();
	}
	if($orderres['cantktime'] < TIMESTAMP){
		$resArr['error'] = 1;
		$resArr['message'] = '已经超过允许的退款时间！';
		echo json_encode($resArr);
		exit();
	}
	$refund_desc = trim($_GPC['refund_desc']);
	if(empty($refund_desc)){
		$resArr['error'] = 1;
		$resArr['message'] = '请填写退款原因！';
		echo json_encode($resArr);
		exit();
	}
	$data['refund_desc'] = $refund_desc;
	$data['tktime'] = TIMESTAMP;
	$data['refund_price'] = $orderres['status'] == 1 ? $orderres['price'] : $orderres['price']-$orderres['yunfei'];
	$data['status'] = -2;
	pdo_update(BEST_ORDER,$data,array('id'=>$orderres['id']));
	$resArr['error'] = 0;
	$resArr['message'] = '申请退款成功！';
	echo json_encode($resArr);
	exit();
}else{
	$message = '请求方式不存在！';
	include $this->template('error');
	exit;
}
?>