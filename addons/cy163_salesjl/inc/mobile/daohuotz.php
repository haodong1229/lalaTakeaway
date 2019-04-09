<?php
global $_W,$_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$merchant = $this->checkmergentauth();
$id = intval($_GPC['id']);
$merchanthd = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANTHD)." WHERE id = {$id}");
if ($operation == 'display') {
	$hdorders = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE isjl = 0 AND merchant_id = {$merchant['id']} AND mhdid = {$id} AND status = 1 AND ztdid > 0 ORDER BY createtime DESC");
	foreach($hdorders as $k=>$v){
		$hdorders[$k]['member'] = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$v['from_user']}'");
	}
	include $this->template('daohuotz');
}elseif ($operation == 'do') {
	$orderid = $_GPC['orderid'];
	if(empty($orderid)){
		$resArr["error"] = 1;
		$resArr["message"] = "请选择要发送的对象！";
		echo json_encode($resArr);
		exit;
	}
	$orderids = array();
	foreach($orderid as $k=>$v){
		$orderids[] = $v;
	}
	$hdorders = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE isjl = 0 AND merchant_id = {$merchant['id']} AND mhdid = {$id} AND status = 1 AND id in ( " . implode(',', $orderids) . ") AND ztdid > 0 ORDER BY createtime DESC");
	if(!empty($hdorders)){
		$or_paysuccess_redirect = $_W["siteroot"].'app/'.str_replace("./","",$this->createMobileUrl("myorder"));
		foreach($hdorders as $k=>$v){
			if($this->module['config']['istplon'] == 1){
				$ordergoods = pdo_fetchall("SELECT goodsname FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$v['id']}");
				$goodsnames = '';
				foreach($ordergoods as $kk=>$vv){
					$goodsnames .= $vv['goodsname']."、";
				}
				$ztdres = pdo_fetch("SELECT * FROM ".tablename(BEST_ZITIDIAN)." WHERE id = {$v['ztdid']}");
				$postdata = array(
					'first' => array(
						'value' => "到货通知！",
						'color' => '#ff510'
					),
					'keyword1' => array(
						'value' => "手机号：".$v['address']."、订单号：".$v['ordersn'],
						'color' => '#ff510'
					),
					'keyword2' => array(
						'value' => $goodsnames,
						'color' => '#ff510'
					),
					'remark' => array(
						'value' => "请及时至".$ztdres['address']."领取" ,
						'color' => '#ff510'
					),							
				);
				$account_api = WeAccount::create();
				$account_api->sendTplNotice($v['from_user'],$this->module['config']['daohuo_tz'],$postdata,$or_paysuccess_redirect,'#FF5454');
			}
		}
		$data['dhtznum'] = $merchanthd['dhtznum']+1;
		pdo_update(BEST_MERCHANTHD,$data,array('id'=>$id));
	}
	$resArr["error"] = 0;
	$resArr["message"] = "发送到货通知成功！";
	echo json_encode($resArr);
	exit;
}
?>