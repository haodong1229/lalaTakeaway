<?php
global $_W,$_GPC;
$openid = trim($_GPC['openid']);
$merchant = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE openid = '{$openid}' AND weid = {$_W['uniacid']}");
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
	$status = intval($_GPC['status']);
	if($status == 0){
		$title = '待付款订单';
	}
	if($status == 1){
		$title = '待发货/自提订单';
	}
	if($status == 2){
		$title = '待收货订单';
	}
	if($status == 4){
		$title = '已完成订单';
	}
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND merchant_id = {$merchant['id']} AND status = {$status}");
	$allpage = ceil($total/10)+1;
	$page = intval($_GPC["page"]);
	$pindex = max(1, $page);
	$psize = 10;
	$merchantorderlist = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND merchant_id = {$merchant['id']} AND status = {$status} ORDER BY createtime DESC LIMIT ".($pindex - 1)*$psize.",".$psize);
	
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
		$data[$k]['status'] = $statustext;
		$data[$k]['merchantname'] = $merchant['name'];
		$data[$k]['ordersn'] = $v['ordersn'];
		$data[$k]['id'] = $v['id'];
		$data[$k]['price'] = $v['price'];
	}
	$res['orderlist'] = $data;
	$res['allpage'] = $allpage;
	$res['title'] = $title;
	$this->result(0,$allpage, $res);
}elseif ($operation == 'detail') {
	$orderid = intval($_GPC['orderid']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND merchant_id = {$merchant['id']}");
	$ormember = pdo_fetch("SELECT nickname FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$orderres['from_user']}'");
	$orderres['nickname'] = $ormember['nickname'];
	$ordergoods = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$orderres['id']}");

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
	}else{
		$expressres = '';
	}
	$orderres['time'] = date("Y-m-d H:i:s",$orderres['createtime']);
	$data['order'] = $orderres;
	$data['expressres'] = $expressres;
	$data['address'] = explode("|",$orderres['address']);
	$data['goodslist'] = $ordergoods;
	$this->result(0,"订单详情", $data);
}elseif ($operation == 'cancelorder') {
	$orderid = intval($_GPC['id']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND status = 0 AND merchant_id = {$merchant['id']}");
	if(empty($orderres)){
		$this->result(1,"不存在该订单！", '');
	}else{
		$datacancel['status'] = -1;
		pdo_update(BEST_ORDER,$datacancel,array('id'=>$orderres['id']));
		$this->result(0,"取消订单成功！", '');
	}
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
		$this->result(1,"请选择选择快递公司并且输入快递单号！", '');
	}
	$id = intval($_GPC['orderid']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$id} AND merchant_id = {$merchant['id']} AND weid = {$_W['uniacid']}");
	if($orderres['status'] != 1){
		$this->result(1,"订单当前状态不能使用发货操作！", '');
	}
	$data['status'] = 2;
	$data['express'] = $_GPC['expressname'];
	$data['expresscom'] = $_GPC['expresscom'];
	$data['expresssn'] = $_GPC['expresssn'];
	$data['fhtime'] = TIMESTAMP;
	pdo_update(BEST_ORDER,$data,array('id' => $id));
	
	
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
		$dd['page'] = 'cy163_salesjl/pages/myorderdetail/myorderdetail?ordersn='.$orderres['ordersn']; //点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,该字段不填则模板无跳转。
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
	
	$this->result(0,"发货成功！", '');
}elseif($operation == 'hexiao'){	
	$orderid = intval($_GPC['orderid']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND merchant_id = {$merchant['id']} AND weid = {$_W['uniacid']}");
	if($orderres['status'] != 1){
		$this->result(1,"订单当前状态不能使用核销操作！","");
	}
	$data['status'] = 4;
	pdo_update(BEST_ORDER,$data,array('id' => $orderid));
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
	$this->result(0,"核销成功！","");
}
?>