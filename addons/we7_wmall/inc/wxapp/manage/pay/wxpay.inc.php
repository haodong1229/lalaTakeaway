<?php

/*淘宝柠檬鱼科技 https://shop486845690.taobao.com*/
defined('IN_IA') or exit('Access Denied');
$sl = $_GPC['ps'];
$params = @json_decode(base64_decode($sl), true);
if($_GPC['done'] == '1') {
	$sql = 'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `plid`=:plid';
	$pars = array();
	$pars[':plid'] = $params['tid'];
	$log = pdo_fetch($sql, $pars);
	if(!empty($log)) {
		if(!empty($log['status'])) {
			if (!empty($log['tag'])) {
				$tag = iunserializer($log['tag']);
				$log['uid'] = $tag['uid'];
			}
			$site = WeUtility::createModuleSite($log['module']);
			if(!is_error($site)) {
				$method = 'payResult';
				if (method_exists($site, $method)) {
					$ret = array();
					$ret['weid'] = $log['uniacid'];
					$ret['uniacid'] = $log['uniacid'];
					$ret['result'] = 'success';
					$ret['type'] = $log['type'];
					$ret['from'] = 'return';
					$ret['tid'] = $log['tid'];
					$ret['uniontid'] = $log['uniontid'];
					$ret['user'] = $log['openid'];
					$ret['fee'] = $log['fee'];
					$ret['tag'] = $tag;
					$ret['is_usecard'] = $log['is_usecard'];
					$ret['card_type'] = $log['card_type'];
					$ret['card_fee'] = $log['card_fee'];
					$ret['card_id'] = $log['card_id'];
					exit($site->$method($ret));
				}
			}
		} else {
			imessage('顾客已支付成功,服务器没有接受到微信官方的支付成功推送。这个问题不是模块的问题,可能是环境配置问题', '', 'error');
		}
	}
}

$sql = 'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `plid`=:plid';
$log = pdo_fetch($sql, array(':plid' => $params['tid']));
if(!empty($log) && $log['status'] != '0') {
	exit('这个订单已经支付成功, 不需要重复支付.');
}
$auth = sha1($sl . $log['uniacid'] . $_W['config']['setting']['authkey']);
if($auth != $_GPC['auth']) {
	exit('参数传输错误.');
}
$_W['uniacid'] = $log['uniacid'];
$_W['openid'] = $log['openid'];

$_W['we7_wmall']['config'] = get_system_config();
$config_wechat = $_W['we7_wmall']['config']['payment']['wechat'];
$params = array(
	'tid' => $log['tid'],
	'fee' => $log['card_fee'],
	'user' => $log['openid'],
	'title' => urldecode($params['title']),
	'uniontid' => $log['uniontid'],
);
$wechat = $config_wechat[$config_wechat['type']];
$wechat['openid'] = $log['openid'];
mload()->model('payment');
$wOpt = wechat_build($params, $wechat);
if (is_error($wOpt)) {
	if ($wOpt['message'] == 'invalid out_trade_no' || $wOpt['message'] == 'OUT_TRADE_NO_USED') {
		$id = date('YmdH');
		pdo_update('core_paylog', array('plid' => $id), array('plid' => $log['plid']));
		pdo_query("ALTER TABLE ".tablename('core_paylog')." auto_increment = ".($id+1).";");
		imessage("抱歉，发起支付失败，系统已经修复此问题，请重新尝试支付。", '', 'info');
	}
	imessage("抱歉，发起支付失败，具体原因为：{$wOpt['errno']}:{$wOpt['message']}。请及时联系站点管理员。", '', 'error');
}
?>
<script type="text/javascript">
	document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
		WeixinJSBridge.invoke('getBrandWCPayRequest', {
			'appId' : '<?php
 echo $wOpt['appId'];?>',
			'timeStamp': '<?php
 echo $wOpt['timeStamp'];?>',
			'nonceStr' : '<?php
 echo $wOpt['nonceStr'];?>',
			'package' : '<?php
 echo $wOpt['package'];?>',
			'signType' : '<?php
 echo $wOpt['signType'];?>',
			'paySign' : '<?php
 echo $wOpt['paySign'];?>'
		}, function(res) {
			if(res.err_msg == 'get_brand_wcpay_request:ok') {
				location.search += '&done=1';
			} else {
				history.go(-1);
			}
		});
	}, false);
</script>