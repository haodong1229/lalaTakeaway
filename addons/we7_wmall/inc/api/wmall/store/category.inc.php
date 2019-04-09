<?php
/*淘宝柠檬鱼科技 https://shop486845690.taobao.com*/
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
mload()->model('store');
$_W['agentid'] = 0;
$categorys = store_fetchall_category();
ijson(error(0, $categorys));