<?php
/*淘宝柠檬鱼科技 https://shop486845690.taobao.com*/
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$condition = ' WHERE uniacid = :uniacid and order_type < 3';
$params = array(
	':uniacid' => $_W['uniacid'],
);
$keyword = trim($_GPC['keyword']);
if(!empty($keyword)) {
	$condition .= " AND emp_no LIKE '%{$keyword}%'";
}
if(!empty($_GPC['addtime'])) {
	$condition .= " AND addtime > :start AND addtime < :end";
	$params[':start'] = strtotime($_GPC['starttime']);;
	$params[':end'] = strtotime($_GPC['endtime']) + 86399;;
}

$pindex = max(1, intval($_GPC['page']));
$psize = 20;
$total = pdo_fetchcolumn('select count(*)  from ' . tablename('tiny_wmall_order') . $condition, $params);
$orders = pdo_fetchall('select * from ' . tablename('tiny_wmall_order') . $condition . ' order by id desc limit '. ($pindex - 1) * $psize.','.$psize, $params, 'id');
if(!empty($orders)) {
	$stores = pdo_getall('tiny_wmall_store', array('uniacid' => $_W['uniacid']), array('id', 'title', 'telephone', 'logo'), 'id');
	foreach($stores as &$row) {
		$row['logo'] = tomedia($row['logo']);
	}
	$order_status = order_status();

	$order_ids = implode(',', array_keys($orders));
	$goods_temp = pdo_fetchall('select * from ' . tablename('tiny_wmall_order_stat') . " where uniacid = :uniacid and oid in ({$order_ids})", array(':uniacid' => $_W['uniacid']));
	$goods_all = array();
	foreach($goods_temp as $row) {
		$goods_all[$row['oid']][] =  $row;
	}
	$refund_status = order_refund_status();
	foreach($orders as &$da) {
		$da['refund_status_cn'] = $refund_status[$da['refund_status']]['text'];
		$da['store'] = $stores[$da['sid']];
		$da['status_cn'] = $order_status[$da['status']]['text'];
		$da['goods'] = $goods_all[$da['id']];
	}
}

$result = array(
	'total' => $total,
	'orders' => $orders,
);
ijson(error(0, $result));

