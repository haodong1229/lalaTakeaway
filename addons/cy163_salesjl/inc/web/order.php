<?php
global $_W, $_GPC;
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
$merchant = pdo_fetchall("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} ORDER BY addtime DESC");
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
	$huodong = pdo_fetchall("SELECT * FROM ".tablename(BEST_HUODONG)." WHERE weid = {$_W['uniacid']}");
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$status = $_GPC['status'];
	if($status == ''){
		$status = 100;
	}
	
	$starttime  = $_GPC['starttime'];
	$endtime  = $_GPC['endtime'];
	if (empty($starttime)) {
		$starttime = strtotime('-1 year');
	}else{
		$starttime = strtotime($_GPC['starttime']);
	}
	if (empty($endtime)) {
		$endtime = TIMESTAMP;
	}else{
		$endtime = strtotime($_GPC['endtime']);
	}
	$condition = "weid = {$_W['uniacid']} AND isjl = 0 AND createtime >= {$starttime} AND createtime <= {$endtime}";

	
	
	if (!empty($_GPC['keyword'])) {
		$condition .= " AND ordersn LIKE '%{$_GPC['keyword']}%'";
	}
	if (!empty($_GPC['merchant_id'])) {
		$condition .= " AND merchant_id = {$_GPC['merchant_id']}";
	}
	if (!empty($_GPC['hdid'])) {
		$condition .= " AND hdid = {$_GPC['hdid']}";
	}
	if (!empty($_GPC['pstype'])) {
		if($_GPC['pstype'] == 100){
			$condition .= " AND pstype = 0";
		}else{
			if($_GPC['pstype'] == 4){
				$condition .= " AND (pstype = 1 OR pstype = 4)";
			}
		}
	}
	if (!empty($_GPC['member'])) {
		$condition .= " AND address LIKE '%{$_GPC['member']}%'";
	}
	if ($status != 100) {
		if($status == 3){
			$condition .= " AND status = 4";
		}else{
			$condition .= " AND status = {$status}";
		}
	}
	$iszt = intval($_GPC['iszt']);
	if($iszt == 1){
		$condition .= " AND ztdid > 0";
	}
	if($iszt == 2){
		$condition .= " AND ztdid = 0";
	}
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(BEST_ORDER) . ' WHERE ' . $condition);
	if ($total > 0) {
		if ($_GPC['export'] == '') {
			$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
		}else{
			$limit = '';
		}
		$sql = 'SELECT * FROM ' . tablename(BEST_ORDER) . ' WHERE ' . $condition . ' ORDER BY `createtime` DESC ' . $limit;
		$list = pdo_fetchall($sql);
		$pager = pagination($total, $pindex, $psize);
		if($status == 1 || $status == 2){
			$list2 = pdo_fetchall('SELECT * FROM ' . tablename(BEST_ORDER) . ' WHERE ' . $condition . ' ORDER BY `createtime` DESC ');
			$oids = "0";
			foreach($list2 as $k=>$v){
				$oids .= "-".$v['id'];
			}
		}
		if ($_GPC['export'] == 'export') {
			/* 输入到CSV文件 */
			$html = "\xEF\xBB\xBF";
			/* 输出表头 */
			$filter = array('订单号','订单状态','微信昵称','姓名','电话','商品','数量','单价','运费','总价','下单时间','备注','收货地址','提货地址','代理商信息');
			foreach ($filter as $key => $title) {
				$html .= $title . "\t,";
			}
			
			
			foreach ($list as $k => $v) {
				$ordertetx = '';
				if($v['status'] == -1){
					$ordertetx = '已取消';
				}
				if($v['status'] == 0){
					$ordertetx = '待付款';
				}
				if($v['status'] == 1){
					if($v['ztdid'] == 0){
						$ordertetx = '待发货';
					}else{
						$ordertetx = '待自提';
					}
				}
				if($v['status'] == 2){
					$ordertetx = '待收货';
				}
				if($v['status'] == 4){
					$ordertetx = '已完成';
				}
				if($v['status'] == -2){
					$ordertetx = '申请退款';
				}
				if($v['status'] == -3){
					$ordertetx = '退款成功';
				}
				$v['ztdaddress'] = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","", $v['ztdaddress']);
				if($v['pstype'] == 1 || $v['pstype'] == 4){
					$memberres = pdo_fetch("SELECT nickname FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$v['from_user']}'");
					$memberres['nickname'] = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","", $memberres['nickname']);
					$memberres['nickname'] = str_replace(",","",$memberres['nickname']);
				}else{
					$memberres['nickname'] = "";
				}
				
				$isdmfk = $v['isdmfk'] == 1 ? '[当面付款]' : '';
				
				$merchantres = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE id = {$v['merchant_id']}");
				$merchantres['realname'] = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","", $merchantres['realname']);
				$merchantres['telphone'] = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","", $merchantres['telphone']);
				$merchantres['address'] = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","", $merchantres['address']);
				
				$v['address'] = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","", $v['address']);
				$address_arr = explode("|",$v['address']);
				$goodslist = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$v['id']}");
				foreach($goodslist as $kk=>$vv){
					$vv['goodsname'] = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","", $vv['goodsname']);
					$vv['optionname'] = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","", $vv['optionname']);
					
					$html .= "\n";
					$html .= $isdmfk.$v['ordersn']. "\t, ";
					$html .= $ordertetx. "\t, ";
					$html .= $memberres['nickname']. "\t, ";
					$html .= $v['pstype'] == 1 || $v['pstype'] == 4 ? $address_arr[1]. "\t, " : $address_arr[0]. "\t, ";
					$html .= $v['pstype'] == 1 || $v['pstype'] == 4 ? $address_arr[0]. "\t, " : $address_arr[1]. "\t, ";
					$html .= empty($vv['optionname']) ? $vv['goodsname']. "\t, " : $vv['goodsname']."[".$vv['optionname']."]". "\t, ";
					$html .= $vv['total']. "\t, ";
					$html .= $vv['price']. "\t, ";
					$html .= $v['yunfei']. "\t, ";
					$html .= $vv['price']*$vv['total']. "\t, ";
					$html .= date("Y-m-d H:i:s",$v['createtime']). "\t, ";
					$html .= preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","", $v['remark']). "\t, ";  //有空格导出会有问题
					$html .= $v['pstype'] == 1 || $v['pstype'] == 4 ? "\t, " : $address_arr[2].$address_arr[3].$address_arr[4]. "\t, ";
					$html .= !empty($v['ztdaddress']) ? $v['ztdaddress']. "\t, " : "\t, ";
					$html .= $merchantres['realname'].' '.$merchantres['telphone'].' '.$merchantres['address'];
				}
			}
			/* 输出CSV文件 */
			header("Content-type:text/csv");
			header("Content-Disposition:attachment; filename=订单数据.csv");
			echo $html;
			exit();
		}		
	}
}elseif ($operation == 'plfahuo') {
	if (!empty($_GPC['id'])) {
		$list = pdo_fetchall("SELECT id,price,ordersn,address,otheraddress,createtime FROM ".tablename(BEST_ORDER)." WHERE id in (".implode(",",$_GPC['id']).") AND status = 1 AND isjl = 0");
	}else{
		message('请选择订单进行操作！');
	}
}elseif ($operation == 'doplfahuo') {
	foreach ($_GPC['id'] as $k=>$v) {
		if($_GPC['express'][$k] != "" && $_GPC['expresscom'][$k] != "" && $_GPC['expresssn'][$k] != ""){
			$data['status'] = 2;
			$data['express'] = $_GPC['express'][$k];
			$data['expresscom'] = $_GPC['expresscom'][$k];
			$data['expresssn'] = $_GPC['expresssn'][$k];
			$data['fhtime'] = TIMESTAMP;
			pdo_update(BEST_ORDER,$data,array('id'=>$v));
		}
	}
	message('批量发货成功！', referer(), 'success');
}elseif ($operation == 'jiesuanall') {
	if (!empty($_GPC['id'])) {
		foreach ($_GPC['id'] as $key => $id) {
			$plorder = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$id} AND status = 2 AND isjl = 0");
			if(!empty($plorder)){
				pdo_update(BEST_ORDER, array('status' => 4), array('id' => $plorder['id']));
				if($plorder['isdmfk'] == 0 && $plorder['merchant_id'] > 0){
					$hasmerchantaccount = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANTACCOUNT)." WHERE merchant_id = {$plorder['merchant_id']} AND orderid = {$plorder['id']}");
					if(empty($hasmerchantaccount)){
						$dataaccount = array(
							'weid'=>$_W['uniacid'],
							'merchant_id'=>$plorder['merchant_id'],
							'money'=>$plorder['alllirun'],
							'time'=>TIMESTAMP,
							'explain'=>"代理团结算",
							'orderid'=>$plorder['id'],
							'candotime'=>TIMESTAMP + ($this->module['config']['dltxhour'])*3600,
						);
						pdo_insert(BEST_MERCHANTACCOUNT,$dataaccount);
					}
				}
			}
		}
		message('批量结算订单成功！', referer(), 'success');
	}else{
		message('请选择订单进行操作！');
	}
}elseif ($operation == 'jiesuanallall') {
	$oids = trim($_GPC['oids']);
	$oidsarr = explode("-",$oids);
	$allorders = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id in (".implode(",",$oidsarr).") AND status = 2 AND isjl = 0");
	foreach($allorders as $k=>$v){
		pdo_update(BEST_ORDER, array('status' => 4), array('id' => $v['id']));
		if($v['isdmfk'] == 0 && $v['merchant_id'] > 0){
			$hasmerchantaccount = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANTACCOUNT)." WHERE merchant_id = {$v['merchant_id']} AND orderid = {$v['id']}");
			if(empty($hasmerchantaccount)){
				$dataaccount = array(
					'weid'=>$_W['uniacid'],
					'merchant_id'=>$v['merchant_id'],
					'money'=>$v['alllirun'],
					'time'=>TIMESTAMP,
					'explain'=>"代理团结算",
					'orderid'=>$v['id'],
					'candotime'=>TIMESTAMP + ($this->module['config']['dltxhour'])*3600,
				);
				pdo_insert(BEST_MERCHANTACCOUNT,$dataaccount);
			}
		}
	}
	message('一键强制完成所有订单成功！', referer(), 'success');
}elseif ($operation == 'jiesuanallzt') {
	if (!empty($_GPC['id'])) {
		foreach ($_GPC['id'] as $key => $id) {
			$plorder = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$id} AND status = 1 AND isjl = 0 AND ztdid > 0");
			if(!empty($plorder)){
				pdo_update(BEST_ORDER, array('status' => 4), array('id' => $plorder['id']));
				if($plorder['isdmfk'] == 0 && $plorder['merchant_id'] > 0){
					$hasmerchantaccount = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANTACCOUNT)." WHERE merchant_id = {$plorder['merchant_id']} AND orderid = {$plorder['id']}");
					if(empty($hasmerchantaccount)){
						$dataaccount = array(
							'weid'=>$_W['uniacid'],
							'merchant_id'=>$plorder['merchant_id'],
							'money'=>$plorder['alllirun'],
							'time'=>TIMESTAMP,
							'explain'=>"代理团结算",
							'orderid'=>$plorder['id'],
							'candotime'=>TIMESTAMP + ($this->module['config']['dltxhour'])*3600,
						);
						pdo_insert(BEST_MERCHANTACCOUNT,$dataaccount);
					}
				}
			}
		}
		message('批量结算自提订单成功！', referer(), 'success');
	}else{
		message('请选择订单进行操作！');
	}
}elseif ($operation == 'jiesuanallztall') {
	$oids = trim($_GPC['oids']);
	$oidsarr = explode("-",$oids);
	$allorders = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id in (".implode(",",$oidsarr).") AND status = 1 AND isjl = 0 AND ztdid > 0");
	foreach($allorders as $k=>$v){
		pdo_update(BEST_ORDER, array('status' => 4), array('id' => $v['id']));
		if($v['isdmfk'] == 0 && $v['merchant_id'] > 0){
			$hasmerchantaccount = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANTACCOUNT)." WHERE merchant_id = {$v['merchant_id']} AND orderid = {$v['id']}");
			if(empty($hasmerchantaccount)){
				$dataaccount = array(
					'weid'=>$_W['uniacid'],
					'merchant_id'=>$v['merchant_id'],
					'money'=>$v['alllirun'],
					'time'=>TIMESTAMP,
					'explain'=>"代理团结算",
					'orderid'=>$v['id'],
					'candotime'=>TIMESTAMP + ($this->module['config']['dltxhour'])*3600,
				);
				pdo_insert(BEST_MERCHANTACCOUNT,$dataaccount);
			}
		}
	}
	message('一键强制完成所有自提订单成功！', referer(), 'success');
}elseif ($operation == 'docancel') {
	if (!empty($_GPC['id'])) {
		foreach ($_GPC['id'] as $key => $id) {
			$plorder = pdo_fetch("SELECT id FROM ".tablename(BEST_ORDER)." WHERE id = {$id} AND status = 0 AND isjl = 0");
			if(!empty($plorder)){
				pdo_update(BEST_ORDER, array('status' => -1), array('id' => $plorder['id']));
			}
		}
		message('批量取消订单成功！', referer(), 'success');
	}else{
		message('请选择订单进行操作！');
	}
}elseif ($operation == 'dodelete') {
	if (!empty($_GPC['id'])) {
		foreach ($_GPC['id'] as $key => $id) {
			$plorder = pdo_fetch("SELECT id FROM ".tablename(BEST_ORDER)." WHERE id = {$id} AND status = -1 AND isjl = 0");
			if(!empty($plorder)){
				pdo_delete(BEST_ORDER, array('id' => $plorder['id']));
				pdo_delete(BEST_ORDERGOODS, array('orderid' => $plorder['id']));
			}
		}
		message('批量删除订单成功！', referer(), 'success');
	}else{
		message('请选择订单进行操作！');
	}
}elseif ($operation == 'detail') {
	$id = intval($_GPC['id']);
	$item = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$id} AND weid = {$_W['uniacid']}");
	if (empty($item)) {
		message("抱歉，订单不存在!", referer(), "error");
	}
	$member = pdo_fetch("SELECT nickname FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$item['from_user']}' AND weid = {$_W['uniacid']}");
	if (checksubmit('confirmsend')) {
		if (empty($_GPC['expresscom']) || empty($_GPC['express']) || empty($_GPC['expresssn'])) {
			message('请选择选择快递公司并且输入快递单号！');
		}
		if($item['status'] != 1 && $item['status'] != 2){
			message('订单当前状态不能使用该操作！');
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
					'value' => $item['ordersn'],
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
			$account_api->sendTplNotice($item['from_user'],$this->module['config']['tpl_or_fahuo'],$postdata,$or_paysuccess_redirect,'#FF5454');		
		}
		message('操作成功！', referer(), 'success');
	}
	if (checksubmit('finish')) {
		if($item['status'] != 2){
			message('订单当前状态不能使用该操作！');
		}
		pdo_update(BEST_ORDER, array('status' => 4), array('id' => $id, 'weid' => $_W['uniacid']));
		if($item['isdmfk'] == 0 && $item['merchant_id'] > 0){
			//利润写进代理商数据库，同时设置可提现时间
			$hasmerchantaccount = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANTACCOUNT)." WHERE merchant_id = {$item['merchant_id']} AND orderid = {$item['id']}");
			if(empty($hasmerchantaccount)){
				$dataaccount = array(
					'weid'=>$_W['uniacid'],
					'merchant_id'=>$item['merchant_id'],
					'money'=>$item['alllirun'],
					'time'=>TIMESTAMP,
					'explain'=>"代理团结算",
					'orderid'=>$item['id'],
					'candotime'=>TIMESTAMP + ($this->module['config']['dltxhour'])*3600,
				);
				pdo_insert(BEST_MERCHANTACCOUNT,$dataaccount);
			}
		}
		message('完成订单成功！', referer(), 'success');
	}
	if (checksubmit('confrimpay')) {
		if($item['status'] != 0){
			message('订单当前状态不能使用该操作！');
		}
		pdo_update(BEST_ORDER, array('status' => 1), array('id' => $id, 'weid' => $_W['uniacid']));
		message('确认订单付款操作成功！', referer(), 'success');
	}
	// 订单取消
	if (checksubmit('cancelorder')) {
		if($item['status'] != 0){
			message('订单当前状态不能使用该操作！');
		}
		pdo_update(BEST_ORDER, array('status' => '-1'), array('id' => $item['id']));
		message('订单取消操作成功！', referer(), 'success');
	}
	$goodslist = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$id}");
	$item['user'] = explode("|",$item['address']);
}elseif ($operation == 'delete') {
	/*订单删除*/
	$orderid = intval($_GPC['id']);
	$orderres = pdo_fetch("SELECT id FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND status = -1");
	if(empty($orderres)){
		message('订单不存在或已被删除', $this->createWebUrl('order', array('op' => 'display')), 'error');
	}
	pdo_delete(BEST_ORDER, array('id' => $orderid));
	pdo_delete(BEST_ORDERGOODS, array('orderid' => $orderid));
	message('删除订单成功！', $this->createWebUrl('order', array('status' => -1)), 'success');
}
include $this->template('web/order');
?>