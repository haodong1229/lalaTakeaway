<?php
global $_W,$_GPC;
$operation = empty($_GPC['op']) ? 'display' : $_GPC['op'];
if($operation == 'display'){
	$condition = "weid = {$_W['uniacid']}";
	if (!empty($_GPC['keyword'])) {
		$condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_MEMBERHUODONG)." WHERE ".$condition);
	$list = pdo_fetchall("SELECT * FROM ".tablename(BEST_MEMBERHUODONG)." WHERE ".$condition." ORDER BY time DESC LIMIT ".($pindex - 1)*$psize.",".$psize);
	foreach($list as $k=>$v){
		$list[$k]['member'] = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$v['openid']}'");
		$list[$k]['goodslist'] = pdo_fetchall("SELECT * FROM ".tablename(BEST_MEMBERGOODS)." WHERE hdid = {$v['id']}");
		$list[$k]['ordernum'] = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_ORDER)." WHERE jlid = {$v['id']}");
	}
	$pager = pagination($total, $pindex, $psize);
}elseif ($operation == 'zanting') {
	$id = intval($_GPC['id']);
	$jielong = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERHUODONG)." WHERE id = {$id}");
	if (empty($jielong)) {
		message('抱歉，接龙不存在或是已经删除！', '', 'error');
	}
	$data['admindel'] = 1;
	pdo_update(BEST_MEMBERHUODONG,$data,array('id'=>$id));
	message('暂停接龙成功！', $this->createWebUrl('jielong', array('op' => 'display', 'id' => $id)), 'success');
}elseif ($operation == 'shenhe') {
	$id = intval($_GPC['id']);
	$jielong = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERHUODONG)." WHERE id = {$id}");
	if (empty($jielong)) {
		message('抱歉，接龙不存在或是已经删除！', '', 'error');
	}
	$data['status'] = 1;
	pdo_update(BEST_MEMBERHUODONG,$data,array('id'=>$id));
	message('审核接龙成功！', $this->createWebUrl('jielong', array('op' => 'display', 'id' => $id)), 'success');
}elseif ($operation == 'delete') {
	$id = intval($_GPC['id']);
	$jielong = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERHUODONG)." WHERE id = {$id}");
	if (empty($jielong)) {
		message('抱歉，接龙不存在或是已经删除！', '', 'error');
	}
	$ordernum = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_ORDER)." WHERE jlid = {$id}");
	if ($ordernum > 0) {
		message('抱歉，该接龙已产生订单不能删除！', '', 'error');
	}
	pdo_delete(BEST_MEMBERHUODONG,array('id'=>$id));
	pdo_delete(BEST_MEMBERGOODS,array('hdid'=>$id));
	message('删除接龙成功！', $this->createWebUrl('jielong', array('op' => 'display', 'id' => $id)), 'success');
}elseif ($operation == 'upshare') {
	$id = intval($_GPC['id']);
	$jielong = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERHUODONG)." WHERE id = {$id}");
	if (empty($jielong)) {
		message('抱歉，接龙不存在或是已经删除！', '', 'error');
	}
	$data['sharethumb'] = $_GPC['sharethumb'];
	$data['basicsales'] = intval($_GPC['basicsales']);
	$data['basicviews'] = intval($_GPC['basicviews']);
	pdo_update(BEST_MEMBERHUODONG,$data,array('id'=>$id));
	message('配置分享图成功！', $this->createWebUrl('jielong', array('op' => 'display', 'id' => $id)), 'success');
}
include $this->template('web/jielong');
?>