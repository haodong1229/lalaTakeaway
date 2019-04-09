<?php
/*淘宝柠檬鱼科技 https://shop486845690.taobao.com*/
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';
if($ta == 'index') {
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		ijson(error(-1, '订单不存在或已删除'));
	}
	$order['goods'] = order_fetch_goods($order['id']);
	$deliveryer = pdo_get('tiny_wmall_deliveryer', array('uniacid' => $_W['uniacid'], 'id' => $order['deliveryer_id']));
	$store = store_fetch($order['sid'], array('id', 'title', 'logo'));
	$result = array(
		'store' => $store,
		'order' => $order,
		'deliveryer' => $deliveryer,
	);
	ijson(error(0, $result));
}

elseif($ta == 'post') {
	$id = intval($_GPC['id']);
	$order = order_fetch($id);
	if(empty($order)) {
		ijson(error(-1, '订单不存在或已删除'));
	}
	if($order['is_comment'] == 1) {
		ijson(error(-1, '订单已评价'));
	}
	$store = store_fetch($order['sid'], array('comment_status'));
	$insert = array(
		'uniacid' => $_W['uniacid'],
		'agentid' => $order['agentid'],
		'uid' => $order['emp_no'],
		'username' => $order['username'],
		'avatar' => '',
		'mobile' => $order['mobile'],
		'oid' => $id,
		'sid' => $order['sid'],
		'deliveryer_id' => $order['deliveryer_id'],
		'goods_quality' => intval($_GPC['goods_quality']) ? intval($_GPC['goods_quality']) : 5,
		'delivery_service' => intval($_GPC['delivery_service']) ? intval($_GPC['delivery_service']) : 5,
		'note' => trim($_GPC['note']),
		'status' => $store['comment_status'],
		'data' => '',
		'addtime' => TIMESTAMP,
	);
	if(!empty($_GPC['thumbs'])) {
		$thumbs = array();
		foreach($_GPC['thumbs'] as $thumb) {
			if(empty($thumb)) continue;
			$thumbs[] = $thumb;
		}
		$insert['thumbs'] = iserializer($thumbs);
	}
	$goods = order_fetch_goods($order['id']);
	foreach($goods as $good) {
		$value = intval($_GPC['goods'][$good['id']]);
		if(!$value) {
			continue;
		}
		$update = ' set comment_total = comment_total + 1';
		if($value == 1) {
			$update .= ' , comment_good = comment_good + 1';
			$insert['data']['good'][] = $good['goods_title'];
		} else {
			$insert['data']['bad'][] = $good['goods_title'];
		}
		pdo_query('update ' . tablename('tiny_wmall_goods') . $update . ' where id = :id', array(':id' => $good['goods_id']));
	}
	$insert['score'] = $insert['goods_quality'] + $insert['delivery_service'];
	$insert['data'] = iserializer($insert['data']);
	pdo_insert('tiny_wmall_order_comment', $insert);
	pdo_update('tiny_wmall_order', array('is_comment' => 1), array('id' => $id));
	if($store['comment_status'] == 1) {
		store_comment_stat($order['sid']);
	}
	ijson(error(0, '评价成功'));
}


