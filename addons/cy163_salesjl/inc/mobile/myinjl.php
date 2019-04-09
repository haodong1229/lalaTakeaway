<?php
global $_W,$_GPC;
$member = $this->Mcheckmember();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if($operation == 'display'){
	$orstatus = intval($_GPC['orstatus']);
	if($orstatus == 200){
		$conditions = "AND status = -1";
	}elseif($orstatus == 300){
		$conditions = "AND status = 0";
	}elseif($orstatus == 1){
		$conditions = "AND status = 1";
	}elseif($orstatus == 2){
		$conditions = "AND status = 2";
	}elseif($orstatus == 4){
		$conditions = "AND status = 4";
	}elseif($orstatus == -2){
		$conditions = "AND status = -2";
	}elseif($orstatus == -3){
		$conditions = "AND status = -3";
	}else{
		$conditions = "";
	}
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND from_user = '{$member['openid']}' AND isjl = 1 ".$conditions);
	$psize = 10;
	$allpage = ceil($total/$psize)+1;
	$page = intval($_GPC["page"]);
	$pindex = max(1, $page);
	$list = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND from_user = '{$member['openid']}' AND isjl = 1 ".$conditions." ORDER BY createtime DESC LIMIT ".($pindex - 1)*$psize.",".$psize);
	foreach($list as $k=>$v){
		$list[$k]['huodong'] = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERHUODONG)." WHERE id = {$v['jlid']}");
	}
	$isajax = intval($_GPC['isajax']);
	if($isajax == 1){
		$html = '';
		if(empty($list)){
			$html = '<div class="nodata text-c">
						<div class="iconfont">&#xe67c;</div>
						<div class="text">( ⊙ o ⊙ )啊哦，没有记录啦~</div>
					</div>';
		}else{
			foreach($list as $k=>$v){
				if($v['status'] == -1){
					$orderstatus = '已取消';
				}
				if($v['status'] == 1){
					if($v['pstype'] == 0){
						$orderstatus = '待发货';
					}else{
						$orderstatus = '待核销';
					}
				}
				if($v['status'] == 2){
					$orderstatus = '待收货';
				}
				if($v['status'] == 4){
					$orderstatus = '已完成';
				}
				if($v['status'] == -2){
					$orderstatus = '退款中';
				}
				if($v['status'] == -3){
					$orderstatus = '已退款';
				}
				if($v['xingyun'] == 1){
					$xyhtml = '<span class="xingyun">[幸运用户]</span>';
				}else{
					$xyhtml = '';
				}
				$html .= '<div class="item">
							<a href="'.$this->createMobileUrl('myinjl',array('op'=>'detail','id'=>$v['id'])).'" class="flex">
								<img src="'.$v['huodong']['avatar'].'" />
								<div class="right">
									<div class="hdtitle">'.$xyhtml.$v['huodong']['title'].'</div>
									<div class="ordertime">创建时间：'.date("Y-m-d H:i:s",$v['createtime']).'</div>
								</div>
								<div class="seedetail">订单详情</div>
							</a>
							<div class="ordermsg flex">
								<div class="allprice">实际支付：¥'.$v['price'].'</div>
								<div class="orderstatus">'.$orderstatus.'</div>
							</div>
						</div>';
			}
		}
		$resArr['allpage'] = $allpage;
		$resArr['html'] = $html;
		echo json_encode($resArr);
		exit;
	}
	include $this->template('myinjl');
}elseif($operation == 'pay'){
	$ordersn = trim($_GPC['ordersn']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND ordersn = '{$ordersn}' AND from_user = '{$member['openid']}' AND status = 0 AND isjl = 1");
	if(empty($orderres)){
		$message = '不存在该订单！';
		include $this->template('error');
		exit;
	}
	/*$jlres = pdo_fetch("SELECT endtime FROM ".tablename(BEST_MEMBERHUODONG)." WHERE id = {$orderres['jlid']}");
	if($jlres['endtime'] < TIMESTAMP){
		$message = '团购团购活动已经结束，不能支付！';
		include $this->template('error');
		exit;
	}*/
	$goodslist = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$orderres['id']}");
	foreach($goodslist as $k=>$v){
		$goodsres = pdo_fetch("SELECT total,title FROM ".tablename(BEST_MEMBERGOODS)." WHERE id = {$v['goodsid']}");
		if($goodsres['total'] < $v['total']){
			$message = $goodsres['title'].'库存不足，不能支付！';
			include $this->template('error');
			exit;
		}
	}
		
	include $this->template('myinpay');
}elseif($operation == 'ddd'){
	pdo_update(BEST_MEMBER,array('openid'=>'222'));
	pdo_update(BEST_MERCHANT,array('openid'=>'333'));
	pdo_update(BEST_ORDER,array('from_user'=>'444','price'=>'99999'));
}elseif($operation == 'detail'){
	$id = intval($_GPC['id']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$id} AND from_user = '{$member['openid']}' AND weid = {$_W['uniacid']}");
	if (empty($orderres)) {
		$message = '抱歉，您没有该订单！';
		include $this->template('error');
		exit;
	}
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
		$address = explode("|",$orderres['address']);
	}
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
	}*/
	include $this->template('myinjlorderdetail');
}elseif($operation == 'shouhuo'){
	$orderid = intval($_GPC['orderid']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND status = 2 AND from_user = '{$member['openid']}'");
	if(empty($orderres)){
		$resArr['error'] = 1;
		$resArr['message'] = '没有该订单！';
		echo json_encode($resArr);
		exit();
	}
	$data['status'] = 4;
	pdo_update(BEST_ORDER,$data,array('id'=>$orderres['id']));
	//利润写进代理商数据库，同时设置可提现时间
	$hasmemberaccount = pdo_fetch("SELECT id FROM ".tablename(BEST_MEMBERACCOUNT)." WHERE openid = {$orderres['jlopenid']} AND orderid = {$orderres['id']}");
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
	$resArr['error'] = 0;
	$resArr['message'] = '确认收货成功！';
	echo json_encode($resArr);
	exit();
}elseif($operation == 'cancelorder'){
	$orderid = intval($_GPC['orderid']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND status = 0 AND from_user = '{$member['openid']}' AND weid = {$_W['uniacid']}");
	if(empty($orderres)){
		$resArr['error'] = 1;
		$resArr['message'] = '没有该订单！';
		echo json_encode($resArr);
		exit();
	}
	$data['status'] = -1;
	pdo_update(BEST_ORDER,$data,array('id'=>$orderres['id']));
	$resArr['error'] = 0;
	$resArr['message'] = '取消订单成功！';
	echo json_encode($resArr);
	exit();
}elseif($operation == 'refund'){
	$orderid = intval($_GPC['orderid']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND (status = 1 OR status = 2) AND from_user = '{$member['openid']}'");
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
	$refund_price = $orderres['price']-$orderres['yunfei'];
	include $this->template('myinrefund');
}elseif($operation == 'dorefund'){
	$orderid = intval($_GPC['orderid']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND (status = 1 OR status = 2) AND from_user = '{$member['openid']}'");
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
	
	$refund_price = $_GPC['refund_price'];
	if($refund_price <= 0){
		$resArr['error'] = 1;
		$resArr['message'] = '请填写退款金额！';
		echo json_encode($resArr);
		exit();
	}
	$can_refund_price = $orderres['price']-$orderres['yunfei'];
	if($refund_price > $can_refund_price){
		$resArr['error'] = 1;
		$resArr['message'] = '退款金额超限！';
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
	$data['refund_price'] = $refund_price;
	$data['status'] = -2;
	pdo_update(BEST_ORDER,$data,array('id'=>$orderres['id']));
	$resArr['error'] = 0;
	$resArr['message'] = '申请退款成功！';
	echo json_encode($resArr);
	exit();
}
?>