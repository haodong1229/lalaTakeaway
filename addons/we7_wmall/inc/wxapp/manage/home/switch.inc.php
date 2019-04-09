<?php

/*淘宝柠檬鱼科技 https://shop486845690.taobao.com*/
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$sid = intval($_GPC['sid']);
isetcookie('__mg_sid', $sid, 86400 * 7);
header('location: ' . imurl('manage/shop/index', array('sid' => $sid)));
exit();