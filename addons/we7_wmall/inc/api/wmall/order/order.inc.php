<?php
/*淘宝柠檬鱼科技 https://shop486845690.taobao.com*/
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('iorder');
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'detail';

if($ta == 'detail') {
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		ijson(error(-1, '订单不存在或已经删除'));
	}
	$order['store'] = store_fetch($order['sid'], array('id', 'title', 'telephone', 'pack_price', 'logo', 'delivery_price', 'address', 'location_x', 'location_y'));
	$order['goods'] = order_fetch_goods($order['id']);
	$order['activityed'] = order_fetch_discount($id);
	$order['logs'] = order_fetch_status_log($id);
	if(!empty($order['logs'])) {
		foreach($order['logs'] as &$log) {
			$log['addtime'] = date('H:i', $log['addtime']);
		}
	}
	ijson(error(0, $order));
}

elseif($ta == 'refund') {
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		ijson(error(-1, '订单不存在或已经删除'));
	}
	$result = iorder_begin_huawei_payrefund($order['id']);
	ijson($result);
}

function iorder_begin_huawei_payrefund($order_id) {
	global $_W;
	$refund = pdo_get('tiny_wmall_order_refund', array('uniacid' => $_W['uniacid'], 'order_id' => $order_id));
	if(empty($refund)) {
		return error(-1, '退款申请不存在或已删除');
	}
	if($refund['status'] == 2) {
		return error(-1, '退款进行中, 不能发起退款');
	}
	if($refund['status'] == 3) {
		return error(-1, '退款已成功, 不能发起退款');
	}
	if($refund['pay_type'] == 'huawei') {
		$refund_update = array(
			'status' => 3,
			'account' => '原路返回',
			'channel' => 'ORIGINAL',
			'handle_time' => TIMESTAMP,
			'success_time' => TIMESTAMP,
		);
		pdo_update('tiny_wmall_order_refund', $refund_update, array('uniacid' => $_W['uniacid'], 'id' => $refund['id']));
		pdo_update('tiny_wmall_order', array('refund_status' => 3), array('uniacid' => $_W['uniacid'], 'id' => $refund['order_id']));
		order_insert_refund_log($refund['order_id'], 'handle');
		order_insert_refund_log($refund['order_id'], 'success');
		return error(0, '退款成功');
	}
	return error(-1, '退款失败');;
}



