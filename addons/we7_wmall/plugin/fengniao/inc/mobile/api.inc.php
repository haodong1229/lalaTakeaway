<?php
/*淘宝柠檬鱼科技 https://shop486845690.taobao.com*/
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
pload()->classs('fengniao');
$fengniao = new Fengniao();

//$data  = $fengniao->getAccessToken();
//$data  = $fengniao->chainStore(1);
$data  = $fengniao->orderPush(421);

p($data);

