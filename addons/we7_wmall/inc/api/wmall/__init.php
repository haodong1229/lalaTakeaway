<?php
/*淘宝柠檬鱼科技 https://shop486845690.taobao.com*/
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
if(!empty($_GPC['__input'])) {
	foreach($_GPC['__input'] as $key => $item) {
		$_GPC[$key] = $item;
	}
	unset($_GPC['__input']);
}
mload()->model('common');
mload()->func('app');
mload()->model('member');
mload()->model('store');
mload()->model('order');
$_W['we7_wmall']['global'] = get_global_config();
if($_W['we7_wmall']['global']['development'] == 1) {
	ini_set('display_errors', '1');
	error_reporting(E_ALL ^ E_NOTICE);
}
$_W['we7_wmall']['config'] = get_system_config();
$_config_mall = $_W['we7_wmall']['config']['mall'];
if(empty($_config_mall['delivery_title'])) {
	$_config_mall['delivery_title'] = '平台专送';
}
$_W['role'] = 'consumer';
$_W['role_cn'] = '下单顾客';

function wcheckauth() {
	global $_W, $_GPC;
	$emp_no = intval($_GPC['emp_no']);
	if(empty($emp_no)) {
		ijson(error(-1, 'emp_no不能为空'), '', 'ajax');
	}
	$_W['member'] = array(
		'uid' => $emp_no,
	);
	return true;
}


