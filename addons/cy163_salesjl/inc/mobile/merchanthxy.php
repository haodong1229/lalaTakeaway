<?php
global $_GPC, $_W;
$merchant = $this->checkmergentauth();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
	$list = pdo_fetchall("SELECT * FROM ".tablename(BEST_HEXIAOYUAN)." WHERE weid = {$_W['uniacid']} AND merchant_id = {$merchant['id']}");
	include $this->template('merchanthxy');
}elseif ($operation == 'post') {
	$id = intval($_GPC['id']);
	if (!empty($id)) {
		$item = pdo_fetch("SELECT * FROM " . tablename(BEST_HEXIAOYUAN) . " WHERE merchant_id = {$merchant['id']} AND id = {$id}");
		if (empty($item)) {
			message('抱歉，分销员不存在或是已经删除！', '', 'error');
		}
	}
	if ($_GPC['submit']) {
		if (empty($_GPC['name'])) {
			$resArr['error'] = 1;
			$resArr['msg'] = '请输入核销员名称！';
			echo json_encode($resArr);
			exit();
		}
		if (empty($_GPC['openid'])) {
			$resArr['error'] = 1;
			$resArr['msg'] = '请输入核销员openid！';
			echo json_encode($resArr);
			exit();
		}
		$data = array(			
			'weid' => intval($_W['uniacid']),
			'name' => $_GPC['name'],
			'openid' => $_GPC['openid'],
			'merchant_id'=>$merchant['id'],
		);
		if (empty($id)) {
			pdo_insert(BEST_HEXIAOYUAN, $data);
		} else {
			pdo_update(BEST_HEXIAOYUAN, $data, array('id' => $id));
		}	
		$resArr['error'] = 0;
		$resArr['msg'] = '操作成功！';
		echo json_encode($resArr);
		exit();
	}
	include $this->template('merchanthxy_edit');
}elseif ($operation == 'delete') {
	$id = intval($_GPC['id']);
	$row = pdo_fetch("SELECT id FROM " . tablename(BEST_HEXIAOYUAN) . " WHERE merchant_id = {$merchant['id']} AND id = {$id}");
	if (empty($row)) {
		$resArr['error'] = 1;
		$resArr['msg'] = '抱歉，分销员不存在或是已经被删除！';
		echo json_encode($resArr);
		exit();
	}
	pdo_delete(BEST_HEXIAOYUAN, array('id' => $id));
	$resArr['error'] = 0;
	$resArr['msg'] = '删除成功！';
	echo json_encode($resArr);
	exit();
}
?>