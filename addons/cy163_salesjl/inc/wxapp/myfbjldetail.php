<?php
global $_W,$_GPC;
$member['openid'] = trim($_GPC['openid']);
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
	$id = intval($_GPC['id']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$id} AND jlopenid = '{$member['openid']}' AND weid = {$_W['uniacid']} AND isjl = 1");

	$member = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$orderres['from_user']}' AND weid = {$_W['uniacid']}");
	//商品信息
	$ordergoods = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$orderres['id']}");
	if($orderres['pstype'] == 0){
		$orderres['address'] = explode("|",$orderres['address']);
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
		//构造电子面单提交信息
		/*$eorder = [];
		$eorder["ShipperCode"] = $orderres['expresscom'];
		$eorder["OrderCode"] = $orderres['expresssn'];
		$eorder["PayType"] = 1;
		$eorder["ExpType"] = 1;
		
		$addddd = explode(" ",$orderres['address'][2]);
		$sender = [];
		$sender["Name"] = $orderres['address'][0];
		$sender["Mobile"] = $orderres['address'][1];
		$sender["ProvinceName"] = $addddd[0];
		$sender["CityName"] = $addddd[1];
		$sender["ExpAreaName"] = $addddd[2];
		$sender["Address"] = $orderres['address'][3];

		$receiver = [];
		$receiver["Name"] = $orderres['address'][0];
		$receiver["Mobile"] = $orderres['address'][1];
		$receiver["ProvinceName"] = $addddd[0];
		$receiver["CityName"] = $addddd[1];
		$receiver["ExpAreaName"] = $addddd[2];
		$receiver["Address"] = $orderres['address'][3];

		$commodityOne = [];
		$commodityOne["GoodsName"] = "其他";
		$commodity = [];
		$commodity[] = $commodityOne;

		$eorder["Sender"] = $sender;
		$eorder["Receiver"] = $receiver;
		$eorder["Commodity"] = $commodity;
		
		//调用电子面单
		$jsonParam = json_encode($eorder, JSON_UNESCAPED_UNICODE);
		$jsonResult = $this->submitEOrder($jsonParam);
		$result = json_decode($jsonResult, true);
		$expressres = $result["ResultCode"];
		//rsort($expressres);
	}else{*/
		$expressres = '';
	/*}*/

	$orderres['time'] = date("Y-m-d H:i:s",$orderres['createtime']);
	$res['orderres'] = $orderres;
	$res['ordergoods'] = $ordergoods;
	$res['expressres'] = $expressres;
	$this->result(0,"团购订单详情！",$res);
}elseif($operation == 'fahuo'){
	$express = array(
		array(
			'pinyin'=>'ZFPS',
			'value'=>'自发配送',
		),
		array(
			'pinyin'=>'SF',
			'value'=>'顺丰速运',
		),
		array(
			'pinyin'=>'HTKY',
			'value'=>'百世快递',
		),
		array(
			'pinyin'=>'ZTO',
			'value'=>'中通快递',
		),
		array(
			'pinyin'=>'STO',
			'value'=>'申通快递',
		),
		array(
			'pinyin'=>'YTO',
			'value'=>'圆通速递',
		),
		array(
			'pinyin'=>'YD',
			'value'=>'韵达速递',
		),
		array(
			'pinyin'=>'YZPY',
			'value'=>'邮政快递包裹',
		),
		array(
			'pinyin'=>'EMS',
			'value'=>'EMS',
		),
		array(
			'pinyin'=>'HHTT',
			'value'=>'天天快递',
		),
		array(
			'pinyin'=>'JD',
			'value'=>'京东物流',
		),
		array(
			'pinyin'=>'QFKD',
			'value'=>'全峰快递',
		),
		array(
			'pinyin'=>'GTO',
			'value'=>'国通快递',
		),
		array(
			'pinyin'=>'UC',
			'value'=>'优速快递',
		),
		array(
			'pinyin'=>'DBL',
			'value'=>'德邦',
		),
		array(
			'pinyin'=>'FAST',
			'value'=>'快捷快递',
		),
		array(
			'pinyin'=>'ZJS',
			'value'=>'宅急送',
		),
	);
	$this->result(0,"发货！", $express);
}elseif($operation == 'dofahuo'){
	if (empty($_GPC['expresscom']) || empty($_GPC['expressname']) || empty($_GPC['expresssn'])) {
		$this->result(1,"请选择选择快递公司并且输入快递单号！","");
	}
	$orderid = intval($_GPC['orderid']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND jlopenid = '{$member['openid']}' AND weid = {$_W['uniacid']}");
	if($orderres['status'] != 1){
		$this->result(1,"订单当前状态不能使用发货操作！","");
	}
	$data['status'] = 2;
	$data['express'] = $_GPC['expressname'];
	$data['expresscom'] = $_GPC['expresscom'];
	$data['expresssn'] = $_GPC['expresssn'];
	$data['fhtime'] = TIMESTAMP;
	pdo_update(BEST_ORDER,$data,array('id' => $orderid));
	
	if($this->module['config']['istplon'] == 1){
		$temvalue = array(
			"keyword1"=>array(
				"value"=>$_GPC['expressname'],
				"color"=>"#4a4a4a"
			),
			"keyword2"=>array(
				"value"=>$_GPC['expresssn'],
				"color"=>"#9b9b9b"
			),
			"keyword3"=>array(
				"value"=>$orderres['ordersn'],
				"color"=>"#9b9b9b"
			),
			"keyword4"=>array(
				"value"=>date("Y-m-d H:i:s",TIMESTAMP),
				"color"=>"#9b9b9b"
			)
		);
		
		$account_api = WeAccount::create();
		$access_token = $account_api->getAccessToken();
		$url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token;
		$dd = array();
		$dd['touser'] = $orderres['from_user'];
		$dd['template_id'] = $this->module['config']['tpl_or_fahuo'];
		$dd['page'] = 'cy163_salesjl/pages/orderdetail/orderdetail?id='.$orderid; //点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,该字段不填则模板无跳转。
		$dd['form_id'] = $_GPC['formid'];
		$dd['data'] = $temvalue;                        //模板内容，不填则下发空模板
		$dd['color'] = '';                        //模板内容字体的颜色，不填默认黑色
		$dd['emphasis_keyword'] = '';    //模板需要放大的关键词，不填则默认无放大
		//$dd['emphasis_keyword']='keyword1.DATA';
		//$send = json_encode($dd);   //二维数组转换成json对象
		
		/* curl_post()进行POST方式调用api： api.weixin.qq.com*/
		//load()->func('communication');
		//$result = ihttp_post($url,json_encode($dd));
		$result = $this->https_curl_json($url,$dd,'json');
		/*if($result){
			echo json_encode(array('state'=>5,'msg'=>$result));
		}else{
			echo json_encode(array('state'=>5,'msg'=>$result));
		}*/
	}
	
	$this->result(0,"发货成功！","");
}elseif($operation == 'hexiao'){
	$orderid = intval($_GPC['orderid']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND jlopenid = '{$member['openid']}' AND weid = {$_W['uniacid']}");
	if($orderres['status'] != 1){
		$this->result(1,"订单当前状态不能使用核销操作！","");
	}
	$data['status'] = 4;
	pdo_update(BEST_ORDER,$data,array('id' => $orderid));
	$hasmemberaccount = pdo_fetch("SELECT id FROM ".tablename(BEST_MEMBERACCOUNT)." WHERE openid = '{$orderres['jlopenid']}' AND orderid = {$orderres['id']}");
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
	$this->result(0,"发货成功！","");
}elseif($operation == 'cancelorder'){
	$orderid = intval($_GPC['id']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND status = 0 AND jlopenid = '{$member['openid']}' AND weid = {$_W['uniacid']}");
	if(empty($orderres)){
		$this->result(1,"不存在该订单！", '');
	}
	$data['status'] = -1;
	pdo_update(BEST_ORDER,$data,array('id'=>$orderres['id']));
	$this->result(0,"取消订单成功！", '');
}elseif($operation == 'refund') {
	/*$orderid = intval($_GPC['orderid']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND jlopenid = '{$member['openid']}' AND weid = {$_W['uniacid']}");
	if($orderres['status'] != -2){
		$this->result(1,"订单当前状态不能使用退款操作！","");
	}

	$data['status'] = -3;
	$data['refund_status'] = 1;
	pdo_update(BEST_ORDER,$data,array('id' => $orderid));

	//退款返还购买者
	$datamoney['weid'] = $_W['uniacid'];
	$datamoney['openid'] = $orderres['from_user'];
	$datamoney['money'] = $orderres['refund_price'];
	$datamoney['time'] = TIMESTAMP;
	$datamoney['orderid'] = $orderres['id'];
	$datamoney['explain'] = "订单退款返还";
	pdo_insert(BEST_MEMBERACCOUNT,$datamoney);

	$resArr['error'] = 0;
	$resArr['message'] = '同意退款成功！';
	echo json_encode($resArr);
	exit();*/
}
?>