<?php
global $_W,$_GPC;
$operation = empty($_GPC['op']) ? 'display' : $_GPC['op'];
if($operation == 'display'){
	$isjl = intval($_GPC['isjl']);
	$status = $_GPC['status'];
	if($status == ""){
		$conditions = "weid = {$_W['uniacid']} AND status = -2 AND isjl = {$isjl}";
	}else{
		$conditions = "weid = {$_W['uniacid']} AND status = -3 AND isjl = {$isjl}";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_ORDER)." WHERE ".$conditions);
	$list = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE ".$conditions." ORDER BY tktime DESC LIMIT ".($pindex - 1)*$psize.",".$psize);	
	foreach($list as $k=>$v){
		$memberrz = pdo_fetch("SELECT rzrealname FROM ".tablename(BEST_MEMBERRZ)." WHERE openid = '{$v['from_user']}' AND rzstatus = 1 AND isjujue = 0 AND weid = {$_W['uniacid']}");
		$memberres = pdo_fetch("SELECT nickname FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$v['from_user']}' AND weid = {$_W['uniacid']}");
		$list[$k]['nickname'] = str_replace(array("\r\n", "\r", "\n"), "", $memberres['nickname']);
		$list[$k]['nickname'] = str_replace("'", "", $list[$k]['nickname']);
		if(empty($memberrz)){
			$list[$k]['realname'] = "未实名";
		}else{
			$list[$k]['realname'] = $memberrz['rzrealname'];
		}
	}
	$pager = pagination($total, $pindex, $psize);
}elseif($operation == 'outcash'){
	$ids=$_GPC['ids'];
	if(empty($ids)){
		returnError('请选择要退款的记录');
	}
	$tuikuan = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$ids} AND status = -2 AND weid = {$_W['uniacid']}");
	if($tuikuan['refund_price'] < 0){
		returnError('该记录不能被提现！');
	}
	try {
		$ret=$this->refundByPay(array(
			'transaction_id'=>$tuikuan['transid'],
			'total_fee'=>$tuikuan['price'],
			'refund_fee'=>$tuikuan['refund_price'],
		));
	} catch (Exception $e) {
		//load()->func('logging');
		//logging_run('提现异常：'.$e->getMessage());
		$ret=false;
	}
	if(is_error($ret)){ // 发放失败
		returnError($ret);
	}else{ // 发放成功
		// 更新记录状态
		$data['refund_status'] = 1;
		$data['status'] = -3;
		pdo_update(BEST_ORDER,$data,array('id'=>$tuikuan['id']));
		
		$extend['weid'] = $_W['uniacid'];
		$extend['sqtime'] = $tuikuan['tktime'];
		$extend['time'] = TIMESTAMP;
		$extend['orderid'] = $tuikuan['id'];
		$extend['openid'] = $tuikuan['from_user'];
		$extend['type'] = 2;              //退款类型
		$extend['price'] = $tuikuan['refund_price'];//退款金额
		$extend['result'] = 'success';    //退款结果
		$extend['refund_id'] = $ret['refund_id'];         //微信退款交易号
		$extend['out_refund_no'] = $ret['out_refund_no']; //退款单号
		$extend['refund_desc'] = $tuikuan['refund_desc'];
		pdo_insert(BEST_REFUND,$extend);
		if($this->module['config']['refundstock'] == 1){
			$ordergoods = pdo_fetchall("SELECT optionid,goodsid,total FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$tuikuan['id']}");
			foreach($ordergoods as $k=>$v){
				if($v['optionid'] > 0){
					pdo_query("UPDATE ".tablename(BEST_GOODSOPTION)." SET stock = stock + {$v['total']} WHERE id = {$v['optionid']}");
				}
				pdo_query("UPDATE ".tablename(BEST_GOODS)." SET total = total + {$v['total']} WHERE id = {$v['goodsid']}");
			}
		}
		returnSuccess('退款成功!');
	}
}elseif($operation == 'shoudong'){
	$id = intval($_GPC['id']);
	$tuikuan = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$id} AND status = -2 AND weid = {$_W['uniacid']}");
	if($tuikuan['refund_price'] < 0){
		message('该记录不能被退款！');
	}
	
	// 更新记录状态
	$data['refund_status'] = 1;
	$data['status'] = -3;
	pdo_update(BEST_ORDER,$data,array('id'=>$tuikuan['id']));
	
	$extend['weid'] = $_W['uniacid'];
	$extend['sqtime'] = $tuikuan['tktime'];
	$extend['time'] = TIMESTAMP;
	$extend['orderid'] = $tuikuan['id'];
	$extend['openid'] = $tuikuan['from_user'];
	$extend['type'] = 2;              //退款类型
	$extend['price'] = $tuikuan['refund_price'];//退款金额
	$extend['result'] = 'success';    //退款结果
	$extend['refund_id'] = '';         //微信退款交易号
	$extend['out_refund_no'] = ''; //退款单号
	$extend['refund_desc'] = $tuikuan['refund_desc'];
	pdo_insert(BEST_REFUND,$extend);
	if($this->module['config']['refundstock'] == 1){
		$ordergoods = pdo_fetchall("SELECT optionid,goodsid,total FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$tuikuan['id']}");
		foreach($ordergoods as $k=>$v){
			if($v['optionid'] > 0){
				pdo_query("UPDATE ".tablename(BEST_GOODSOPTION)." SET stock = stock + {$v['total']} WHERE id = {$v['optionid']}");
			}
			pdo_query("UPDATE ".tablename(BEST_GOODS)." SET total = total + {$v['total']} WHERE id = {$v['goodsid']}");
		}
	}
	message('手动退款成功！', $this->createWebUrl('refund', array('op' => 'display')), 'success');
}
include $this->template('web/refund');
?>