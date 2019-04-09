<?php
defined("IN_IA") || exit("Access Denied");
global $_W;
global $_GPC;
mload()->model("deliveryer");

if ($store['delivery_mode'] != 1) {
	imessage('当前门店的配送模式为平台配送员, 您无法进行该操作', referer(), 'error');
}


$ta = ((trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list'));

if ($ta == 'list') {
	$_W['page']['title'] = '配送员列表';
	$deliveryers = deliveryer_fetchall($sid, array('work_status' => -1, 'agentid' => -1));

	if (!(empty($deliveryers))) {
		foreach ($deliveryers as &$row ) {
			$da['stat'] = deliveryer_order_stat($sid, $row['deliveryer_id']);
		}
	}

}


if ($ta == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete("tiny_wmall_store_deliveryer", array("uniacid" => $_W['uniacid'], 'sid' => $sid, 'deliveryer_id' => $id));
	mlog(4002, $id, '商户删除店铺配送员');
	imessage(error(0, "删除配送员配送权限成功"), referer(), "ajax");
}


if ($ta == 'stat') {
	$_W['page']['title'] = '配送统计';
	$id = intval($_GPC['id']);
	$deliveryer = deliveryer_fetch($id);

	if (empty($deliveryer)) {
		imessage('配送员不存在', referer(), 'error');
	}


	$start = (($_GPC['start'] ? strtotime($_GPC['start']) : strtotime(date('Y-m'))));
	$end = (($_GPC['end'] ? strtotime($_GPC['end']) + 86399 : strtotime(date('Y-m-d')) + 86399));
	$day_num = ($end - $start) / 86400;
	if ($_W['isajax'] && $_W['ispost']) {
		$days = array();
		$datasets = array(
			'flow1' => array()
			);
		$i = 0;

		while ($i < $day_num) {
			$key = date('m-d', $start + (86400 * $i));
			$days[$key] = 0;
			$datasets['flow1'][$key] = 0;
			++$i;
		}

		$data = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_order') . 'WHERE uniacid = :uniacid AND sid = :sid AND deliveryer_id = :deliveryer_id and status = 5', array(':uniacid' => $_W['uniacid'], ':sid' => $sid, ':deliveryer_id' => $id));

		foreach ($data as $da ) {
			$key = date('m-d', $da['addtime']);

			if (in_array($key, array_keys($days))) {
				++$datasets['flow1'][$key];
			}

		}

		$shuju['label'] = array_keys($days);
		$shuju['datasets'] = $datasets;
		exit(json_encode($shuju));
	}


	$stat = deliveryer_order_stat($sid, $id);
}


if ($ta == 'add') {
	if ($_W['isajax']) {
		$mobile = trim($_GPC['mobile']);

		if (empty($mobile)) {
			imessage(error(-1, '手机号不能为空'), '', 'ajax');
		}


		$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'mobile' => $mobile));

		if (empty($deliveryer)) {
			imessage(error(-1, '未找到该手机号对应的配送员'), '', 'ajax');
		}


		if ($deliveryer['status'] != 1) {
			imessage(error(-1, '该手机号对应的配送员已被删除至回收站'), '', 'ajax');
		}


		$is_exist = pdo_get('tiny_wmall_store_deliveryer', array('uniacid' => $_W['uniacid'], 'deliveryer_id' => $deliveryer['id'], 'sid' => $sid));

		if (!(empty($is_exist))) {
			imessage(error(-1, '该手机号对用的配送员已经是店内配送员, 请勿重复添加'), '', 'ajax');
		}


		$data = array('uniacid' => $_W['uniacid'], 'agentid' => $store['agentid'], 'sid' => $sid, 'deliveryer_id' => $deliveryer['id'], 'addtime' => TIMESTAMP);
		pdo_insert("tiny_wmall_store_deliveryer", $data);
		mlog(4000, $deliveryer['id'], '商户添加店内配送员');
		imessage(error(0, "添加店内配送员成功"), '', "ajax");
	}

}


if ($ta == 'cover') {
	$_W['page']['title'] = '配送员入口';
	$urls = array('register' => imurl('delivery/auth/register', array(), true), 'login' => imurl('delivery/auth/login', array(), true));
}


include itemplate("store/shop/deliveryer");

?>