<?php
/*淘宝柠檬鱼科技 https://shop486845690.taobao.com*/
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$sid = intval($_GPC['id']) ? intval($_GPC['id']) : 3;
$store = store_fetch($sid, array('id', 'title', 'logo', 'address'));
if(empty($store) || is_error($store)) {
	ijson(error(-1, '门店不存在或已删除'));
}

$stat = store_comment_stat($sid);
$stat['all'] = intval(pdo_fetchcolumn('select count(*) as num from ' . tablename('tiny_wmall_order_comment') . ' where uniacid = :uniacid and sid = :sid and status = 1', array(':uniacid' => $_W['uniacid'], ':sid' => $sid)));
$stat['good'] = intval(pdo_fetchcolumn('select count(*) as num from ' . tablename('tiny_wmall_order_comment') . ' where uniacid = :uniacid and sid = :sid and status = 1 and score >= 8', array(':uniacid' => $_W['uniacid'], ':sid' => $sid)));
$stat['middle'] = intval(pdo_fetchcolumn('select count(*) as num from ' . tablename('tiny_wmall_order_comment') . ' where uniacid = :uniacid and sid = :sid and status = 1 and score >= 4 and score <= 7', array(':uniacid' => $_W['uniacid'], ':sid' => $sid)));
$stat['bad'] = intval(pdo_fetchcolumn('select count(*) as num from ' . tablename('tiny_wmall_order_comment') . ' where uniacid = :uniacid and sid = :sid and status = 1 and score <= 3', array(':uniacid' => $_W['uniacid'], ':sid' => $sid)));

$condition = ' where a.uniacid = :uniacid and a.sid = :sid and a.status = 1';
$params = array(':uniacid' => $_W['uniacid'], ':sid' => $sid);
$type = intval($_GPC['type']);
if($type == 1) {
	$condition .= ' and a.score >= 8';
} elseif($type == 2) {
	$condition .= ' and a.score >= 4 and a.score <= 7';
} elseif($type == 3) {
	$condition .= ' and a.score <= 3';
}
$note = intval($_GPC['note']);
if($note > 0) {
	$condition .= " and a.note != ''";
}

$pindex = max(1, intval($_GPC['page']));
$psize = 20;
$comments = pdo_fetchall('select a.id as aid, a.*, b.title from ' . tablename('tiny_wmall_order_comment') . ' as a left join ' . tablename('tiny_wmall_store') . " as b on a.sid = b.id {$condition} order by a.id desc limit " .($pindex - 1) * $psize.','.$psize, $params);
if(!empty($comments)) {
	foreach ($comments as &$row) {
		$row['data'] = iunserializer($row['data']);
		$row['score'] = ($row['delivery_service'] + $row['goods_quality']) * 10;
		$row['mobile'] = str_replace(substr($row['mobile'], 4, 4), '****', $row['mobile']);
		$row['addtime'] = date('Y-m-d H:i', $row['addtime']);
		$row['replytime'] = date('Y-m-d H:i', $row['replytime']);
		$row['avatar'] = tomedia($row['avatar']) ? tomedia($row['avatar']) : WE7_WMALL_TPL_URL . 'static/img/head.png';
		$row['thumbs'] = iunserializer($row['thumbs']);
		$row['scores'] = score_format($row['score'] / 20);
		if(!empty($row['thumbs'])) {
			foreach($row['thumbs'] as &$item) {
				$item = tomedia($item);
			}
		}
	}
}
$result = array(
	'store' => $store,
	'stat' => $stat,
	'comments' => array_values($comments),
);
ijson(error(0, $result));