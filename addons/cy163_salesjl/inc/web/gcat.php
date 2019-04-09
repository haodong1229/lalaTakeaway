<?php
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
	if (!empty($_GPC['displayorder'])) {
		foreach ($_GPC['displayorder'] as $id => $displayorder) {
			pdo_update(BEST_GCAT, array('displayorder' => $displayorder), array('id' => $id, 'weid' => $_W['uniacid']));
		}
		message('商品分类排序更新成功！', $this->createWebUrl('gcat', array('op' => 'display')), 'success');
	}
	$list = pdo_fetchall("SELECT * FROM ".tablename(BEST_GCAT)." WHERE weid = {$_W['uniacid']} ORDER BY displayorder ASC");
} elseif ($operation == 'post') {
	$id = intval($_GPC['id']);
	if (checksubmit('submit')) {
		$catename = $_GPC['catename'];
		if(empty($catename)){
			message("请填写分类名称！");
		}
		$data = array(
			'weid' => $_W['uniacid'],
			'catename' => $catename,
			'enabled' => intval($_GPC['enabled']),
			'displayorder' => intval($_GPC['displayorder'])
		);
		if (!empty($id)) {
			pdo_update(BEST_GCAT, $data, array('id' => $id));
		} else {
			pdo_insert(BEST_GCAT, $data);
		}
		message('操作成功！', $this->createWebUrl('gcat', array('op' => 'display')), 'success');
	}
	$gcat = pdo_fetch("select * from ".tablename(BEST_GCAT)." where id = {$id} and weid= {$_W['uniacid']}");
} elseif ($operation == 'delete') {
	$id = intval($_GPC['id']);
	$gcat = pdo_fetch("SELECT id FROM ".tablename(BEST_GCAT)." WHERE id = {$id} AND weid = {$_W['uniacid']}");
	if (empty($gcat)) {
		message('抱歉，商品分类不存在或是已经被删除！', $this->createWebUrl('gcat', array('op' => 'display')), 'error');
	}
	pdo_delete(BEST_GCAT, array('id' => $id));
	message('商品分类删除成功！', $this->createWebUrl('gcat', array('op' => 'display')), 'success');
}else {
	message('请求方式不存在');
}
include $this->template('web/gcat');
?>