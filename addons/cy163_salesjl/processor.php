<?php
defined('IN_IA') or exit('Access Denied');
define('BEST_GOODS', 'cy163salesjl_goods');
define('BEST_GOODSOPTION', 'cy163salesjl_goods_option');
define('BEST_ORDER', 'cy163salesjl_order');
define('BEST_ORDERGOODS', 'cy163salesjl_order_goods');
define('BEST_MERCHANT', 'cy163salesjl_merchant');
define('BEST_MERCHANTACCOUNT', 'cy163salesjl_merchantaccount');
define('BEST_MEMBER', 'cy163salesjl_member');
define('BEST_TIXIAN', 'cy163salesjl_tixian');
define('BEST_HUODONG', 'cy163salesjl_huodong');
define('BEST_HUODONGGOODS', 'cy163salesjl_huodonggoods');
define('BEST_MERCHANTHD', 'cy163salesjl_merchanthd');
define('BEST_MERCHANTHDGOODS', 'cy163salesjl_merchanthdgoods');
define('BEST_YUNFEI', 'cy163salesjl_yunfei');
define('BEST_YUNFEISHENG', 'cy163salesjl_yfsheng');
define('BEST_MEMBERGOODS', 'cy163salesjl_membergoods');
define('BEST_MEMBERGOODSJIETI', 'cy163salesjl_membergoods_jieti');
define('BEST_MEMBERHUODONG', 'cy163salesjl_memberhuodong');
define('BEST_CART', 'cy163salesjl_cart');
define('BEST_MEMBERACCOUNT', 'cy163salesjl_memberaccount');
define('BEST_CITY', 'cy163salesjl_city');
define('BEST_ZITIDIAN', 'cy163salesjl_zitidian');
define('BEST_REFUND', 'cy163salesjl_refund');
define('BEST_MEMBERRZ', 'cy163salesjl_memberrz');
define('BEST_HEXIAOYUAN', 'cy163salesjl_hexiaoyuan');
define('BEST_HUODONGTEAMJIANG', 'cy163salesjl_huodong_teamjiang');
define('BEST_TZORDER', 'cy163salesjl_tzorder');

class Cy163_salesjlModuleProcessor extends WeModuleProcessor {
	
	public function respond() {
		global $_W;
		$openid = $this->message['from'];
		if($this->message['content'] == "接龙订单"){			
			$merchant = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANT)." WHERE openid = '{$openid}'");
			$merchanthd = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANTHD)." WHERE merchant_id = {$merchant['id']} ORDER BY time DESC LIMIT 1");
			$hdorder = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE mhdid = {$merchanthd['id']} AND status >= 0 AND isjl = 0");
			if(empty($hdorder)){
				$sendcon = $this->module['config']['dodatahf'];
			}else{
				$sendcon = '';
				
				/*
				$now = 0;
				foreach($hdorder as $k=>$v){
					$omember = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$v['from_user']}'");
					$ordergoods = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$v['id']}");
					foreach($ordergoods as $kk=>$vv){
						$now++;
						$sendcon .= $now."、".$omember['nickname']."  ".$vv['goodsname']."  +".$vv['total']."\n";
					}
				}*/
				foreach($hdorder as $k=>$v){
					$omember = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$v['from_user']}'");
					$ordergoods = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$v['id']}");
					
					$goodssend = '';
					foreach($ordergoods as $kk=>$vv){
						$goodssend .= $vv['goodsname']."  +".$vv['total']."、 ";
					}
					$sendcon .= ($k+1)."、".$omember['nickname']."  ".$goodssend."\n";
				}
			}			
			return $this->respText($sendcon);
		}

	}
}