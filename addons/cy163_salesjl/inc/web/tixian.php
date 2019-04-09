<?php
global $_W,$_GPC;
$operation = empty($_GPC['op']) ? 'display' : $_GPC['op'];
if($operation == 'display'){
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$status = $_GPC['status'];
	if(empty($status)){
		$status = 0;
	}
	$condition = " weid = {$_W['uniacid']} AND txstatus = {$status}";
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(BEST_TIXIAN) . ' WHERE ' . $condition);
	$list = pdo_fetchall("SELECT * FROM ".tablename(BEST_TIXIAN)." WHERE weid = {$_W['uniacid']} AND txstatus = {$status} ORDER BY time DESC LIMIT ".($pindex - 1)*$psize.",".$psize);
	foreach($list as $k=>$v){
		//$memberrz = pdo_fetch("SELECT rzrealname FROM ".tablename(BEST_MEMBERRZ)." WHERE openid = '{$v['openid']}' AND rzstatus = 1 AND isjujue = 0 AND weid = {$_W['uniacid']}");
		$memberres = pdo_fetch("SELECT nickname FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$v['openid']}' AND weid = {$_W['uniacid']}");
		$list[$k]['nickname'] = str_replace(array("\r\n", "\r", "\n"), "", $memberres['nickname']);
		$list[$k]['nickname'] = str_replace("'", "", $list[$k]['nickname']);		
		//if(empty($memberrz)){
		//	$list[$k]['realname2'] = "未实名";
		//}else{
		//	$list[$k]['realname2'] = $memberrz['rzrealname'];
		//}
		if($v['membertype'] == 1){
			$list[$k]['moneylist'] = pdo_fetchall("SELECT * FROM ".tablename(BEST_MEMBERACCOUNT)." WHERE openid = '{$v['openid']}' ORDER BY time DESC");
			$list[$k]['allmoney'] = pdo_fetchcolumn("SELECT SUM(money) FROM ".tablename(BEST_MEMBERACCOUNT)." WHERE openid = '{$v['openid']}'");
			$list[$k]['allmoney2'] = pdo_fetchcolumn("SELECT SUM(money) FROM ".tablename(BEST_MEMBERACCOUNT)." WHERE openid = '{$v['openid']}' AND money < 0");
			$list[$k]['allmoney3'] = pdo_fetchcolumn("SELECT SUM(money) FROM ".tablename(BEST_MEMBERACCOUNT)." WHERE openid = '{$v['openid']}' AND money >= 0");
		}else{
			$merchantres = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} AND openid = '{$v['openid']}'");
			$list[$k]['moneylist'] = pdo_fetchall("SELECT * FROM ".tablename(BEST_MERCHANTACCOUNT)." WHERE merchant_id = {$merchantres['id']} ORDER BY time DESC");
			$list[$k]['allmoney'] = pdo_fetchcolumn("SELECT SUM(money) FROM ".tablename(BEST_MERCHANTACCOUNT)." WHERE merchant_id = {$merchantres['id']}");
			$list[$k]['allmoney2'] = pdo_fetchcolumn("SELECT SUM(money) FROM ".tablename(BEST_MERCHANTACCOUNT)." WHERE merchant_id = {$merchantres['id']} AND money < 0");
			$list[$k]['allmoney3'] = pdo_fetchcolumn("SELECT SUM(money) FROM ".tablename(BEST_MERCHANTACCOUNT)." WHERE merchant_id = {$merchantres['id']} AND money >= 0");
		}
	}
	$pager = pagination($total, $pindex, $psize);
}elseif($operation == 'outcash'){
	$ids=$_GPC['ids'];
	if(empty($ids)){
		returnError('请选择要发放的记录');
	}
	$tixian = pdo_fetch("SELECT * FROM ".tablename(BEST_TIXIAN)." WHERE id = {$ids} AND weid = {$_W['uniacid']}");
	if($tixian['money'] > 0 || $tixian['txstatus'] != 0){
		returnError('该记录不能被提现！');
	}
	try {
		$ret=$this->transferByPay(array(
			'id'=>$tixian['id'],
			'openid'=>$tixian['openid'],
			'amount'=>abs($tixian['dzprice']*100),
			'desc'=>'提现'.abs($tixian['dzprice']).'元',
		));
	} catch (Exception $e) {
		load()->func('logging');
		logging_run('提现异常：'.$e->getMessage());
		$ret=false;
	}
	if(is_error($ret)){ // 发放失败
		returnError($ret);
	}else{ // 发放成功
		// 更新记录状态
		$data['txstatus'] = 1;
		$data['dztime'] = strtotime($ret['payment_time']);
		pdo_update(BEST_TIXIAN,$data,array('id'=>$tixian['id']));
		returnSuccess('发放成功!');
	}
}elseif($operation=='refusecash'){
	// 拒绝
	$id=$_GPC['id'];
	$tixian = pdo_fetch("SELECT * FROM ".tablename(BEST_TIXIAN)." WHERE id = {$id} AND weid = {$_W['uniacid']}");
	if(empty($tixian)){
		returnError('请选择要拒绝的记录');
	}
	$data['txstatus'] = -1;
	pdo_update(BEST_TIXIAN,$data,array('id'=>$id));
	if($tixian['membertype'] == 1){
		$data2 = array(
			'weid'=>$_W['uniacid'],
			'openid'=>$tixian['openid'],
			'money'=>abs($tixian['money']),
			'time'=>TIMESTAMP,
			'explain'=>'提现失败返还',
		);
		pdo_insert(BEST_MEMBERACCOUNT,$data2);
	}else{
		$merchant = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE openid = '{$tixian['openid']}'");
		$data2 = array(
			'weid'=>$_W['uniacid'],
			'merchant_id'=>$merchant['id'],
			'money'=>abs($tixian['money']),
			'time'=>TIMESTAMP,
			'explain'=>'提现失败返还',
		);
		pdo_insert(BEST_MERCHANTACCOUNT,$data2);
	}
	returnSuccess('成功拒绝');
}elseif($operation=='delete'){
	$id=$_GPC['id'];
	$tixian = pdo_fetch("SELECT * FROM ".tablename(BEST_TIXIAN)." WHERE id = {$id} AND weid = {$_W['uniacid']}");
	if(empty($tixian)){
		message('请选择要删除的记录！');
	}
	pdo_delete(BEST_TIXIAN, array('id' => $id));
	message('删除成功！', $this->createWebUrl('tixian', array('op' => 'display')), 'success');
}elseif($operation=='finishsd'){
	$id=$_GPC['id'];
	$tixian = pdo_fetch("SELECT * FROM ".tablename(BEST_TIXIAN)." WHERE id = {$id} AND weid = {$_W['uniacid']}");
	if(empty($tixian)){
		message('请选择要发放的记录');
	}
	$data['txstatus'] = 1;
	$data['dztime'] = TIMESTAMP;
	pdo_update(BEST_TIXIAN,$data,array('id'=>$tixian['id']));
	message('完成发放成功！', $this->createWebUrl('tixian', array('op' => 'display')), 'success');
}elseif($operation=='jjsd'){
	$id=$_GPC['id'];
	$tixian = pdo_fetch("SELECT * FROM ".tablename(BEST_TIXIAN)." WHERE id = {$id} AND weid = {$_W['uniacid']}");
	if(empty($tixian)){
		message('请选择要拒绝的记录');
	}
	$data['txstatus'] = -1;
	pdo_update(BEST_TIXIAN,$data,array('id'=>$id));	
	if($tixian['membertype'] == 1){
		$data2 = array(
			'weid'=>$_W['uniacid'],
			'openid'=>$tixian['openid'],
			'money'=>abs($tixian['money']),
			'time'=>TIMESTAMP,
			'explain'=>'提现失败返还',
		);
		pdo_insert(BEST_MEMBERACCOUNT,$data2);
	}else{
		$merchant = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANT)." WHERE openid = '{$tixian['openid']}' AND weid = {$_W['uniacid']}");
		$data2 = array(
			'weid'=>$_W['uniacid'],
			'merchant_id'=>$merchant['id'],
			'money'=>abs($tixian['money']),
			'time'=>TIMESTAMP,
			'explain'=>'提现失败返还',
		);
		pdo_insert(BEST_MERCHANTACCOUNT,$data2);
	}
	message('拒绝发放成功！', $this->createWebUrl('tixian', array('op' => 'display')), 'success');
}
include $this->template('web/tixian');
?>