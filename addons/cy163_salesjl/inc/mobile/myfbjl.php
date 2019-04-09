<?php
global $_W,$_GPC;
$member = $this->Mcheckmember();
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
if($operation == 'display'){
	$hdstatus = trim($_GPC['hdstatus']);
	if($hdstatus == 'status'){
		$conditions = "AND status = 0";
	}elseif($hdstatus == 'isxiugai'){
		$conditions = "AND isxiugai = 1";
	}elseif($hdstatus == 'owndel'){
		$conditions = "AND owndel = 1";
	}elseif($hdstatus == 'admindel'){
		$conditions = "AND admindel = 1";
	}elseif($hdstatus == 'finish'){
		$conditions = "AND owndel = 0 AND admindel = 0 AND status = 1 AND isxiugai = 0 AND endtime < ".TIMESTAMP;
	}elseif($hdstatus == 'ing'){
		$conditions = "AND owndel = 0 AND admindel = 0 AND status = 1 AND isxiugai = 0 AND endtime > ".TIMESTAMP;
	}else{
		$conditions = "";
	}
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_MEMBERHUODONG)." WHERE weid = {$_W['uniacid']} AND openid = '{$member['openid']}' ".$conditions);
	$psize = 10;
	$allpage = ceil($total/$psize)+1;
	$page = intval($_GPC["page"]);
	$pindex = max(1, $page);
	$list = pdo_fetchall("SELECT * FROM ".tablename(BEST_MEMBERHUODONG)." WHERE weid = {$_W['uniacid']} AND openid = '{$member['openid']}' ".$conditions." ORDER BY time DESC LIMIT ".($pindex - 1)*$psize.",".$psize);
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
				if($v['status'] == 0){
					$hdstatus = '<span style="color:red;">审核中</span>';
				}elseif($v['isxiugai'] == 1){
					$hdstatus = '<span style="color:red;">修改中</span>';
				}elseif($v['owndel'] == 1){
					$hdstatus = '<span style="color:red;">提前终止</span>';
				}elseif($v['admindel'] == 1){
					$hdstatus = '<span style="color:red;">平台终止</span>';
				}elseif($v['endtime'] < TIMESTAMP){
					$hdstatus = '<span style="color:red;">已结束</span>';
				}else{
					$hdstatus = '<span style="color:green;">进行中</span>';
				}
				$html .= '<div class="item">						
							<a href="'.$this->createMobileUrl('hddetail',array('id'=>$v['id'])).'">
								<div class="hdtitle">'.$v['title'].'</div>
								<div class="hdstatus">
									<span>团购状态：</span>'.$hdstatus.'
								</div>
								<div class="nums flex">
									<div class="left">参与人数：'.$v['inpeople'].'人</div>
									<div class="right text-r">创建时间：'.date("Y-m-d",$v['time']).'</div>
								</div>
							</a>
						</div>';
			}
		}
		$resArr['allpage'] = $allpage;
		$resArr['html'] = $html;
		echo json_encode($resArr);
		exit;
	}
	include $this->template('myfbjl');
}elseif($operation == 'search'){
	$id = intval($_GPC['id']);
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
	$keyword = trim($_GPC['keyword']);
	if(empty($keyword)){
		$resArr['error'] = 1;
		$resArr['message'] = "请输入订单号或下单人信息查询！";
		echo json_encode($resArr);
		exit;
	}else{
		$conditions .= " AND (ordersn like '%{$keyword}%' OR address like '%{$keyword}%')";
	}
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND jlid = {$id} AND isjl = 1 ".$conditions);
	$psize = 10;
	$allpage = ceil($total/$psize)+1;
	$page = intval($_GPC["page"]);
	$pindex = max(1, $page);
	$list = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND jlid = {$id} AND isjl = 1 ".$conditions." ORDER BY createtime DESC LIMIT ".($pindex - 1)*$psize.",".$psize);
	foreach($list as $k=>$v){
		$list[$k]['huodong'] = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERHUODONG)." WHERE id = {$v['jlid']}");
		$list[$k]['member'] = pdo_fetch("SELECT avatar FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$v['from_user']}'");
		$list[$k]['shr'] = explode("|",$v['address']);
	}
	
	$html = '';
	if(empty($list)){
		$html = '<div class="nodata text-c">
					<div class="iconfont">&#xe67c;</div>
					<div class="text">( ⊙ o ⊙ )啊哦，没有记录啦~</div>
				</div>';
	}else{	
		foreach($list as $k=>$v){
			if($v['status'] == 0){
				$orderstatus = '待付款';
			}
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
			if($v['shr'][2]){
				$shrh = '<div class="shr">'.$v['shr'][2].$v['shr'][3].'</div>';
			}else{
				$shrh = '';
			}
			$html .= '<div class="item">
						<a href="'.$this->createMobileUrl('myfbjl',array('op'=>'orderdetail','orderid'=>$v['id'])).'" class="flex">
							<img src="'.$v['member']['avatar'].'" />
							<div class="right">
								<div class="hdtitle">'.$v['huodong']['title'].'</div>
								<div class="ordertime">创建时间：'.date("Y-m-d H:i:s",$v['createtime']).'</div>
							</div>
							<div class="seedetail">订单详情</div>
						</a>
						<div class="shmsg">
							<div class="shr">'.$v['shr'][0].$v['shr'][1].'</div>
							'.$shrh.'
						</div>
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
}elseif($operation == 'detail'){
	$id = intval($_GPC['id']);
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
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND jlid = {$id} AND isjl = 1 ".$conditions);
	$psize = 10;
	$allpage = ceil($total/$psize)+1;
	$page = intval($_GPC["page"]);
	$pindex = max(1, $page);
	$list = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND jlid = {$id} AND isjl = 1 ".$conditions." ORDER BY createtime DESC LIMIT ".($pindex - 1)*$psize.",".$psize);
	foreach($list as $k=>$v){
		$list[$k]['huodong'] = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERHUODONG)." WHERE id = {$v['jlid']}");
		$list[$k]['member'] = pdo_fetch("SELECT avatar FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$v['from_user']}'");
		$list[$k]['shr'] = explode("|",$v['address']);
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
				if($v['status'] == 0){
					$orderstatus = '待付款';
				}
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
				if($v['shr'][2]){
					$shrh = '<div class="shr">'.$v['shr'][2].$v['shr'][3].'</div>';
				}else{
					$shrh = '';
				}
				$html .= '<div class="item">
							<a href="'.$this->createMobileUrl('myfbjl',array('op'=>'orderdetail','orderid'=>$v['id'])).'" class="flex">
								<img src="'.$v['member']['avatar'].'" />
								<div class="right">
									<div class="hdtitle">'.$v['huodong']['title'].'</div>
									<div class="ordertime">创建时间：'.date("Y-m-d H:i:s",$v['createtime']).'</div>
								</div>
								<div class="seedetail">订单详情</div>
							</a>
							<div class="shmsg">
								<div class="shr">'.$v['shr'][0].$v['shr'][1].'</div>
								'.$shrh.'
							</div>
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
	include $this->template('myfbjldetail');
}elseif($operation == 'orderdetail'){
	$id = intval($_GPC['orderid']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$id} AND jlopenid = '{$member['openid']}' AND weid = {$_W['uniacid']} AND isjl = 1");
	if (empty($orderres)) {
		$message = '抱歉，您没有该订单！';
		include $this->template('error');
		exit;
	}
	$member = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$orderres['from_user']}'");
	//商品信息
	$ordergoods = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$orderres['id']}");
	if($orderres['pstype'] == 0){
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
	include $this->template('myfbjlorderdetail');
}elseif($operation == 'delete'){
	$id = intval($_GPC['id']);
	$memberhd = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERHUODONG)." WHERE weid = {$_W['uniacid']} AND openid = '{$member['openid']}' AND id = {$id}");
	if(empty($memberhd)){
		$resArr['error'] = 1;
		$resArr['message'] = "不存在该团购！";
		echo json_encode($resArr);
		exit();
	}
	$data['owndel'] = 1;
	pdo_update(BEST_MEMBERHUODONG,$data,array('id'=>$id));
	$data2['status'] = -1;
	pdo_update(BEST_ORDER,$data2,array('jlid'=>$id,'status'=>0));
	$this->jlorderrefund($id);
	$resArr['message'] = "结束团购成功！";
	$resArr['error'] = 0;
	echo json_encode($resArr);
	exit();
}elseif($operation == 'fahuo') {
	if (empty($_GPC['expresscom']) || empty($_GPC['express']) || empty($_GPC['expresssn'])) {
		$resArr['error'] = 1;
		$resArr['message'] = '请选择选择快递公司并且输入快递单号！';
		echo json_encode($resArr);
		exit();
	}
	$orderid = intval($_GPC['orderid']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND jlopenid = '{$member['openid']}' AND weid = {$_W['uniacid']}");
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
	pdo_update(BEST_ORDER,$data,array('id' => $orderid));
	if($this->module['config']['istplon'] == 1){
		$or_paysuccess_redirect = $_W["siteroot"].'app/'.str_replace("./","",$this->createMobileUrl("myinjl"));
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
}elseif($operation == 'refund') {
	/*$orderid = intval($_GPC['orderid']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND jlopenid = '{$member['openid']}' AND weid = {$_W['uniacid']}");
	if($orderres['status'] != -2){
		$resArr['error'] = 1;
		$resArr['message'] = '订单当前状态不能使用退款操作！';
		echo json_encode($resArr);
		exit();
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
	exit();
	*/
}elseif($operation == 'shouhuo') {
	$orderid = intval($_GPC['orderid']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND jlopenid = '{$member['openid']}' AND weid = {$_W['uniacid']}");
	if($orderres['status'] != 2){
		$resArr['error'] = 1;
		$resArr['message'] = '订单当前状态不能使用发货操作！';
		echo json_encode($resArr);
		exit();
	}
	$nowtime = TIMESTAMP;
	$canshtime = $orderres['fhtime']+$this->module['config']['qrshhour1']*3600;
	if($canshtime > $nowtime){
		$resArr['error'] = 1;
		$resArr['message'] = date("Y-m-d H:i:s",$canshtime).'后才能主动完成收货！';
		echo json_encode($resArr);
		exit();
	}
	$data['status'] = 4;
	pdo_update(BEST_ORDER,$data,array('id' => $orderid));
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
}elseif($operation == 'dohexiao') {
	$orderid = intval($_GPC['orderid']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND jlopenid = '{$member['openid']}' AND weid = {$_W['uniacid']} AND status = 1");
	if (empty($orderres)) {
		$resArr['error'] = 1;
		$resArr['message'] = '抱歉，没有该订单信息！';
		echo json_encode($resArr);
		exit();
	}
	$data['status'] = 4;
	pdo_update(BEST_ORDER,$data,array('id'=>$orderid));
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
	$resArr['message'] = '核销订单成功！';
	echo json_encode($resArr);
	exit();
}elseif($operation == 'xiugai'){
	$id = intval($_GPC['id']);
	$memberhd = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERHUODONG)." WHERE weid = {$_W['uniacid']} AND openid = '{$member['openid']}' AND id = {$id} AND owndel = 0 AND admindel = 0");
	if(empty($memberhd)){
		$message = "不存在该团购！";
		include $this->template('error');
		exit;
	}
	$thumbs = unserialize($memberhd['thumbs']);
	$data['isxiugai'] = 1;
	pdo_update(BEST_MEMBERHUODONG,$data,array('id'=>$id));
	
	$goodslist = pdo_fetchall("SELECT * FROM ".tablename(BEST_MEMBERGOODS)." WHERE hdid = {$id}");
	foreach($goodslist as $k=>$v){
		$hasjt = pdo_fetch("SELECT id FROM ".tablename(BEST_MEMBERGOODSJIETI)." WHERE goodsid = {$v['id']}");
		if(!empty($hasjt)){
			unset($goodslist[$k]);
		}else{
			$goodslist[$k]['imgs'] = unserialize($v['thumbs']);
		}
	}
	$mygoodsku = pdo_fetchall("SELECT * FROM ".tablename(BEST_MEMBERGOODSKU)." WHERE weid = {$_W['uniacid']} AND openid = '{$member['openid']}'");
	foreach($mygoodsku as $k=>$v){
		$thumbs = unserialize($v['thumbs']);
		$mygoodsku[$k]['thumb'] = tomedia($thumbs[0]);
	}
	include $this->template('xiugaijl');
}elseif($operation == 'doxiugai'){
	$id = intval($_GPC['id']);
	$datahd['title'] = $_GPC['title'];
	$datahd['canziti'] = intval($_GPC['canziti']);
	$datahd['cansh'] = intval($_GPC['cansh']);
	$datahd['isxiugai'] = 0;
	if(empty($datahd['title'])){
		$resArr['error'] = 1;
		$resArr['message'] = "请填写团购主题名称！";
		echo json_encode($resArr);
		exit;
	}
	$datahd['des'] = $_GPC['des'];
	if(empty($datahd['des'])){
		$resArr['error'] = 1;
		$resArr['message'] = "请填写团购主题介绍！";
		echo json_encode($resArr);
		exit;
	}
	if(empty($_GPC['thumbs'])){
		$resArr['error'] = 1;
		$resArr['message'] = "请上传团购主题图片介绍！";
		echo json_encode($resArr);
		exit;
	}
	$datahd['thumbs'] = serialize($_GPC['thumbs']);
	$datahd['starttime'] = strtotime($_GPC['starttime']);
	$datahd['endtime'] = strtotime($_GPC['endtime']);
	$datahd['yunfei'] = $_GPC['yunfei'];
	$datahd['manjian'] = $_GPC['manjian'];
	if(empty($datahd['starttime']) || empty($datahd['endtime'])){
		$resArr['error'] = 1;
		$resArr['message'] = "请选择团购主题开始时间和截止时间！";
		echo json_encode($resArr);
		exit;
	}
	if($datahd['starttime'] > $datahd['endtime']){
		$resArr['error'] = 1;
		$resArr['message'] = "团购主题开始时间不能大于截止时间！";
		echo json_encode($resArr);
		exit;
	}
	if($datahd['canziti'] == 0 && $datahd['cansh'] == 0){
		$resArr['error'] = 1;
		$resArr['message'] = "自提与送货必须选择至少选择其中一项！";
		echo json_encode($resArr);
		exit;
	}
	$datahd['xingyun'] = $_GPC['xingyun'];
	pdo_update(BEST_MEMBERHUODONG,$datahd,array('id'=>$id));
	
	
	$goodsname = $_GPC['goodsname'];
	if(empty($goodsname)){
		$resArr['error'] = 1;
		$resArr['message'] = "请上传商品！";
		echo json_encode($resArr);
		exit;
	}
	
	$goodslist = pdo_fetchall("SELECT * FROM ".tablename(BEST_MEMBERGOODS)." WHERE hdid = {$id}");
	$goodsidarr = array(-1);
	foreach($goodslist as $k=>$v){
		$hasjt = pdo_fetch("SELECT id FROM ".tablename(BEST_MEMBERGOODSJIETI)." WHERE goodsid = {$v['id']}");
		if(empty($hasjt)){
			$goodsidarr[] = $v['id'];
		}
	}
	

	foreach($goodsname as $k=>$v){
		$datagoods['title'] = $v;
		if(empty($datagoods['title'])){
			$resArr['error'] = 1;
			$resArr['message'] = "请填写商品名称！";
			echo json_encode($resArr);
			exit;
		}
		$datagoods['optionname'] = $_GPC['optionname'][$k];
		if(empty($datagoods['optionname'])){
			$resArr['error'] = 1;
			$resArr['message'] = "请填写商品规格！";
			echo json_encode($resArr);
			exit;
		}
		$datagoods['normalprice'] = $_GPC['normalprice'][$k];
		if(empty($datagoods['normalprice'])){
			$resArr['error'] = 1;
			$resArr['message'] = "请填写商品价格！";
			echo json_encode($resArr);
			exit;
		}
		$datagoods['total'] = $_GPC['total'][$k];
		if(empty($datagoods['total'])){
			$resArr['error'] = 1;
			$resArr['message'] = "请填写商品库存！";
			echo json_encode($resArr);
			exit;
		}
		$goodsthumbskey = 'goodsthumbs'.$k;
		if(empty($_GPC[$goodsthumbskey])){
			$resArr['error'] = 1;
			$resArr['message'] = "请上传商品图片！";
			echo json_encode($resArr);
			exit;
		}
		$datagoods['thumbs'] = serialize($_GPC[$goodsthumbskey]);	
		
		$datagoods['xiangounum'] = $_GPC['xiangounum'][$k];

		$hdgid = $v['mgoodsid'];		
		
		if(in_array($v['mgoodsid'],$goodsidarr)){
			foreach($goodsidarr as $kk=>$vv){
				if($vv == $v['mgoodsid']){
					unset($goodsidarr[$kk]);
				}
			}
			pdo_update(BEST_MEMBERGOODS,$datagoods,array('id'=>$hdgid));
		}else{
			$datagoods['createtime'] = TIMESTAMP;
			$datagoods['hdid'] = $id;
			$datagoods['weid'] = $_W['uniacid'];
			$datagoods['openid'] = $member['openid'];
			pdo_insert(BEST_MEMBERGOODS,$datagoods);
		}
	}
	pdo_query("DELETE FROM ".tablename(BEST_MEMBERGOODS)." WHERE hdid = {$id} AND id in (".implode(",",$goodsidarr).")");
	
	$resArr['error'] = 0;
	$resArr['message'] = "修改团购成功！";
	echo json_encode($resArr);
	exit;
}
?>