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
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$status = $_GPC['status'];
	if($status == ''){
		$status = 100;
	}
	
	$starttime  = $_GPC['starttime'];
	$endtime  = $_GPC['endtime'];
	if (empty($starttime)) {
		$starttime = strtotime('-1 month');
	}else{
		$starttime = strtotime($_GPC['starttime']);
	}
	if (empty($endtime)) {
		$endtime = TIMESTAMP;
	}else{
		$endtime = strtotime($_GPC['endtime']);
	}
	$condition = "weid = {$_W['uniacid']} AND isjl = 1 AND createtime >= {$starttime} AND createtime <= {$endtime}";
	if (!empty($_GPC['keyword'])) {
		$condition .= " AND ordersn LIKE '%{$_GPC['keyword']}%'";
	}
	if (!empty($_GPC['member'])) {
		$condition .= " AND address LIKE '%{$_GPC['member']}%'";
	}
	if (!empty($_GPC['faqiren'])) {
		$faqirenres = pdo_fetch("SELECT openid FROM ".tablename(BEST_MEMBER)." WHERE nickname like '%{$_GPC['faqiren']}%'");
		$condition .= " AND jlopenid = '{$faqirenres['openid']}'";
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
	$sql = 'SELECT COUNT(*) FROM '.tablename(BEST_ORDER).' WHERE '.$condition;
	$total = pdo_fetchcolumn($sql, $paras);
	if ($total > 0) {
		if ($_GPC['export'] == '') {
			$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
		}else{
			$limit = '';
		}
		$sql = 'SELECT * FROM ' . tablename(BEST_ORDER) . ' WHERE ' . $condition . ' ORDER BY `createtime` DESC ' . $limit;
		$list = pdo_fetchall($sql);
		$pager = pagination($total, $pindex, $psize);
		if ($_GPC['export'] == 'export') {
			/* 输入到CSV文件 */
			$html = "\xEF\xBB\xBF";
			/* 输出表头 */
			$filter = array('订单号','订单状态','微信昵称','姓名','电话','商品','数量','单价','运费','总价','下单时间','备注','收货地址','提货地址','卖家信息');
			foreach ($filter as $key => $title) {
				$html .= $title . "\t,";
			}
			$html .= "\n";
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
				}else{
					$memberres['nickname'] = "";
				}
				
				$memberres2 = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$v['jlopenid']}'");
				$v['address'] = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","", $v['address']);
				$address_arr = explode("|",$v['address']);
				$goodslist = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$v['id']}");
				foreach($goodslist as $kk=>$vv){
					$vv['goodsname'] = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","", $vv['goodsname']);
					$vv['optionname'] = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","", $vv['optionname']);
					
					$html .= $v['ordersn']. "\t, ";
					$html .= $ordertetx. "\t, ";
					$html .= $memberres['nickname']. "\t, ";
					$html .= $v['pstype'] == 1 || $v['pstype'] == 4 ? "\t, " : $address_arr[0]. "\t, ";
					$html .= $v['pstype'] == 1 || $v['pstype'] == 4 ? $address_arr[0]. "\t, " : $address_arr[1]. "\t, ";
					$html .= empty($vv['optionname']) ? $vv['goodsname']. "\t, " : $vv['goodsname']."[".$vv['optionname']."]". "\t, ";
					$html .= $vv['total']. "\t, ";
					$html .= $vv['price']. "\t, ";
					$html .= $v['yunfei']. "\t, ";
					$html .= $vv['price']*$vv['total']. "\t, ";
					$html .= date("Y-m-d H:i:s",$v['createtime']). "\t, ";
					$html .= $v['remark']. "\t, ";
					$html .= $v['pstype'] == 1 || $v['pstype'] == 4 ? "\t, " : $address_arr[2].$address_arr[3].$address_arr[4]. "\t, ";
					$html .= !empty($v['ztdaddress']) ? $v['ztdaddress']. "\t, " : "\t, ";
					$html .= $memberres2['nickname']. "\t, ";
					$html .= "\n";
				}
			}
			/* 输出CSV文件 */
			header("Content-type:text/csv");
			header("Content-Disposition:attachment; filename=接龙订单数据.csv");
			echo $html;
			exit();
		}		
	}	
}elseif ($operation == 'jiesuanall') {
	if (!empty($_GPC['id'])) {
		foreach ($_GPC['id'] as $key => $id) {
			$plorder = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$id} AND status = 2 AND isjl = 1");
			if(!empty($plorder)){
				pdo_update(BEST_ORDER, array('status' => 4), array('id' => $plorder['id']));
				if($plorder['isdmfk'] == 0){					
					$hasmemberaccount = pdo_fetch("SELECT id FROM ".tablename(BEST_MEMBERACCOUNT)." WHERE openid = '{$plorder['jlopenid']}' AND orderid = {$plorder['id']}");
					if(empty($hasmemberaccount)){
						$datamoney['weid'] = $_W['uniacid'];
						$datamoney['openid'] = $plorder['jlopenid'];
						$datamoney['money'] = $plorder['price'];
						$datamoney['time'] = TIMESTAMP;
						$datamoney['orderid'] = $plorder['id'];
						$datamoney['explain'] = "接龙订单";
						$datamoney['candotime'] = TIMESTAMP + ($this->module['config']['zftxhour'])*3600;
						pdo_insert(BEST_MEMBERACCOUNT,$datamoney);
					}
				}
			}
		}
		message('批量结算订单成功！', referer(), 'success');
	}else{
		message('请选择订单进行操作！');
	}
} elseif ($operation == 'docancel') {
	if (!empty($_GPC['id'])) {
		foreach ($_GPC['id'] as $key => $id) {
			$plorder = pdo_fetch("SELECT id FROM ".tablename(BEST_ORDER)." WHERE id = {$id} AND status = 0 AND isjl = 1");
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
			$plorder = pdo_fetch("SELECT id FROM ".tablename(BEST_ORDER)." WHERE id = {$id} AND status = -1 AND isjl = 1");
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
	$goodslist = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$id}");
	$item['user'] = explode("|",$item['address']);
	if (checksubmit('finish')) {
		if($item['status'] != 2){
			message('订单当前状态不能使用该操作！');
		}
		pdo_update(BEST_ORDER, array('status' => 4), array('id' => $id, 'weid' => $_W['uniacid']));
		if($item['isdmfk'] == 0){		
			$hasmemberaccount = pdo_fetch("SELECT id FROM ".tablename(BEST_MEMBERACCOUNT)." WHERE openid = '{$item['jlopenid']}' AND orderid = {$item['id']}");
			if(empty($hasmemberaccount)){
				$datamoney['weid'] = $_W['uniacid'];
				$datamoney['openid'] = $item['jlopenid'];
				$datamoney['money'] = $item['price'];
				$datamoney['time'] = TIMESTAMP;
				$datamoney['orderid'] = $item['id'];
				$datamoney['explain'] = "接龙订单";
				$datamoney['candotime'] = TIMESTAMP + ($this->module['config']['zftxhour'])*3600;
				pdo_insert(BEST_MEMBERACCOUNT,$datamoney);
			}
		}
		
		message('完成订单成功！', referer(), 'success');
	}
	// 订单取消
	if (checksubmit('cancelorder')) {
		if($item['status'] != 0){
			message('订单当前状态不能使用该操作！');
		}
		pdo_update(BEST_ORDER, array('status' => '-1'), array('id' => $item['id']));
		message('订单取消操作成功！', referer(), 'success');
	}
}
include $this->template('web/jlorder');
?>