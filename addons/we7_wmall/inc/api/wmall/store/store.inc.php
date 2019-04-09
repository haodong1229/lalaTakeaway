<?php
/*淘宝柠檬鱼科技 https://shop486845690.taobao.com*/
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'category';
$_W['agentid'] = 0;
$_GPC['lat'] = 37.791069;
$_GPC['lng'] = 112.609959;
mload()->model('page');
mload()->model('store');

if($ta == 'category') {
	$category_id = intval($_GPC['category_id']) ? intval($_GPC['category_id']) : 1;
	$stores = store_filter(array('cid' => $category_id));
	ijson(error(0, $stores['stores']));
	return;
}

if($ta == 'ids') {
	$ids = trim($_GPC['ids']) ? trim($_GPC['ids']) : '3,31,41';
	$stores = store_filter(array('ids' => $ids));
	ijson(error(0, $stores['stores']));
	return;
}


