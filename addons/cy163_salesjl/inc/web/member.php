<?php
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$setting = $this->setting;
if ($operation == 'display') {	
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$nickname = $_GPC['nickname'];
	$condition = " weid = {$_W['uniacid']} ";
	if ($nickname != '') {
		$condition .= " AND nickname like '%{$nickname}%' ";
	}
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_MEMBER)." WHERE ".$condition);
	$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
	$list = pdo_fetchall("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE ".$condition." ORDER BY regtime DESC ".$limit);
	foreach($list as $k=>$v){
		$list[$k]['moneylist'] = pdo_fetchall("SELECT * FROM ".tablename(BEST_MEMBERACCOUNT)." WHERE openid = '{$v['openid']}' ORDER BY time DESC");
	}
	$pager = pagination($total, $pindex, $psize);
} elseif ($operation == 'delete') {
	$id = intval($_GPC['id']);
	$member = pdo_fetch("SELECT id FROM ".tablename(BEST_MEMBER)." WHERE id = {$id} AND weid = {$_W['uniacid']}");
	if (empty($member)) {
		message('抱歉，该会员不存在或是已经被删除！', $this->createWebUrl('member', array('op' => 'display')), 'error');
	}
	pdo_delete(BEST_MEMBER, array('id' => $id));
	message('会员删除成功！', $this->createWebUrl('member', array('op' => 'display')), 'success');
}elseif($operation == 'deletedu') {
	$id = intval($_GPC['id']);
	$money = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERACCOUNT)." WHERE id = {$id}");
	if (empty($money)) {
		$resarr['error'] = 1;
		$resarr['msg'] = '不存在该记录！';
		echo json_encode($resarr);
		exit();
	}
	pdo_delete(BEST_MEMBERACCOUNT,array('id'=>$id));
	$resarr['error'] = 0;
	$resarr['msg'] = '删除成功！';
	echo json_encode($resarr);
	exit();
}elseif($operation == 'account') {
	$id = intval($_GPC['id']);
	$member = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE id = {$id} AND weid = {$_W['uniacid']}");
	$allmoney = pdo_fetchcolumn("SELECT SUM(money) FROM ".tablename(BEST_MEMBERACCOUNT)." WHERE openid = '{$member['openid']}'");
	$allmoney = empty($allmoney) ? 0 : $allmoney;
}elseif($operation == 'doaccount') {
	$money = $_GPC['money'];
	if(empty($money)){
		message('请填写金额！');
	}
	$data = array(
		'weid'=>$_W['uniacid'],
		'openid'=>$_GPC['openid'],
		'money'=>$money,
		'explain'=>$_GPC['explain'],
		'time'=>TIMESTAMP,
	);
	pdo_insert(BEST_MEMBERACCOUNT,$data);
	message('操作成功！', '', 'success');
}elseif($operation == 'show') {
	$list = pdo_fetchall("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} AND istz = 1 ORDER BY addtime DESC");
	echo "<table style='width:100%;'><thead><th style='text-align:left;'>团长</th><th style='text-align:left;'>团长昵称</th><th style='text-align:left;'>下级</th><th style='text-align:left;'>下级昵称</th></thead>";
	foreach($list as $k=>$v){
		$member = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE weid = {$_W['uniacid']} AND openid = '{$v['openid']}'");
		$list2 = pdo_fetchall("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} AND fopenid = '{$v['openid']}' AND  fopenid != '' ORDER BY addtime DESC");
		$xxjj = $xxjj2 = '';
		foreach($list2 as $kk=>$vv){
			$member2 = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE weid = {$_W['uniacid']} AND openid = '{$vv['openid']}'");
			$xxjj .= $vv['name'].'、';
			$xxjj2 .= $member2['nickname'].'、';
		}
		echo "<tr><td>".$v['name']."</td><td>".$member['nickname']."</td><td>".$xxjj."</td><td>".$xxjj2."</td></tr>";
	}
	echo "</table>";
	exit;
}else {
	message('请求方式不存在!');
}
include $this->template('web/member');
?>