<?php
/**
 * 社区社群团购模块微站定义
 *
 * @author 梅小西
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
define('STATIC_ROOT', '../addons/cy163_salesjl/static');
define('KDN_URL', 'http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx');

define('HB_ROOT_TD', '../addons/cy163_salesjl/haibao/tuandui/');
define('HB_ROOT_SHEQU', '../addons/cy163_salesjl/haibao/shequ/');
define('HB_ROOT_SHEQUINDEX', '../addons/cy163_salesjl/haibao/shequindex/');
define('HB_ROOT_ALLHX', '../addons/cy163_salesjl/haibao/allhx/');
//海报
define('HB_ROOT_ZFJL', '../addons/cy163_salesjl/haibao/zfjl/');


define('ROOT_PATH', IA_ROOT.'/addons/cy163_salesjl/');

define('BEST_DOMAINSF', 'http://we7.qiumipai.com/shengfen.php');
define('BEST_DOMAINCS', 'http://we7.qiumipai.com/chengshi.php');
define('BEST_DOMAINQX', 'http://we7.qiumipai.com/quxian.php');

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
define('BEST_FORMID', 'cy163salesjl_formid');
define('BEST_FFJIANG', 'cy163salesjl_ffjiang');
define('BEST_XIAOQU', 'cy163salesjl_xiaoqu');
define('BEST_GCAT', 'cy163salesjl_gcat');
define('BEST_MEMBERGOODSKU', 'cy163salesjl_membergoodsku');


function timediff($begin_time,$end_time){
      if($begin_time < $end_time){
         $starttime = $begin_time;
         $endtime = $end_time;
      }else{
         $starttime = $end_time;
         $endtime = $begin_time;
      }

      //计算天数
      $timediff = $endtime-$starttime;
      $days = intval($timediff/86400);
      //计算小时数
      $remain = $timediff%86400;
      $hours = intval($remain/3600);
      //计算分钟数
      $remain = $remain%3600;
      $mins = intval($remain/60);
      //计算秒数
      $secs = $remain%60;
      $res = array("day" => $days,"hour" => $hours,"min" => $mins,"sec" => $secs);
      return $res;
}

function returnError($message, $data = '', $status = 0, $type = '')
{
	global $_W;
	if ($_W['isajax'] || $type == 'ajax') {
		header('Content-Type:application/json; charset=utf-8');
		$ret = array('status' => $status, 'info' => $message, 'data' => $data);
		die(json_encode($ret));
	} else {
		return message($message, $data, 'error');
	}
}

function returnSuccess($message, $data = '', $status = 1, $type = '')
{
	global $_W;
	if ($_W['isajax'] || $type == 'ajax') {
		header('Content-Type:application/json; charset=utf-8');
		$ret = array('status' => $status, 'info' => $message, 'data' => $data);
		die(json_encode($ret));
	} else {
		return message($message, $data, 'success');
	}
}

class Cy163_salesjlModuleSite extends WeModuleSite {
	
	protected function transferByPay($transfer)
	{
		global $_W;
		$api = array('mchid' => $this->module['config']['mchid'], 'appid' => $this->module['config']['appid'], 'ip' => $this->module['config']['ip'], 'key' => $this->module['config']['key']);
		$ret = array();
		$amount = $transfer['amount'];
		$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
		$pars = array();
		$pars['mch_appid'] = $api['appid'];
		$pars['mchid'] = $api['mchid'];
		$pars['nonce_str'] = random(32);
		$pars['partner_trade_no'] = time() . random(4, true);
		$pars['openid'] = empty($transfer['openid']) ? $_W['openid'] : $transfer['openid'];
		$pars['check_name'] = "NO_CHECK";
		$pars['amount'] = $amount;
		$pars['desc'] = $transfer['desc'];
		$pars['spbill_create_ip'] = $api['ip'];
		ksort($pars, SORT_STRING);
		$string1 = '';
		foreach ($pars as $k => $v) {
			$string1 .= "{$k}={$v}&";
		}
		$string1 .= "key={$api['key']}";
		$pars['sign'] = strtoupper(md5($string1));
		$xml = array2xml($pars);
		$extras = array();
		$extras['CURLOPT_CAINFO'] = '../addons/cy163_salesjl/'.$_W['uniacid'].'/rootca.pem';
		$extras['CURLOPT_SSLCERT'] = '../addons/cy163_salesjl/'.$_W['uniacid'].'/apiclient_cert.pem';
		$extras['CURLOPT_SSLKEY'] = '../addons/cy163_salesjl/'.$_W['uniacid'].'/apiclient_key.pem';
		load()->func('communication');
		$procResult = null;
		$resp = ihttp_request($url, $xml, $extras);
		if (is_error($resp)) {
			return error(-1, $resp['message']);
		} else {
			$xml = '<?xml version="1.0" encoding="utf-8"?>' . $resp['content'];
			$dom = new DOMDocument();
			if ($dom->loadXML($xml)) {
				$xpath = new DOMXPath($dom);
				$code = $xpath->evaluate('string(//xml/return_code)');
				$result = $xpath->evaluate('string(//xml/result_code)');
				if (strtolower($code) == 'success' && strtolower($result) == 'success') {
					$partner_trade_no = $xpath->evaluate('string(//xml/partner_trade_no)');
					$payment_no = $xpath->evaluate('string(//xml/payment_no)');
					$payment_time = $xpath->evaluate('string(//xml/payment_time)');
					return array('partner_trade_no' => $partner_trade_no, 'payment_no' => $payment_no, 'payment_time' => $payment_time);
				} else {
					$error = $xpath->evaluate('string(//xml/err_code_des)');
					return error(-2, $error);
				}
			} else {
				return error(-3, 'error response');
			}
		}
	}
	
	protected function refundByPay($params){
		global $_W;
		$data = array(
			'appid' => $this->module['config']['appid'],
			'mch_id' => $this->module['config']['mchid'],
			'nonce_str' => random(32),
			'out_refund_no' => random(22, true),
			'refund_fee' => floatval($params['refund_fee']) * 100,
			'total_fee' => floatval($params['total_fee']) * 100,
			'transaction_id' => $params['transaction_id'],
			//'op_user_id' => $this->module['config']['mchid']
		);
		$xml_data = '<xml>';
		foreach ($data as $k => $v) {
			$xml_data .= "<{$k}>{$v}</{$k}>";
		}	
		ksort($data);
		$data_str = '';
		foreach ($data as $k => $v) {
			if ($v == '' || $k == 'sign') {
				continue;
			}
			$data_str .= "{$k}={$v}&";
		}
		$data_str .= "key=" . $this->module['config']['key'];
		$sign = strtoupper(md5($data_str));
		$xml_data .= "<sign>{$sign}</sign>";
		$xml_data .= '</xml>';
		$headers = array();
		$headers['Content-Type'] = 'application/x-www-form-urlencoded';
		$headers['CURLOPT_SSL_VERIFYPEER'] = false;
		$headers['CURLOPT_SSL_VERIFYHOST'] = false;
		$headers['CURLOPT_SSLCERTTYPE'] = 'PEM';
		$headers['CURLOPT_SSLCERT'] = '../addons/cy163_salesjl/'.$_W['uniacid'].'/apiclient_cert.pem';
		$headers['CURLOPT_SSLKEYTYPE'] = 'PEM';
		$headers['CURLOPT_SSLKEY'] = '../addons/cy163_salesjl/'.$_W['uniacid'].'/apiclient_key.pem';
		$headers['CURLOPT_CAINFO'] = '../addons/cy163_salesjl/'.$_W['uniacid'].'/rootca.pem';
		$response = ihttp_request("https://api.mch.weixin.qq.com/secapi/pay/refund", $xml_data, $headers);
		if ($response == '') {			
			return error(-1, $response['message']);
		}
		$response = $response['content'];

		$xml = @simplexml_load_string($response);
		if (empty($xml)) {
			return error(-2, '[wxpay-api:refund] parse xml NULL');
		}

		$return_code = $xml->return_code ? (string) $xml->return_code : '';
		$return_msg = $xml->return_msg ? (string) $xml->return_msg : '';
		$result_code = $xml->result_code ? (string) $xml->result_code : '';
		$err_code = $xml->err_code ? (string) $xml->err_code : '';
		$err_code_des = $xml->err_code_des ? (string) $xml->err_code_des : '';
		if ($return_code == 'SUCCESS' && $result_code == 'SUCCESS') {
			$ret = array('success' => true, 'refund_id' => (string) $xml->refund_id, 'out_refund_no' => (string) $xml->out_refund_no);
			return $ret;
		} else {
			return error(-3, $return_code . ':' . $return_msg . ',' . $err_code . ':' . $err_code_des);
		}
	}
	
	public function Mcheckmember(){
		global $_W,$_GPC;
		if(empty($_W['fans']['from_user'])){
			$message = '请在微信浏览器中打开！';
			include $this->template('error');
			exit;
		}
		$member = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$_W['fans']['from_user']}'");
		if(empty($member)){
			$account_api = WeAccount::create();
			$info = $account_api->fansQueryInfo($_W['fans']['from_user']);
			if($info['subscribe'] == 1){
				$avatar = $info['headimgurl'];
				$nickname = $info['nickname'];
				if($info['sex'] == 1){
					$gender = '男';
				}else{
					$gender = '女';
				}
			}else{
				$fan = mc_oauth_userinfo();
				$avatar = $fan['headimgurl'];
				$nickname = $fan['nickname'];
				if($fan['sex'] == 1){
					$gender = '男';
				}else{
					$gender = '女';
				}
			}
			$data['weid'] = $_W['uniacid'];
			$data['openid'] = $_W['fans']['from_user'];
			$data['nickname'] = $nickname;
			$data['avatar'] = $avatar;
			$data['gender'] = $gender;
			$data['regtime'] = TIMESTAMP;
			pdo_insert(BEST_MEMBER,$data);
			$member = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$_W['fans']['from_user']}'");
		}
		return $member;
	}
	
	public function doMobilePytask() {
		/*global $_W,$_GPC;
		$orderlist1 = pdo_fetchall("SELECT id,createtime FROM ".tablename(BEST_ORDER)." WHERE status = 0 AND isjl = 0");
		$cancelordertimes = ($this->module['config']['cancelorderdays'])*3600;
		foreach($orderlist1 as $k=>$v){
			$chatime1 = TIMESTAMP - $v['createtime'];
			if($chatime1 > $cancelordertimes){
				pdo_update(BEST_ORDER,array('status'=>-1),array('id'=>$v['id']));
			}
		}
		$orderlist2 = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE status = 2 AND (pstype = 0 || pstype = 3) AND isjl = 0 AND merchant_id > 0");
		$finishordertimesfahuo = ($this->module['config']['finishorderdays'])*3600;
		foreach($orderlist2 as $k=>$v){
			$chatime2 = TIMESTAMP - $v['createtime'];
			if($chatime2 > $finishordertimesfahuo){
				pdo_update(BEST_ORDER,array('status'=>4),array('id'=>$v['id']));
			}
			//利润写进代理商数据库，同时设置可提现时间
			$hasmerchantaccount = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANTACCOUNT)." WHERE merchant_id = {$v['merchant_id']} AND orderid = {$v['id']}");
			if(empty($hasmerchantaccount)){
				$datamerchant['weid'] = $_W['uniacid'];
				$datamerchant['merchant_id'] = $v['merchant_id'];
				$datamerchant['money'] = $v['alllirun'];
				$datamerchant['time'] = TIMESTAMP;
				$datamerchant['explain'] = '代理团结算';
				$datamerchant['orderid'] = $v['id'];
				$datamerchant['candotime'] = TIMESTAMP + ($this->module['config']['dltxhour'])*3600;
				pdo_insert(BEST_MERCHANTACCOUNT,$datamerchant);
			}
		}
		
		$orderlist4 = pdo_fetchall("SELECT id,createtime FROM ".tablename(BEST_ORDER)." WHERE status = 0 AND isjl = 1");
		$cancelordertimeszf = ($this->module['config']['cancelorderdayszf'])*3600;
		foreach($orderlist4 as $k=>$v){
			$chatime4 = TIMESTAMP - $v['createtime'];
			if($chatime4 > $cancelordertimeszf){
				pdo_update(BEST_ORDER,array('status'=>-1),array('id'=>$v['id']));
			}
		}
		
		$orderlist5 = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE status = 2 AND pstype = 0 AND isjl = 1 AND jlopenid != ''");
		$finishordertimesfahuozf = ($this->module['config']['finishorderdayszf'])*3600;
		foreach($orderlist5 as $k=>$v){
			$chatime5 = TIMESTAMP - $v['createtime'];
			if($chatime5 > $finishordertimesfahuozf){
				pdo_update(BEST_ORDER,array('status'=>4),array('id'=>$v['id']));
			}
			//利润写进代理商数据库，同时设置可提现时间
			$hasmemberaccount = pdo_fetch("SELECT id FROM ".tablename(BEST_MEMBERACCOUNT)." WHERE openid = {$v['jlopenid']} AND orderid = {$v['id']}");
			if(empty($hasmemberaccount)){
				$datamoney['weid'] = $_W['uniacid'];
				$datamoney['openid'] = $v['jlopenid'];
				$datamoney['money'] = $v['price'];
				$datamoney['time'] = TIMESTAMP;
				$datamoney['orderid'] = $v['id'];
				$datamoney['explain'] = "团购订单";
				$datamoney['candotime'] = TIMESTAMP + ($this->module['config']['zftxhour'])*3600;
				pdo_insert(BEST_MEMBERACCOUNT,$datamoney);
			}
		}
		*/
	}
	
	public function doMobileShequhb(){
		include_once ROOT_PATH.'inc/mobile/shequhb.php';
	}
	
	public function createImg($data,$codeImg){
		global $_W,$_GPC;
		include_once ROOT_PATH.'Haibao.class.php';
		$haibao_obj = new Haibao();
		if($this->module['config']['goodsthumb'] != ""){
			$backImg = tomedia($this->module['config']['goodsthumb']);
		}else{
			$backImg = STATIC_ROOT."/goodsbg.jpg";
		}
		$new =  $data['new_img'];
		$goods_img = $data['goods_img'];
		// 添加二维码
		$haibao_obj->addPic($backImg,$codeImg,200,200,480,1070,$new);
		// 添加产品
		$haibao_obj->addPic($new,$goods_img,630,630,60,345,$new);
		
		$ziti = STATIC_ROOT.'/msyh.ttf';
		// 添加产品描述，对描述进行分行
		$theTitle = $haibao_obj->cn_row_substr($data['title'],2,10);
		$haibao_obj->addWord($theTitle[1],60,1140,24,'black',$ziti,$new);
		$haibao_obj->addWord($theTitle[2],60,1180,24,'black',$ziti,$new);

		// 添加价格1
		$haibao_obj->addWord($data['price_market'],60,1080,34,'red',$ziti,$new);
		// 添加价格2
		$haibao_obj->addWord($data['price_member'],240,1080,22,'hui',$ziti,$new);

		
		$desTitle = $haibao_obj->cn_row_substr($data['shotdes'],2,12);
		$haibao_obj->addWord($desTitle[1],60,1220,20,'hui',$ziti,$new);
		$haibao_obj->addWord($desTitle[2],60,1250,20,'hui',$ziti,$new);
		return $new;
	}
	
	
	public function createindexImg($data,$codeImg){
		global $_W,$_GPC;
		include_once ROOT_PATH.'Haibao.class.php';
		$haibao_obj = new Haibao();
		if($this->module['config']['indexhbstyle'] == 3){
			$backImg = tomedia($this->module['config']['indexthumb']);
		}else{
			if($this->module['config']['indexhbstyle'] == 0){
				$backImg = STATIC_ROOT."/indexbg.png";
			}
			if($this->module['config']['indexhbstyle'] == 1){
				$backImg = STATIC_ROOT."/indexbg2.png";
			}
			if($this->module['config']['indexhbstyle'] == 2){
				$backImg = STATIC_ROOT."/indexbg3.png";
			}
		}
		$xqid = $data['xqid'];
		$xqmsg = $this->getxqmsg($xqid);
		$xiaoqu = $xqmsg['xiaoqu'];
		$merchant = $xqmsg['merchant'];
		$hdres = $xqmsg['hdres'];
		$merchanthd = $xqmsg['merchanthd'];
		$merchanthd['id'] = !isset($merchanthd) ? 0 : $merchanthd['id'];
		$goodslist = pdo_fetchall("SELECT mhdid,goodsid,optionid FROM ".tablename(BEST_MERCHANTHDGOODS)." WHERE mhdid = {$merchanthd['id']} GROUP BY goodsid");
		foreach($goodslist as $k=>$v){
			$hdgoods = pdo_fetch("SELECT displayorder FROM ".tablename(BEST_HUODONGGOODS)." WHERE weid = {$_W['uniacid']} AND hdid = {$hdres['id']} AND goodsid = {$v['goodsid']}");
			$goodslist[$k]['displayorder'] = $hdgoods['displayorder'];
		}
		if(!function_exists('array_column')){
			array_multisort($this->getNewArrByElement($goodslist,'displayorder'),SORT_DESC,$goodslist);
		}else{
			array_multisort(array_column($goodslist,'displayorder'),SORT_DESC,$goodslist);
		}
		
		$ziti = STATIC_ROOT.'/msyh.ttf';
		$new =  $data['new_img'];
		// 添加二维码
		$haibao_obj->addPic($backImg,$codeImg,200,200,487,1088,$new);
		
		//添加商品
		if(isset($goodslist[0])){
			$goods = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE weid = {$_W['uniacid']} AND id = {$goodslist[0]['goodsid']}");
			$goods['scprice'] = str_replace(".00","",$goods['scprice']);
			$goods['normalprice'] = str_replace(".00","",$goods['normalprice']);
			$thumb = tomedia($goods['thumb']);
			$haibao_obj->addPic($new,$thumb,140,140,50,280,$new);
			$goodsTitle = $haibao_obj->cn_row_substr($goods['title'],2,5);
			$haibao_obj->addWord($goodsTitle[1],200,300,20,'black',$ziti,$new);
			$haibao_obj->addWord($goodsTitle[2],200,330,20,'black',$ziti,$new);
			
			$haibao_obj->addWord('￥'.$goods['scprice'],200,370,20,'hui',$ziti,$new);
			$haibao_obj->addWord('￥'.$goods['normalprice'],200,410,24,'red',$ziti,$new);
		}
		
		if(isset($goodslist[1])){
			$goods = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE weid = {$_W['uniacid']} AND id = {$goodslist[1]['goodsid']}");
			$goods['scprice'] = str_replace(".00","",$goods['scprice']);
			$goods['normalprice'] = str_replace(".00","",$goods['normalprice']);
			$thumb = tomedia($goods['thumb']);
			$haibao_obj->addPic($new,$thumb,140,140,390,280,$new);
			$goodsTitle = $haibao_obj->cn_row_substr($goods['title'],2,5);
			$haibao_obj->addWord($goodsTitle[1],540,300,20,'black',$ziti,$new);
			$haibao_obj->addWord($goodsTitle[2],540,330,20,'black',$ziti,$new);
			
			$haibao_obj->addWord('￥'.$goods['scprice'],540,370,20,'hui',$ziti,$new);
			$haibao_obj->addWord('￥'.$goods['normalprice'],540,410,24,'red',$ziti,$new);
		}
		
		if(isset($goodslist[2])){
			$goods = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE weid = {$_W['uniacid']} AND id = {$goodslist[2]['goodsid']}");
			$goods['scprice'] = str_replace(".00","",$goods['scprice']);
			$goods['normalprice'] = str_replace(".00","",$goods['normalprice']);
			$thumb = tomedia($goods['thumb']);
			$haibao_obj->addPic($new,$thumb,140,140,50,480,$new);
			$goodsTitle = $haibao_obj->cn_row_substr($goods['title'],2,5);
			$haibao_obj->addWord($goodsTitle[1],200,500,20,'black',$ziti,$new);
			$haibao_obj->addWord($goodsTitle[2],200,530,20,'black',$ziti,$new);
			
			$haibao_obj->addWord('￥'.$goods['scprice'],200,570,20,'hui',$ziti,$new);
			$haibao_obj->addWord('￥'.$goods['normalprice'],200,610,24,'red',$ziti,$new);
		}
		
		if(isset($goodslist[3])){
			$goods = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE weid = {$_W['uniacid']} AND id = {$goodslist[3]['goodsid']}");
			$goods['scprice'] = str_replace(".00","",$goods['scprice']);
			$goods['normalprice'] = str_replace(".00","",$goods['normalprice']);
			$thumb = tomedia($goods['thumb']);
			$haibao_obj->addPic($new,$thumb,140,140,390,480,$new);
			$goodsTitle = $haibao_obj->cn_row_substr($goods['title'],2,5);
			$haibao_obj->addWord($goodsTitle[1],540,500,20,'black',$ziti,$new);
			$haibao_obj->addWord($goodsTitle[2],540,530,20,'black',$ziti,$new);
			
			$haibao_obj->addWord('￥'.$goods['scprice'],540,570,20,'hui',$ziti,$new);
			$haibao_obj->addWord('￥'.$goods['normalprice'],540,610,24,'red',$ziti,$new);
		}
		
		if(isset($goodslist[4])){
			$goods = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE weid = {$_W['uniacid']} AND id = {$goodslist[4]['goodsid']}");
			$goods['scprice'] = str_replace(".00","",$goods['scprice']);
			$goods['normalprice'] = str_replace(".00","",$goods['normalprice']);
			$thumb = tomedia($goods['thumb']);
			$haibao_obj->addPic($new,$thumb,140,140,50,680,$new);
			$goodsTitle = $haibao_obj->cn_row_substr($goods['title'],2,5);
			$haibao_obj->addWord($goodsTitle[1],200,700,20,'black',$ziti,$new);
			$haibao_obj->addWord($goodsTitle[2],200,730,20,'black',$ziti,$new);
			
			$haibao_obj->addWord('￥'.$goods['scprice'],200,770,20,'hui',$ziti,$new);
			$haibao_obj->addWord('￥'.$goods['normalprice'],200,810,24,'red',$ziti,$new);
		}
		
		if(isset($goodslist[5])){
			$goods = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE weid = {$_W['uniacid']} AND id = {$goodslist[5]['goodsid']}");
			$goods['scprice'] = str_replace(".00","",$goods['scprice']);
			$goods['normalprice'] = str_replace(".00","",$goods['normalprice']);
			$thumb = tomedia($goods['thumb']);
			$haibao_obj->addPic($new,$thumb,140,140,390,680,$new);
			$goodsTitle = $haibao_obj->cn_row_substr($goods['title'],2,5);
			$haibao_obj->addWord($goodsTitle[1],540,700,20,'black',$ziti,$new);
			$haibao_obj->addWord($goodsTitle[2],540,730,20,'black',$ziti,$new);
			
			$haibao_obj->addWord('￥'.$goods['scprice'],540,770,20,'hui',$ziti,$new);
			$haibao_obj->addWord('￥'.$goods['normalprice'],540,810,24,'red',$ziti,$new);
		}
		
		if(isset($goodslist[6])){
			$goods = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE weid = {$_W['uniacid']} AND id = {$goodslist[6]['goodsid']}");
			$goods['scprice'] = str_replace(".00","",$goods['scprice']);
			$goods['normalprice'] = str_replace(".00","",$goods['normalprice']);
			$thumb = tomedia($goods['thumb']);
			$haibao_obj->addPic($new,$thumb,140,140,50,880,$new);
			$goodsTitle = $haibao_obj->cn_row_substr($goods['title'],2,5);
			$haibao_obj->addWord($goodsTitle[1],200,900,20,'black',$ziti,$new);
			$haibao_obj->addWord($goodsTitle[2],200,930,20,'black',$ziti,$new);
			
			$haibao_obj->addWord('￥'.$goods['scprice'],200,970,20,'hui',$ziti,$new);
			$haibao_obj->addWord('￥'.$goods['normalprice'],200,1010,24,'red',$ziti,$new);
		}
		
		if(isset($goodslist[7])){
			$goods = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE weid = {$_W['uniacid']} AND id = {$goodslist[7]['goodsid']}");
			$goods['scprice'] = str_replace(".00","",$goods['scprice']);
			$goods['normalprice'] = str_replace(".00","",$goods['normalprice']);
			$thumb = tomedia($goods['thumb']);
			$haibao_obj->addPic($new,$thumb,140,140,390,880,$new);
			$goodsTitle = $haibao_obj->cn_row_substr($goods['title'],2,5);
			$haibao_obj->addWord($goodsTitle[1],540,900,20,'black',$ziti,$new);
			$haibao_obj->addWord($goodsTitle[2],540,930,20,'black',$ziti,$new);
			
			$haibao_obj->addWord('￥'.$goods['scprice'],540,970,20,'hui',$ziti,$new);
			$haibao_obj->addWord('￥'.$goods['normalprice'],540,1010,24,'red',$ziti,$new);
		}
		
	
		if(isset($merchant)){
			$haibao_obj->addWord($merchant['realname'],145,1125,18,'black',$ziti,$new);
		}
		if(isset($hdres)){
			$qgtime = date("Y-m-d H:i:s",$hdres['starttime']);
			$haibao_obj->addWord($qgtime,145,1165,18,'black',$ziti,$new);
		}
		
		$ztdlist = pdo_fetch("SELECT address FROM ".tablename(BEST_ZITIDIAN)." WHERE openid = '{$merchant['openid']}' AND weid = {$_W['uniacid']} AND ztdtype = 1");
		
		$desTitle = $haibao_obj->cn_row_substr($ztdlist['address'],2,12);
		$haibao_obj->addWord($desTitle[1],145,1200,18,'black',$ziti,$new);
		$haibao_obj->addWord($desTitle[2],145,1235,18,'black',$ziti,$new);
		return $new;
	}
	
	
	public function createzfImg($data,$codeImg){
		global $_W,$_GPC;
		include_once ROOT_PATH.'Haibao.class.php';
		$haibao_obj = new Haibao();
		if($this->module['config']['goodsthumb'] != ""){
			$backImg = tomedia($this->module['config']['goodsthumb']);
		}else{
			$backImg = STATIC_ROOT."/goodsbg.jpg";
		}
		$new =  $data['new_img'];
		$goods_img = $data['goods_img'];
		// 添加二维码
		$haibao_obj->addPic($backImg,$codeImg,200,200,480,1070,$new);
		// 添加产品
		$haibao_obj->addPic($new,$goods_img,630,630,60,345,$new);
		
		$ziti = STATIC_ROOT.'/msyh.ttf';
		// 添加产品描述，对描述进行分行
		$theTitle = $haibao_obj->cn_row_substr($data['title'],2,10);
		$haibao_obj->addWord($theTitle[1],60,1140,24,'black',$ziti,$new);
		$haibao_obj->addWord($theTitle[2],60,1180,24,'black',$ziti,$new);

		// 添加价格1
		$haibao_obj->addWord($data['price_market'],60,1080,34,'red',$ziti,$new);
		// 添加价格2
		$haibao_obj->addWord($data['price_member'],60,1220,22,'hui',$ziti,$new);
		return $new;
	}
	
	public function createmerImg($data,$codeImg){
		global $_W,$_GPC;
		include_once ROOT_PATH.'Haibao.class.php';
		$haibao_obj = new Haibao();
		$backImg = tomedia($this->module['config']['tdthumb']);
		$new =  $data['new_img'];
		// 添加二维码
		$haibao_obj->addPic($backImg,$codeImg,180,180,$this->module['config']['tdleft'],$this->module['config']['tdbottom'],$new);
		return $new;
	}
	
	public function Convert_BD09_To_GCJ02($lat,$lng){  
			$x_pi = 3.14159265358979324 * 3000.0 / 180.0;  
			$x = $lng - 0.0065;  
			$y = $lat - 0.006;  
			$z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);  
			$theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);  
			$lng = $z * cos($theta);  
			$lat = $z * sin($theta);  
			return array('lng'=>$lng,'lat'=>$lat);  
	}
	
	public function doMobileIndexhb() {
		include_once ROOT_PATH.'inc/mobile/indexhb.php';
	}
	
	public function doMobileIndex() {
		global $_W,$_GPC;
		$member = $this->Mcheckmember();
		$xqid = intval($_GPC['xqid']);
		if($xqid > 0){
			if($member['viewxqs'] == ""){
				$datam['viewxqs'] = $xqid;
				pdo_update(BEST_MEMBER,$datam,array('id'=>$member['id']));
			}else{
				$viewxqsarr = explode("-",$member['viewxqs']);
				if(count($viewxqsarr) < 3){
					$datam['viewxqs'] = $member['viewxqs']."-".$xqid;
					pdo_update(BEST_MEMBER,$datam,array('id'=>$member['id']));
				}else{
					foreach($viewxqsarr as $k=>$v){
						
					}
					$datam['viewxqs'] = $viewxqsarr[1]."-".$viewxqsarr[2]."-".$xqid;
					pdo_update(BEST_MEMBER,$datam,array('id'=>$member['id']));
				}
			}
			$xiaoqu = pdo_fetch("SELECT * FROM ".tablename(BEST_XIAOQU)." WHERE weid = {$_W['uniacid']} AND id = {$xqid}");
		}else{
			if($member['viewxqs'] != ""){
				$viewxqsarr = explode("-",$member['viewxqs']);
				if(count($viewxqsarr) == 1){
					$xqid = $viewxqsarr[0];
				}
				if(count($viewxqsarr) == 2){
					$xqid = $viewxqsarr[1];
				}
				if(count($viewxqsarr) == 3){
					$xqid = $viewxqsarr[2];
				}
				$xiaoqu = pdo_fetch("SELECT * FROM ".tablename(BEST_XIAOQU)." WHERE weid = {$_W['uniacid']} AND id = {$xqid}");
			}else{
				$ipurl = "https://apis.map.qq.com/ws/location/v1/ip?ip=".CLIENT_IP."&key=".$this->module['config']['mapkey'];
				$ipres = file_get_contents($ipurl);
				$ipres = json_decode($ipres,true);
				$latitude = $ipres['result']['location']['lat'];
				$longitude = $ipres['result']['location']['lng'];
				$dushu = 100;
				$fanwei = 180;
				$sql = "SELECT * FROM ".tablename(BEST_XIAOQU)." WHERE weid = {$_W['uniacid']} AND weidu > ({$latitude}-{$dushu}) AND weidu < ({$latitude}+{$dushu}) AND jingdu > ({$longitude}-{$dushu}) AND jingdu < ({$longitude}+{$dushu}) ORDER BY ACOS(SIN(({$latitude}*3.1415)/{$fanwei}) *SIN((weidu*3.1415)/{$fanwei}) +COS(({$latitude}*3.1415)/{$fanwei})*COS((weidu*3.1415)/{$fanwei})*COS(({$longitude}*3.1415)/{$fanwei}-(jingdu*3.1415)/{$fanwei}))*6380 ASC LIMIT 1";
				$xiaoqu = pdo_fetch($sql);
			}
		}
		$xqid = $xiaoqu["id"];
		$merchant = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} AND openid = '{$xiaoqu['zzopenid']}'");
		
		$hdres = pdo_fetch("SELECT * FROM ".tablename(BEST_HUODONG)." WHERE weid = {$_W['uniacid']} AND isxq = 1 AND isdqhd = 1");
		$hdres['id'] = empty($hdres) ? 0 : $hdres['id'];
		$daojishi = 1;
		if($hdres['endtime'] < TIMESTAMP || $hdres['tqjs'] == 1){
			$daojishi = 0;
		}
		$merchanthd = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANTHD)." WHERE weid = {$_W['uniacid']} AND hdid = {$hdres['id']} AND merchant_id = {$merchant['id']} AND isxq = 1");
		$merchanthd['id'] = empty($merchanthd) ? 0 : $merchanthd['id'];
		
		
		$cateid = intval($_GPC['cateid']);
		if($cateid > 0){
			$goodslist = pdo_fetchall("SELECT a.mhdid,a.goodsid,a.optionid FROM ".tablename(BEST_MERCHANTHDGOODS)." as a,".tablename(BEST_GOODS)." as b WHERE b.cateid = {$cateid} AND a.goodsid = b.id AND a.mhdid = {$merchanthd['id']} GROUP BY a.goodsid");
		}else{
			$goodslist = pdo_fetchall("SELECT mhdid,goodsid,optionid FROM ".tablename(BEST_MERCHANTHDGOODS)." WHERE mhdid = {$merchanthd['id']} GROUP BY goodsid");
		}
		$cateidarr = array(0);
		
		foreach($goodslist as $k=>$v){
			$hdgoods = pdo_fetch("SELECT displayorder FROM ".tablename(BEST_HUODONGGOODS)." WHERE weid = {$_W['uniacid']} AND hdid = {$hdres['id']} AND goodsid = {$v['goodsid']}");
			
			$goodslist[$k]['goods'] = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE weid = {$_W['uniacid']} AND id = {$v['goodsid']}");
			$cateidarr[] = $goodslist[$k]['goods']['cateid'];
			
			$goodslist[$k]['normalprice'] = $goodslist[$k]['goods']['normalprice'];
			$goodslist[$k]['scprice'] = $goodslist[$k]['goods']['scprice'];
			$goodslist[$k]['sales'] = pdo_fetchcolumn("SELECT SUM(a.total) FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.goodsid = {$v['goodsid']} AND a.orderid = b.id AND b.hdid = {$merchanthd['hdid']} AND b.status >= 1");
			$goodslist[$k]['sales'] = empty($goodslist[$k]['sales']) ? $goodslist[$k]['goods']['basicsales'] : $goodslist[$k]['goods']['basicsales']+$goodslist[$k]['sales'];
			
			$salelist = pdo_fetchall("SELECT b.from_user FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.goodsid = {$v['goodsid']} AND a.orderid = b.id AND b.hdid = {$merchanthd['hdid']} AND b.status >= 1 ORDER BY b.createtime DESC LIMIT 3");

			foreach($salelist as $kk=>$vv){
				$membersa = pdo_fetch("SELECT nickname,avatar FROM ".tablename(BEST_MEMBER)." WHERE weid = {$_W['uniacid']} AND openid = '{$vv['from_user']}'");
				$salelist[$kk]['nickname'] = $membersa['nickname'];
				$salelist[$kk]['avatar'] = $membersa['avatar'];
			}
			$goodslist[$k]['salelist'] = $salelist;
			$goodslist[$k]['views'] = $goodslist[$k]['goods']['views'];
			$goodslist[$k]['displayorder'] = $hdgoods['displayorder'];
		}
		
		$gcates = pdo_fetchall("SELECT * FROM ".tablename(BEST_GCAT)." WHERE weid = {$_W['uniacid']} AND id in (".implode(',',$cateidarr).") ORDER BY displayorder ASC");

		if(!function_exists('array_column')){
			array_multisort($this->getNewArrByElement($goodslist,'displayorder'),SORT_DESC,$goodslist);
		}else{
			array_multisort(array_column($goodslist,'displayorder'),SORT_DESC,$goodslist);
		}
		$cartnum = pdo_fetchcolumn("SELECT SUM(total) FROM ".tablename(BEST_CART)." WHERE xqid = {$xqid} AND hdid = {$hdres['id']} AND openid = '{$member['openid']}'");
		
		$salelist2 = pdo_fetchall("SELECT b.from_user,a.* FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.mhdid = {$merchanthd['id']} AND a.orderid = b.id AND b.status >= 1 ORDER BY b.createtime DESC LIMIT 10");
		foreach($salelist2 as $kk=>$vv){
			$membersa = pdo_fetch("SELECT nickname,avatar FROM ".tablename(BEST_MEMBER)." WHERE weid = {$_W['uniacid']} AND openid = '{$vv['from_user']}'");
			$salelist2[$kk]['nickname'] = $membersa['nickname'];
			$salelist2[$kk]['avatar'] = $membersa['avatar'];
		}
		
		//下期预告
		$hdres2 = pdo_fetch("SELECT * FROM ".tablename(BEST_HUODONG)." WHERE weid = {$_W['uniacid']} AND isdqhd = 2 AND isxq = 1");
		$hdres2['id'] = empty($hdres2) ? 0 : $hdres2['id'];
		$merchanthd2 = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANTHD)." WHERE weid = {$_W['uniacid']} AND hdid = {$hdres2['id']} AND merchant_id = {$merchant['id']} AND isxq = 1");
		$merchanthd2['id'] = empty($merchanthd2) ? 0 : $merchanthd2['id'];
		
		$goodslist2 = pdo_fetchall("SELECT mhdid,goodsid,optionid FROM ".tablename(BEST_MERCHANTHDGOODS)." WHERE mhdid = {$merchanthd2['id']} GROUP BY goodsid");
		foreach($goodslist2 as $k=>$v){
			$hdgoods = pdo_fetch("SELECT displayorder FROM ".tablename(BEST_HUODONGGOODS)." WHERE weid = {$_W['uniacid']} AND hdid = {$hdres2['id']} AND goodsid = {$v['goodsid']}");
			
			$goodslist2[$k]['goods'] = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE weid = {$_W['uniacid']} AND id = {$v['goodsid']}");
			$goodslist2[$k]['normalprice'] = $goodslist2[$k]['goods']['normalprice'];
			$goodslist2[$k]['scprice'] = $goodslist2[$k]['goods']['scprice'];

			$goodslist2[$k]['salelist'] = '';
			$goodslist2[$k]['sales'] = 0;
			$goodslist2[$k]['views'] = $goodslist2[$k]['goods']['views'];
			$goodslist2[$k]['displayorder'] = $hdgoods['displayorder'];
		}
		
		if(!function_exists('array_column')){
			array_multisort($this->getNewArrByElement($goodslist2,'displayorder'),SORT_DESC,$goodslist2);
		}else{
			array_multisort(array_column($goodslist2,'displayorder'),SORT_DESC,$goodslist2);
		}
	
		$shareurl = $_W["siteroot"].'app/'.str_replace("./","",$this->createMobileUrl('index',array('xqid'=>$xqid)));
		include $this->template('index');
	}
	
	
	public function doMobileIndexajax() {
		global $_W,$_GPC;
		$member = $this->Mcheckmember();
		$xqid = intval($_GPC['xqid']);
		if($xqid > 0){
			$xiaoqu = pdo_fetch("SELECT * FROM ".tablename(BEST_XIAOQU)." WHERE weid = {$_W['uniacid']} AND id = {$xqid}");
		}else{
			if($member['viewxqs'] != ""){
				$viewxqsarr = explode("-",$member['viewxqs']);
				if(count($viewxqsarr) == 1){
					$xqid = $viewxqsarr[0];
				}
				if(count($viewxqsarr) == 2){
					$xqid = $viewxqsarr[1];
				}
				if(count($viewxqsarr) == 3){
					$xqid = $viewxqsarr[2];
				}
				$xiaoqu = pdo_fetch("SELECT * FROM ".tablename(BEST_XIAOQU)." WHERE weid = {$_W['uniacid']} AND id = {$xqid}");
			}else{
				$ipurl = "https://apis.map.qq.com/ws/location/v1/ip?ip=".CLIENT_IP."&key=".$this->module['config']['mapkey'];
				$ipres = file_get_contents($ipurl);
				$ipres = json_decode($ipres,true);
				$latitude = $ipres['result']['location']['lat'];
				$longitude = $ipres['result']['location']['lng'];
				$dushu = 100;
				$fanwei = 180;
				$sql = "SELECT * FROM ".tablename(BEST_XIAOQU)." WHERE weid = {$_W['uniacid']} AND weidu > ({$latitude}-{$dushu}) AND weidu < ({$latitude}+{$dushu}) AND jingdu > ({$longitude}-{$dushu}) AND jingdu < ({$longitude}+{$dushu}) ORDER BY ACOS(SIN(({$latitude}*3.1415)/{$fanwei}) *SIN((weidu*3.1415)/{$fanwei}) +COS(({$latitude}*3.1415)/{$fanwei})*COS((weidu*3.1415)/{$fanwei})*COS(({$longitude}*3.1415)/{$fanwei}-(jingdu*3.1415)/{$fanwei}))*6380 ASC LIMIT 1";
				$xiaoqu = pdo_fetch($sql);
			}
		}
		$xqid = $xiaoqu["id"];
		$merchant = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} AND openid = '{$xiaoqu['zzopenid']}'");
		
		$hdres = pdo_fetch("SELECT * FROM ".tablename(BEST_HUODONG)." WHERE weid = {$_W['uniacid']} AND isxq = 1 AND isdqhd = 1");
		$hdres['id'] = empty($hdres) ? 0 : $hdres['id'];

		$merchanthd = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANTHD)." WHERE weid = {$_W['uniacid']} AND hdid = {$hdres['id']} AND merchant_id = {$merchant['id']} AND isxq = 1");
		$merchanthd['id'] = empty($merchanthd) ? 0 : $merchanthd['id'];
		
		$cateid = intval($_GPC['cateid']);
		if($cateid > 0){
			$goodslist = pdo_fetchall("SELECT a.mhdid,a.goodsid,a.optionid FROM ".tablename(BEST_MERCHANTHDGOODS)." as a,".tablename(BEST_GOODS)." as b WHERE b.cateid = {$cateid} AND a.goodsid = b.id AND a.mhdid = {$merchanthd['id']} GROUP BY a.goodsid");
		}else{
			$goodslist = pdo_fetchall("SELECT mhdid,goodsid,optionid FROM ".tablename(BEST_MERCHANTHDGOODS)." WHERE mhdid = {$merchanthd['id']} GROUP BY goodsid");
		}
		
		foreach($goodslist as $k=>$v){
			$hdgoods = pdo_fetch("SELECT displayorder FROM ".tablename(BEST_HUODONGGOODS)." WHERE weid = {$_W['uniacid']} AND hdid = {$hdres['id']} AND goodsid = {$v['goodsid']}");
			$goodslist[$k]['goods'] = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE weid = {$_W['uniacid']} AND id = {$v['goodsid']}");

			
			$goodslist[$k]['normalprice'] = $goodslist[$k]['goods']['normalprice'];
			$goodslist[$k]['scprice'] = $goodslist[$k]['goods']['scprice'];
			$goodslist[$k]['sales'] = pdo_fetchcolumn("SELECT SUM(a.total) FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.goodsid = {$v['goodsid']} AND a.orderid = b.id AND b.hdid = {$merchanthd['hdid']} AND b.status >= 1");
			$goodslist[$k]['sales'] = empty($goodslist[$k]['sales']) ? $goodslist[$k]['goods']['basicsales'] : $goodslist[$k]['goods']['basicsales']+$goodslist[$k]['sales'];
			
			$salelist = pdo_fetchall("SELECT b.from_user FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.goodsid = {$v['goodsid']} AND a.orderid = b.id AND b.hdid = {$merchanthd['hdid']} AND b.status >= 1 ORDER BY b.createtime DESC LIMIT 3");

			foreach($salelist as $kk=>$vv){
				$membersa = pdo_fetch("SELECT nickname,avatar FROM ".tablename(BEST_MEMBER)." WHERE weid = {$_W['uniacid']} AND openid = '{$vv['from_user']}'");
				$salelist[$kk]['nickname'] = $membersa['nickname'];
				$salelist[$kk]['avatar'] = $membersa['avatar'];
			}
			$goodslist[$k]['salelist'] = $salelist;
			$goodslist[$k]['views'] = $goodslist[$k]['goods']['views'];
			$goodslist[$k]['displayorder'] = $hdgoods['displayorder'];
		}
		if(!function_exists('array_column')){
			array_multisort($this->getNewArrByElement($goodslist,'displayorder'),SORT_DESC,$goodslist);
		}else{
			array_multisort(array_column($goodslist,'displayorder'),SORT_DESC,$goodslist);
		}
		
		include $this->template('indexajax');
	}
	
	public function getxqmsg($xqid){
		global $_W,$_GPC;
		$xiaoqu = pdo_fetch("SELECT * FROM ".tablename(BEST_XIAOQU)." WHERE weid = {$_W['uniacid']} AND id = {$xqid}");
		$merchant = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} AND openid = '{$xiaoqu['zzopenid']}'");
		$hdres = pdo_fetch("SELECT * FROM ".tablename(BEST_HUODONG)." WHERE weid = {$_W['uniacid']} AND isxq = 1 AND isdqhd = 1");
		$merchanthd = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANTHD)." WHERE weid = {$_W['uniacid']} AND hdid = {$hdres['id']} AND merchant_id = {$merchant['id']} AND isxq = 1");
		return array(
			'xiaoqu'=>$xiaoqu,
			'merchant'=>$merchant,
			'hdres'=>$hdres,
			'merchanthd'=>$merchanthd,
		);
	}
	
	public function getNewArrByElement($list, $element) {
		if(!isset($list) || !is_array($list) || empty($list)) {
			return array();
		}
		if(!isset($element) || empty($element)) {
			return array();
		}
		$j = 0;
		$result = array();
		for($i = 0; $i < count($list); $i++) {
			if(isset($list [$i] [$element])) {
				$result [$j] = $list [$i] [$element];
				$j++;
			} else {
				return array();
			}
		}
		return $result;
	}
	
	public function doMobileSqcart() {
		global $_W,$_GPC;
		if(!checksubmit('submit')){
			$resArr['error'] = 1;
			$resArr['message'] = "请不要频繁提交！";
			echo json_encode($resArr);
			exit;
		}
		$member = $this->Mcheckmember();
		$xqid = intval($_GPC['xqid']);
		$xqmsg = $this->getxqmsg($xqid);
		if(empty($xqmsg['xiaoqu'])){
			$resArr['error'] = 1;
			$resArr['message'] = "获取小区信息失败！";
			echo json_encode($resArr);
			exit;
		}
		$hdres = $xqmsg['hdres'];
		if(empty($hdres)){
			$resArr['error'] = 1;
			$resArr['message'] = "获取活动信息失败！";
			echo json_encode($resArr);
			exit;
		}
		if($hdres['tqjs'] == 1){
			$resArr['error'] = 1;
			$resArr['message'] = "活动已经提前结束！";
			echo json_encode($resArr);
			exit;
		}
		if($hdres['starttime'] > TIMESTAMP){
			$resArr['error'] = 1;
			$resArr['message'] = "活动还未开始！";
			echo json_encode($resArr);
			exit;
		}
		if($hdres['endtime'] < TIMESTAMP){
			$resArr['error'] = 1;
			$resArr['message'] = "活动已经结束！";
			echo json_encode($resArr);
			exit;
		}
		$merchanthd = $xqmsg['merchanthd'];
		
		$goodsid = intval($_GPC['goodsid']);
		$optionid = intval($_GPC['optionid']);
		$goodsres = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE id = {$goodsid}");
		if($goodsres['hasoption'] == 1 && $optionid == 0){
			$resArr['error'] = 1;
			$resArr['message'] = "请选择商品规格！";
			echo json_encode($resArr);
			exit;
		}
		
		$hascart = pdo_fetch("SELECT id,total FROM ".tablename(BEST_CART)." WHERE weid = {$_W['uniacid']} AND openid = '{$member['openid']}' AND mhdid = {$merchanthd['id']} AND goodsid = {$goodsid} AND optionid = {$optionid}");
		$total = empty($hascart) ? 1 : $hascart['total']+1;
		if($optionid == 0){
			if($goodsres['total'] < $total){
				$resArr['error'] = 1;
				$resArr['message'] = "库存不足！";
				echo json_encode($resArr);
				exit;
			}
			if($goodsres['xiangounum'] > 0){
				$hassales = pdo_fetchcolumn("SELECT SUM(a.total) FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.goodsid = {$goodsres['id']} AND a.orderid = b.id AND b.from_user = '{$member['openid']}' AND b.hdid = {$hdres['id']} AND b.status >= 0");
				$hassales = empty($hassales) ? 0 : $hassales;
				$xiangounum = $goodsres['xiangounum']-$hassales;
				if($xiangounum < $total){
					$resArr['error'] = 1;
					$resArr['message'] = "该商品限购".$goodsres['xiangounum']."件！";
					echo json_encode($resArr);
					exit;
				}
			}
		}else{
			$optionres = pdo_fetch("SELECT id,stock,xiangounum FROM ".tablename(BEST_GOODSOPTION)." WHERE id = {$optionid}");
			if(empty($optionres)){
				$resArr['error'] = 1;
				$resArr['message'] = "不存在该规格！";
				echo json_encode($resArr);
				exit;
			}
			if($optionres['stock'] < $total){
				$resArr['error'] = 1;
				$resArr['message'] = "库存不足！";
				echo json_encode($resArr);
				exit;
			}
			
			if($optionres['xiangounum'] > 0){
				$hassales = pdo_fetchcolumn("SELECT SUM(a.total) FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.optionid = {$optionres['id']} AND a.orderid = b.id AND b.from_user = '{$member['openid']}' AND b.hdid = {$hdres['id']} AND b.status >= 0");
				$hassales = empty($hassales) ? 0 : $hassales;
				$xiangounum = $optionres['xiangounum']-$hassales;
				if($xiangounum < $total){
					$resArr['error'] = 1;
					$resArr['message'] = "该商品限购".$optionres['xiangounum']."件！";
					echo json_encode($resArr);
					exit;
				}
			}
		}
		
		if(empty($hascart)){
			$data['weid'] = $_W['uniacid'];
			$data['openid'] = $member['openid'];
			$data['goodsid'] = $goodsid;
			$data['optionid'] = $optionid;
			$data['xqid'] = $xqid;
			$data['mhdid'] = $merchanthd['id'];
			$data['hdid'] = $hdres['id'];
			$data['total'] = 1;
			pdo_insert(BEST_CART,$data);
		}else{
			$data['total'] = $hascart['total']+1;
			pdo_update(BEST_CART,$data,array('id'=>$hascart['id']));
		}
		$resArr['error'] = 0;
		$resArr['message'] = "已加入购物车！";
		echo json_encode($resArr);
		exit;
	}
	
	public function doMobileChosexiaoqu() {
		global $_W,$_GPC;
		include $this->template('chosexiaoqu');
	}
	
	public function doMobileAjaxchosexiaoqu() {
		global $_W,$_GPC;
		$member = $this->Mcheckmember();
		$latitude = trim($_GPC['latitude']);
		$longitude = trim($_GPC['longitude']);
		
		$html1 = $html2 = "";
		if($member['viewxqs'] == ""){
			$viewxqlist = "";
		}else{
			$viewxqsarr = str_replace("-",",",$member['viewxqs']);
			$viewxqlist = pdo_fetchall("SELECT * FROM ".tablename(BEST_XIAOQU)." WHERE weid = {$_W['uniacid']} AND id in(".$viewxqsarr.")");
			foreach($viewxqlist as $k=>$v){
				if($latitude != "" && $longitude != ""){
					$dishtml = $this->getdistance2($latitude,$longitude,$v['weidu'],$v['jingdu'],2,3).'km';
				}else{
					$dishtml = "";
				}
				$html1 .= '<a href="'.$this->createMobileUrl('index',array('xqid'=>$v['id'])).'">
							<div class="xqlist flex">
								<div class="right">
									<div class="name">'.$v['name'].'</div>
									<div class="address flex">
										<div class="addleft">'.$v['address'].'</div>
										<div class="addright">'.$dishtml.'</div>
									</div>
								</div>
								<img src="'.STATIC_ROOT.'/right.png" class="to" />
							</div>
							</a>';
			}
		}
		
		$dushu = 100;
		$fanwei = 180;
		$sql = "SELECT * FROM ".tablename(BEST_XIAOQU)." WHERE weid = {$_W['uniacid']} AND weidu > ({$latitude}-{$dushu}) AND weidu < ({$latitude}+{$dushu}) AND jingdu > ({$longitude}-{$dushu}) AND jingdu < ({$longitude}+{$dushu}) ORDER BY ACOS(SIN(({$latitude}*3.1415)/{$fanwei}) *SIN((weidu*3.1415)/{$fanwei}) +COS(({$latitude}*3.1415)/{$fanwei})*COS((weidu*3.1415)/{$fanwei})*COS(({$longitude}*3.1415)/{$fanwei}-(jingdu*3.1415)/{$fanwei}))*6380 ASC LIMIT 6";
		$xiaoqulist = pdo_fetchall($sql);
		foreach($xiaoqulist as $k=>$v){
			if($latitude != "" && $longitude != ""){
				$dishtml = $this->getdistance2($latitude,$longitude,$v['weidu'],$v['jingdu'],2,3).'km';
			}else{
				$dishtml = "";
			}
			$html2 .= '<a href="'.$this->createMobileUrl('index',array('xqid'=>$v['id'])).'">
						<div class="xqlist flex">
							<div class="right">
								<div class="name">'.$v['name'].'</div>
								<div class="address flex">
									<div class="addleft">'.$v['address'].'</div>
									<div class="addright">'.$dishtml.'</div>
								</div>
							</div>
							<img src="'.STATIC_ROOT.'/right.png" class="to" />
						</div>
						</a>';
		}
		
		$resArr['html1'] = $html1;
		$resArr['html2'] = $html2;
		echo json_encode($resArr);
		exit;
	}
	
	/**
	* 计算两组经纬度坐标 之间的距离
	* params ：lat1 纬度1； lng1 经度1； lat2 纬度2； lng2 经度2； len_type （1:m or 2:km);
	* return m or km
	*/
	public function getdistance2($lat1, $lng1, $lat2, $lng2, $len_type = 2, $decimal = 0) {
		$radLat1 = $lat1 * 3.1415926535898 / 180.0;
		$radLat2 = $lat2 * 3.1415926535898 / 180.0;
		$a = $radLat1 - $radLat2;
		$b = ($lng1 * 3.1415926535898 / 180.0) - ($lng2 * 3.1415926535898 / 180.0);
		$s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1) * cos($radLat2) * pow(sin($b/2),2)));
		$s = $s * 6378.137;
		$s = round($s * 1000);
		if ($len_type > 1)
		{
		$s /= 1000;
		}
		return round($s, $decimal);
	}
	
	public function doMobileSearchxiaoqu() {
		global $_W,$_GPC;
		$keyword = trim($_GPC['keyword']);
		if(empty($keyword)){
			$resArr['error'] = 1;
			$resArr['message'] = '请输入小区名称搜索哦~';
			echo json_encode($resArr);
			exit;
		}
		$latitude = trim($_GPC['latitude']);
		$longitude = trim($_GPC['longitude']);		
		$xiaoqu = pdo_fetchall("SELECT * FROM ".tablename(BEST_XIAOQU)." WHERE weid = {$_W['uniacid']} AND name like '%{$keyword}%' LIMIT 6");
		if(empty($xiaoqu)){
			$resArr['error'] = 1;
			$resArr['message'] = '没有搜索到小区哦~';
		}else{
			$html = '';
			foreach($xiaoqu as $k=>$v){
				if($latitude != "" && $longitude != ""){
					$dishtml = $this->getdistance2($latitude,$longitude,$v['weidu'],$v['jingdu'],2,3).'km';
				}else{
					$dishtml = "";
				}
				$html .= '<a href="'.$this->createMobileUrl('index',array('xqid'=>$v['id'])).'">
								<div class="xqlist flex">
									<div class="right">
										<div class="name">'.$v['name'].'</div>
										<div class="address flex">
											<div class="addleft">'.$v['address'].'</div>
											<div class="addright">'.$dishtml.'</div>
										</div>
									</div>
									<img src="'.STATIC_ROOT.'/right.png" class="to" />
								</div>
								</a>';
			}
			$resArr['error'] = 0;
			$resArr['message'] = $html;
		}
		echo json_encode($resArr);
		exit;
	}
	
	public function doMobileStore() {
		global $_W,$_GPC;
		$openid = trim($_GPC['openid']);
		$member = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$openid}'");
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_MEMBERHUODONG)." WHERE weid = {$_W['uniacid']} AND status = 1 AND owndel = 0 AND admindel = 0 AND openid = '{$openid}'");
		$allpage = ceil($total/10)+1;
		$page = intval($_GPC["page"]);
		$pindex = max(1, $page);
		$psize = 10;
		$hdlist = pdo_fetchall("SELECT * FROM ".tablename(BEST_MEMBERHUODONG)." WHERE weid = {$_W['uniacid']} AND status = 1 AND owndel = 0 AND admindel = 0 AND openid = '{$openid}' ORDER BY time DESC LIMIT ".($pindex - 1)*$psize.",".$psize);
		foreach($hdlist as $k=>$v){
			$thumbs = unserialize($v['thumbs']);			
			$hdlist[$k]['img1'] = tomedia($thumbs[0]);
			$hdlist[$k]['img2'] = tomedia($thumbs[1]);
			$hdlist[$k]['img3'] = tomedia($thumbs[2]);
			if($v['starttime'] > TIMESTAMP){
				$hdlist[$k]['status'] = '未开始';
			}elseif($v['endtime'] < TIMESTAMP){
				$hdlist[$k]['status'] = '已结束';
			}else{
				$hdlist[$k]['status'] = '<span>进行中</span>';
			}
		}
		$isajax = intval($_GPC['isajax']);
		if($isajax == 1){
			$html = '';
			foreach($hdlist as $k=>$v){
				if($v['address'] != ''){
					$addhtml = '<div class="address">'.$v['address'].'</div>';
				}else{
					$addhtml = '';
				}
				$img1 = $v['img1'] ? '<img src="'.$v['img1'].'" />' : '';
				$img2 = $v['img2'] ? '<img src="'.$v['img2'].'" />' : '';
				$img3 = $v['img3'] ? '<img src="'.$v['img3'].'" />' : '';
				$html .= '<div class="item flex">
						<img src="'.$v['avatar'].'" class="avatar" />
						<div class="right">
							<div class="nickname">'.$v['nickname'].'</div>
							<div class="jltj flex">
								<div class="jltjitem">'.$v['views'].'人浏览， '.date("m-d",$v['time']).'</div>
								<div class="jltjitem text-r jlstatus">'.$status.'</div>
							</div>
							<a href="'.$this->createMobileUrl('hddetail',array('id'=>$v['id'])).'">
								<div class="title">'.$v['title'].'</div>
								<div class="imgs flex">
									'.$img1.$img2.$img3.'
									<div style="flex:1;"></div>
								</div>
								'.$addhtml.'
							</a>
						</div>
					</div>';
			}
			echo $html;
			exit;
		}
		$shareurl = $_W["siteroot"].'app/'.str_replace("./","",$this->createMobileUrl('store',array('openid'=>$openid)));
		include $this->template('store');
	}
	
	public function doMobileFabuchose() {
		global $_W,$_GPC;
		$member = $this->Mcheckmember();
		$xqid = intval($_GPC['xqid']);
		$xqmsg = $this->getxqmsg($xqid);
		$merchant = $xqmsg['merchant'];
		$hdres = $xqmsg['hdres'];
		$merchanthd = $xqmsg['merchanthd'];
		if($hdres['pstype'] == 1){
			$hdres['cansonghuo'] = $merchanthd['cansonghuo'];
			$hdres['canziti'] = $merchanthd['canziti'];
			$hdres['manjian'] = $merchanthd['manjian'];
		}else{
			$hdres['cansonghuo'] = 1;
			$hdres['canziti'] = 0;
		}
		if($hdres['canziti'] == 1 && $hdres['cansonghuo'] == 0){
			$hdres['pstype'] = 1;
		}else{
			$hdres['pstype'] = 0;
		}
		
		if($hdres['cansonghuo'] == 1){
			$hdres['shzt'] = '满'.$hdres['manjian'].'元免运费';
		}else{
			$hdres['shzt'] = '免运费';
		}
		
		$cartlist = pdo_fetchall("SELECT * FROM ".tablename(BEST_CART)." WHERE weid = {$_W['uniacid']} AND hdid = {$hdres['id']} AND openid = '{$member['openid']}' AND xqid = {$xqid}");
		foreach($cartlist as $k=>$v){
			if($v['optionid'] > 0){
				$option = pdo_fetch("SELECT id FROM ".tablename(BEST_GOODSOPTION)." WHERE id = {$v['optionid']}");
				if(empty($option)){
					pdo_delete(BEST_CART,array('id'=>$v['id']));
				}
			}
		}
		$cartlist = pdo_fetchall("SELECT * FROM ".tablename(BEST_CART)." WHERE weid = {$_W['uniacid']} AND hdid = {$hdres['id']} AND openid = '{$member['openid']}' AND xqid = {$xqid}");
		foreach($cartlist as $k=>$v){
			$cartlist[$k]['goods'] = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE weid = {$_W['uniacid']} AND id = {$v['goodsid']}");
			if($v['optionid'] > 0){
				$cartlist[$k]['option'] = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODSOPTION)." WHERE id = {$v['optionid']}");
				$cartlist[$k]['price'] = $cartlist[$k]['option']['normalprice'];
				$cartlist[$k]['canbuy'] = $cartlist[$k]['option']['stock'];
			}else{
				$cartlist[$k]['option'] = 0;
				$cartlist[$k]['price'] = $cartlist[$k]['goods']['normalprice'];
				$cartlist[$k]['canbuy'] = $cartlist[$k]['goods']['total'];
			}
		}
		$ztdlist = pdo_fetchall("SELECT * FROM ".tablename(BEST_ZITIDIAN)." WHERE openid = '{$merchant['openid']}' AND weid = {$_W['uniacid']} AND ztdtype = 1");
		$shenglist = pdo_fetchall("SELECT * FROM ".tablename(BEST_CITY)." WHERE type = 1");
		include $this->template('fabuchose');
	}
	
	public function doMobileSqdelcart() {
		global $_W,$_GPC;
		$xqid = intval($_GPC['xqid']);
		$cartid = intval($_GPC['cartid']);
		$member = $this->Mcheckmember();
		if($cartid > 0){
			pdo_delete(BEST_CART,array('id'=>$cartid));
		}else{
			pdo_delete(BEST_CART,array('xqid'=>$xqid,'openid'=>$member['openid']));
		}
		$resArr['error'] = 0;
		$resArr['message'] = "清空购物车成功！";
		echo json_encode($resArr);
		exit;
	}
	
	public function doMobileSqchangecart() {
		global $_W,$_GPC;
		$cartid = intval($_GPC['cartid']);
		$total = intval($_GPC['total']);
		$cartres = pdo_fetch("SELECT * FROM ".tablename(BEST_CART)." WHERE id = {$cartid}");
		
		if($cartres['optionid'] == 0){
			$goodsres = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE id = {$cartres['goodsid']}");
			if($goodsres['total'] < $total){
				$resArr['error'] = 1;
				$resArr['message'] = "库存不足！";
				echo json_encode($resArr);
				exit;
			}
			
			if($goodsres['xiangounum'] > 0){
				$hassales = pdo_fetchcolumn("SELECT SUM(a.total) FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.goodsid = {$goodsres['id']} AND a.orderid = b.id AND b.from_user = '{$cartres['openid']}' AND b.hdid = {$cartres['hdid']} AND b.status >= 0");
				$hassales = empty($hassales) ? 0 : $hassales;
				$xiangounum = $goodsres['xiangounum']-$hassales;
				if($xiangounum < $total){
					$resArr['error'] = 1;
					$resArr['message'] = "该商品限购".$xiangounum."件！";
					echo json_encode($resArr);
					exit;
				}
			}
		}else{
			$optionres = pdo_fetch("SELECT id,stock,xiangounum FROM ".tablename(BEST_GOODSOPTION)." WHERE id = {$cartres['optionid']}");
			if(empty($optionres)){
				$resArr['error'] = 1;
				$resArr['message'] = "不存在该规格！";
				echo json_encode($resArr);
				exit;
			}
			if($optionres['stock'] < $total){
				$resArr['error'] = 1;
				$resArr['message'] = "库存不足！";
				echo json_encode($resArr);
				exit;
			}
			
			if($optionres['xiangounum'] > 0){
				$hassales = pdo_fetchcolumn("SELECT SUM(a.total) FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.optionid = {$optionres['id']} AND a.orderid = b.id AND b.from_user = '{$cartres['openid']}' AND b.hdid = {$cartres['hdid']} AND b.status >= 0");
				$hassales = empty($hassales) ? 0 : $hassales;
				$xiangounum = $optionres['xiangounum']-$hassales;
				if($xiangounum < $total){
					$resArr['error'] = 1;
					$resArr['message'] = "该商品限购".$xiangounum."件！";
					echo json_encode($resArr);
					exit;
				}
			}
		}

		pdo_update(BEST_CART,array('total'=>$total),array('id'=>$cartid));
		$resArr['error'] = 0;
		$resArr['message'] = "";
		echo json_encode($resArr);
		exit;
	}
	
	public function doMobileDosqsub() {
		include_once ROOT_PATH.'inc/mobile/dosqsub.php';
	}
	
	public function doMobileHddetail() {
		global $_W,$_GPC;
		$member = $this->Mcheckmember();
		$id = intval($_GPC['id']);
		$memberhd = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERHUODONG)." WHERE weid = {$_W['uniacid']} AND id = {$id} AND status = 1");
		$memberhd['inpeople'] = $memberhd['inpeople']+$memberhd['basicsales'];
		$memberhd['views'] = $memberhd['views']+$memberhd['basicviews'];
		$hdmember = pdo_fetch("SELECT openid,rztype FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$memberhd['openid']}'");
		if(empty($memberhd)){
			$message = '不存在该团购！';
			include $this->template('error');
			exit;
		}
		$data['views'] = $memberhd['views']+1;
		pdo_update(BEST_MEMBERHUODONG,$data,array('id'=>$id));
		$thumbs = unserialize($memberhd['thumbs']);
		$goodslist = pdo_fetchall("SELECT * FROM ".tablename(BEST_MEMBERGOODS)." WHERE weid = {$_W['uniacid']} AND hdid = {$id}");
		foreach($goodslist as $k=>$v){
			$goodsthumbs = unserialize($v['thumbs']);
			$goodslist[$k]['thumb'] = $goodsthumbs[0];
			$goodslist[$k]['thumb1'] = $goodsthumbs[1];
			$goodslist[$k]['thumb2'] = $goodsthumbs[2];
			$goodslist[$k]['count'] = count($goodsthumbs);
			$hasjieti = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERGOODSJIETI)." WHERE goodsid = {$v['id']} AND jietinumstart <= {$v['inpeople']} AND jietinum >= {$v['inpeople']}");
			if(!empty($hasjieti)){
				$goodslist[$k]['normalprice'] = $hasjieti['jietiprice'];
			}
			$goodslist[$k]['jietilist'] = pdo_fetchall("SELECT * FROM ".tablename(BEST_MEMBERGOODSJIETI)." WHERE goodsid = {$v['id']} ORDER BY jietiprice DESC");
			if($v['xiangounum'] > 0){			
				$hasbuynum = pdo_fetchcolumn("SELECT SUM(a.total) FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.jlid = {$id} AND a.goodsid = {$v['id']} AND b.status >= 0 AND a.orderid = b.id AND b.from_user = '{$member['openid']}'");
				$leftcanbuy = $v['xiangounum']-$hasbuynum;
				if($v['total'] < $leftcanbuy){
					$goodslist[$k]['canbuynum'] = $v['total'];
				}else{
					$goodslist[$k]['canbuynum'] = $leftcanbuy;
				}
			}else{
				$goodslist[$k]['canbuynum'] = $v['total'];
			}
		}

		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND isjl = 1 AND status >= 1 AND jlid = {$id}");
		$psize = 10;
		$allpage = ceil($total/$psize)+1;
		$page = intval($_GPC["page"]);
		$pindex = max(1, $page);
		$canyulist = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND isjl = 1 AND status >= 1 AND jlid = {$id} ORDER BY createtime LIMIT ".($pindex - 1)*$psize.",".$psize);
		$canyunum = 1;
		$canyunum2 = 1;
		
		$zhantie = "团购名称：".$memberhd['title']."\n团购详情：".$memberhd['des']."\n团购详情：\n";
		$canyulist2 = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND isjl = 1 AND status >= 1 AND jlid = {$id} ORDER BY createtime");
		foreach($canyulist2 as $k=>$v){
			$ztmember = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$v['from_user']}'");
			$zttotal = pdo_fetchcolumn("SELECT SUM(total) FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$v['id']}");
			$ordergoods = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$v['id']}");
			$zhantie .= $canyunum2."、".$ztmember['nickname']."(".$ordergoods['goodsname']."... 接力 × ".$zttotal.")\n";
			$canyunum2++;
		}
		$zhantie .= "去参团：".$_W["siteroot"].'app/'.str_replace("./","",$this->createMobileUrl('hddetail',array('id'=>$id)));
		foreach($canyulist as $k=>$v){
			$canyulist[$k]['canyunum'] = $canyunum;
			$canyulist[$k]['member'] = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$v['from_user']}'");
			$canyulist[$k]['total'] = pdo_fetchcolumn("SELECT SUM(total) FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$v['id']}");
			$canyunum++;
		}
		$jlallprice = pdo_fetchcolumn("SELECT SUM(price) FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND isjl = 1 AND status >= 1 AND jlid = {$id}");
		$jlallprice = empty($jlallprice) ? "0.00" : round($jlallprice,2);
		$jlallprice = $jlallprice+$jlallprice*$memberhd['basicsales'];
		$shareurl = $_W["siteroot"].'app/'.str_replace("./","",$this->createMobileUrl('hddetail',array('id'=>$id)));
		$sharethumb = !empty($memberhd['sharethumb']) ? tomedia($memberhd['sharethumb']) : tomedia($thumbs[0]);		
		include $this->template('hddetail');
	}
	
	public function doMobileAjaxjlpeople() {
		global $_W,$_GPC;
		$id = intval($_GPC['id']);
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND isjl = 1 AND status = 1 AND jlid = {$id}");
		$psize = 10;
		$allpage = ceil($total/$psize)+1;
		$page = intval($_GPC["page"]);
		$pindex = max(1, $page);
		$canyulist = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND isjl = 1 AND status = 1 AND jlid = {$id} ORDER BY createtime LIMIT ".($pindex - 1)*$psize.",".$psize);
		$canyunum = $psize*($page-1)+1;
		$html = '';
		foreach($canyulist as $k=>$v){
			$member = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$v['from_user']}'");
			$gtotal = pdo_fetchcolumn("SELECT SUM(total) FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$v['id']}");
			$html .= '<div class="item flex">
						<div class="number">No.'.$canyunum.'</div>
						<img src="'.$member['avatar'].'" />
						<div class="nickname textellipsis1">'.$member['nickname'].'</div>
						<div class="num">+'.$gtotal.'</div>
						<div class="time">'.date("Y-m-d H:i",$v['createtime']).'</div>
					</div>';
			$canyunum++;
		}
		echo $html;
	}
	
	public function doMobileGetgoodstbigimg() {
		global $_W,$_GPC;
		$goodsid = intval($_GPC['goodsid']);
		$goods = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERGOODS)." WHERE id = {$goodsid} AND weid = {$_W['uniacid']}");
		$imglist = unserialize($goods['thumbs']);
		if(!empty($imglist)){
			$imglistval = '';
			foreach($imglist as $k=>$v){
				$imglistval .= tomedia($v).',';
			}
			$imglistval = substr($imglistval,0,-1);
			$resArr['error'] = 0;
			$resArr['message'] = $imglistval;
			echo json_encode($resArr);
			exit;
		}else{
			$resArr['error'] = 1;
			$resArr['message'] = "";
			echo json_encode($resArr);
			exit;
		}
	}
	
	public function doMobileGetgoodstbigimgdl() {
		global $_W,$_GPC;
		$goodsid = intval($_GPC['goodsid']);
		$goods = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE id = {$goodsid} AND weid = {$_W['uniacid']}");
		$imglist = unserialize($goods['thumbs']);
		if(!empty($imglist)){
			$imglistval = tomedia($goods['thumb']).',';
			foreach($imglist as $k=>$v){
				$imglistval .= tomedia($v).',';
			}
			$imglistval = substr($imglistval,0,-1);
			$resArr['error'] = 0;
			$resArr['message'] = $imglistval;
			echo json_encode($resArr);
			exit;
		}else{
			$imglistval = tomedia($goods['thumb']);
			$resArr['error'] = 0;
			$resArr['message'] = $imglistval;
			echo json_encode($resArr);
			exit;
		}
	}
	
	public function doMobileGethuodongbigimg() {
		global $_W,$_GPC;
		$hdid = intval($_GPC['hdid']);
		$imgsrc = trim($_GPC['imgsrc']);
		$hd = pdo_fetch("SELECT thumbs FROM ".tablename(BEST_MEMBERHUODONG)." WHERE id = {$hdid} AND weid = {$_W['uniacid']}");
		$imglist = unserialize($hd['thumbs']);
		if(!empty($imglist)){
			$imglistval = '';
			$nowindex = 0;
			foreach($imglist as $k=>$v){
				$v = tomedia($v);
				if($v == $imgsrc){
					$nowindex = $k;
				}
				$imglistval .= $v.',';
			}
			$imglistval = substr($imglistval,0,-1);
			$resArr['error'] = 0;
			$resArr['message'] = $imglistval;
			$resArr['index'] = $nowindex;
			echo json_encode($resArr);
			exit;
		}else{
			$resArr['error'] = 1;
			$resArr['message'] = "";
			echo json_encode($resArr);
			exit;
		}
	}
	
	public function doMobileMembersub() {
		global $_W,$_GPC;
		$member = $this->Mcheckmember();
		$jlid = intval($_GPC['jlid']);
		$memberhd = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERHUODONG)." WHERE weid = {$_W['uniacid']} AND id = {$jlid}");
		if(empty($memberhd)){
			$resArr['error'] = 1;
			$resArr['message'] = "不存在该团购！";
			echo json_encode($resArr);
			exit;
		}
		if($member['openid'] == $memberhd['openid']){
			$resArr['error'] = 1;
			$resArr['message'] = "不能参与自己发布的团购！";
			echo json_encode($resArr);
			exit;
		}
		if($memberhd['owndel'] == 1 || $memberhd['admindel'] == 1){
			$resArr['error'] = 1;
			$resArr['message'] = "团购已经结束！";
			echo json_encode($resArr);
			exit;
		}
		if($memberhd['isxiugai'] == 1){
			$resArr['error'] = 1;
			$resArr['message'] = "团购正在修改中，暂不能下单！";
			echo json_encode($resArr);
			exit;
		}
		pdo_delete(BEST_CART,array('jlid'=>$jlid,'openid'=>$member['openid']));
		if($memberhd['starttime'] > TIMESTAMP){
			$resArr['error'] = 1;
			$resArr['message'] = "团购还未开始！";
			echo json_encode($resArr);
			exit;
		}
		if($memberhd['endtime'] < TIMESTAMP){
			$resArr['error'] = 1;
			$resArr['message'] = "团购已经结束！";
			echo json_encode($resArr);
			exit;
		}
		$goodsid = $_GPC['goodsid'];
		foreach($goodsid as $k=>$v){
			$total = $_GPC['total'][$k];
			if($total > 0){
				$goodsres = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERGOODS)." WHERE weid = {$_W['uniacid']} AND id = {$v} AND hdid = {$jlid}");
				if(empty($goodsres)){
					$resArr['error'] = 1;
					$resArr['message'] = "有商品不属于该团购！";
					echo json_encode($resArr);
					exit;
				}
				
				if($total > $goodsres['total']){
					$resArr['error'] = 1;
					$resArr['message'] = "商品".$goodsres['title']."库存不足！";
					echo json_encode($resArr);
					exit;
				}
				
				$hasbuytotal = pdo_fetchcolumn("SELECT SUM(a.total) FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.jlid = {$jlid} AND a.goodsid = {$v} AND b.status >= 0 AND a.orderid = b.id AND b.from_user = '{$member['openid']}'");
				$latertotal = $hasbuytotal+$total;
				if($goodsres['xiangounum'] > 0 && $latertotal > $goodsres['xiangounum']){
					$resArr['error'] = 1;
					$resArr['message'] = "商品".$goodsres['title']."每人限购".$goodsres['xiangounum'].$goodsres['optionname']."！您已购买了".$hasbuytotal.$goodsres['optionname'];
					echo json_encode($resArr);
					exit;
				}
			}
		}
		foreach($goodsid as $k=>$v){
			$total = $_GPC['total'][$k];
			if($total > 0){
				$goodsres = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERGOODS)." WHERE weid = {$_W['uniacid']} AND id = {$v} AND hdid = {$jlid}");
				$hasjieti = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERGOODSJIETI)." WHERE goodsid = {$v} AND jietinumstart <= {$goodsres['inpeople']} AND jietinum >= {$goodsres['inpeople']}");
				if(!empty($hasjieti)){
					$data['price'] = $hasjieti['jietiprice'];
				}else{
					$data['price'] = $goodsres['normalprice'];
				}
				$data['weid'] = $_W['uniacid'];
				$data['openid'] = $member['openid'];
				$data['jlid'] = $jlid;
				$data['goodsid'] = $v;
				$data['goodsname'] = $goodsres['title'];
				$data['total'] = $total;
				$data['allprice'] = $data['total']*$data['price'];
				pdo_insert(BEST_CART,$data);
			}
		}
		$resArr['error'] = 0;
		$resArr['message'] = "提交成功！";
		echo json_encode($resArr);
		exit;
	}
	
	public function doMobileMemberordersub() {
		global $_W,$_GPC;
		$member = $this->Mcheckmember();
		$jlid = intval($_GPC['jlid']);
		$list = pdo_fetchall("SELECT * FROM ".tablename(BEST_CART)." WHERE weid = {$_W['uniacid']} AND jlid = {$jlid} AND openid = '{$member['openid']}'");
		if(empty($list)){
			header("Location:".$this->createMobileUrl('my'));
		}
		$allprice = pdo_fetchcolumn("SELECT SUM(allprice) FROM ".tablename(BEST_CART)." WHERE weid = {$_W['uniacid']} AND jlid = {$jlid} AND openid = '{$member['openid']}'");
		$jielong = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERHUODONG)." WHERE id = {$jlid}");
		if($allprice >= $jielong['manjian'] && $jielong['manjian'] > 0){
			$yunfei = 0;
		}else{
			$yunfei = $jielong['yunfei'];
		}
		if($jielong['cansh'] == 0){
			$yunfei = 0;
		}
		$allprice2 = $allprice+$yunfei;
		$ztdlist = pdo_fetchall("SELECT * FROM ".tablename(BEST_ZITIDIAN)." WHERE openid = '{$jielong['openid']}' AND ztdtype = 0");
		include $this->template('memberordersub');
	}
	public function doMobileDomemberordersub() {
		global $_W,$_GPC;
		$member = $this->Mcheckmember();
		$jlid = intval($_GPC['jlid']);
		$memberjl = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERHUODONG)." WHERE id = {$jlid}");
		$list = pdo_fetchall("SELECT * FROM ".tablename(BEST_CART)." WHERE weid = {$_W['uniacid']} AND jlid = {$jlid} AND openid = '{$member['openid']}'");
		if(empty($list)){
			$resArr['error'] = 1;
			$resArr['message'] = "没有商品可以结算！";
			echo json_encode($resArr);
			exit;
		}
		$allprice = pdo_fetchcolumn("SELECT SUM(allprice) FROM ".tablename(BEST_CART)." WHERE weid = {$_W['uniacid']} AND jlid = {$jlid} AND openid = '{$member['openid']}'");
		if($allprice >= $memberjl['manjian'] && $memberjl['manjian'] > 0){
			$yunfei = 0;
		}else{
			$yunfei = $memberjl['yunfei'];
		}
		$pstype = intval($_GPC['pstype']);
		if($pstype == 1){
			$yunfei = 0;
			$shname = $shcity = $shaddress = '';
			$shphone = trim($_GPC['shphone2']);
			if(!$this->isMobile($shphone)){
				$resArr['error'] = 1;
				$resArr['message'] = "请填写正确的手机号码！";
				echo json_encode($resArr);
				exit;
			}
			$ztdid = intval($_GPC['ztdid']);
			$ztdres = pdo_fetch("SELECT * FROM ".tablename(BEST_ZITIDIAN)." WHERE id = {$ztdid}");
			if(empty($ztdres)){
				$resArr['error'] = 1;
				$resArr['message'] = "请选择自提点！";
				echo json_encode($resArr);
				exit;
			}
			$data['remark'] = $_GPC['remark2'];
		}else{
			$shname = trim($_GPC['shname']);
			$shphone = trim($_GPC['shphone']);
			$shcity = trim($_GPC['shcity']);
			$shaddress = trim($_GPC['shaddress']);
			if(empty($shname)){
				$resArr['error'] = 1;
				$resArr['message'] = "请填写收货人姓名！";
				echo json_encode($resArr);
				exit;
			}
			if(!$this->isMobile($shphone)){
				$resArr['error'] = 1;
				$resArr['message'] = "请填写正确的收货人手机号码！";
				echo json_encode($resArr);
				exit;
			}
			if(empty($shcity)){
				$resArr['error'] = 1;
				$resArr['message'] = "请选择省市！";
				echo json_encode($resArr);
				exit;
			}
			if(empty($shaddress)){
				$resArr['error'] = 1;
				$resArr['message'] = "请填写详细地址！";
				echo json_encode($resArr);
				exit;
			}
			$data['remark'] = $_GPC['remark'];
		}
		$allprice2 = $allprice+$yunfei;
		if($pstype == 0){
			$data['address'] = $shname."|".$shphone."|".$shcity."|".$shaddress;
			$datam['shname'] = $shname;
			$datam['shphone'] = $shphone;
			$datam['shcity'] = $shcity;
			$datam['shaddress'] = $shaddress;
			pdo_update(BEST_MEMBER,$datam,array('openid'=>$member['openid']));
		}else{
			$data['address'] = $shphone;
			$data['ztdid'] = $ztdid;
			$data['ztdaddress'] = $ztdres['address'];
			$data['ztdjingdu'] = $ztdres['jingdu'];
			$data['ztdweidu'] = $ztdres['weidu'];
		}
		$data['price'] = $allprice2;
		$data['yunfei'] = $yunfei;
		$data['weid'] = $_W['uniacid'];
		$data['from_user'] = $member['openid'];
		$data['ordersn'] = date('Ymd').random(14,1);
		$data['createtime'] = TIMESTAMP;
		$data['jlid'] = $jlid;
		$data['isjl'] = 1;
		$data['jlopenid'] = $memberjl['openid'];
		$data['pstype'] = $pstype;
		/*if(isset($this->module['config']['zftkhour'])){
			$data['cantktime'] = $memberjl['endtime'] - ($this->module['config']['zftkhour'])*3600;
		}*/
		pdo_insert(BEST_ORDER, $data);
		$orderid = pdo_insertid();
		foreach($list as $k=>$v){
			$datao['weid'] = $_W['uniacid'];
			$datao['total'] = $v['total'];
			$datao['price'] = $v['price'];
			$datao['goodsid'] = $v['goodsid'];
			$datao['createtime'] = TIMESTAMP;
			$datao['orderid'] = $orderid;
			$datao['goodsname'] = $v['goodsname'];
			$datao['jlid'] = $jlid;
			pdo_insert(BEST_ORDERGOODS,$datao);
		}
		pdo_delete(BEST_CART,array('jlid'=>$jlid,'openid'=>$member['openid']));
		$resArr['error'] = 0;
		$resArr['fee'] = $allprice2;
		$resArr['ordertid'] = $data['ordersn'];
		$resArr['message'] = "提交订单成功！";
		echo json_encode($resArr);
		exit;
	}
	
	public function doMobileMy() {
		global $_W,$_GPC;
		$member = $this->Mcheckmember();
		$allmoney = pdo_fetchcolumn("SELECT SUM(money) as allmoney FROM ".tablename(BEST_MEMBERACCOUNT)." WHERE openid = '{$member['openid']}' AND weid = {$_W['uniacid']}");
		$allmoney = empty($allmoney) ? "0.00" : $allmoney;
		include $this->template('my');
	}
	
	public function doMobileMysq() {
		global $_W,$_GPC;
		$xqid = intval($_GPC['xqid']);
		$xqmsg = $this->getxqmsg($xqid);
		$hdres = $xqmsg['hdres'];
		
		$member = $this->Mcheckmember();
		$isagent = pdo_fetch("SELECT ID FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} AND openid = '{$member['openid']}' AND status = 1");
		$member['isagent'] = empty($isagent) ? 0 : 1;
		$cartnum = pdo_fetchcolumn("SELECT SUM(total) FROM ".tablename(BEST_CART)." WHERE xqid = {$xqid} AND hdid = {$hdres['id']} AND openid = '{$member['openid']}'");
		
		$ordernum1 = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_ORDER)." WHERE status = 0 AND isjl = 0 AND from_user = '{$member['openid']}'");
		$ordernum1 = empty($ordernum1) ? 0 : $ordernum1;
		
		$ordernum2 = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_ORDER)." WHERE status = 1 AND pstype = 0 AND isjl = 0 AND from_user = '{$member['openid']}'");
		$ordernum2 = empty($ordernum2) ? 0 : $ordernum2;
		
		$ordernum3 = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_ORDER)." WHERE status = 2 AND isjl = 0 AND from_user = '{$member['openid']}'");
		$ordernum3 = empty($ordernum3) ? 0 : $ordernum3;
		
		$ordernum4 = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_ORDER)." WHERE status = 1 AND pstype = 1 AND isjl = 0 AND from_user = '{$member['openid']}'");
		$ordernum4 = empty($ordernum4) ? 0 : $ordernum4;
		include $this->template('mysq');
	}
	
	public function doMobileSqcontcat() {
		global $_W,$_GPC;
		$xqid = intval($_GPC['xqid']);
		$xiaoqu = pdo_fetch("SELECT * FROM ".tablename(BEST_XIAOQU)." WHERE weid = {$_W['uniacid']} AND id = {$xqid}");
		include $this->template('sqcontcat');
	}
	
	//检证身份证是否正确	
	public function isCard($id_card){
		if(strlen($id_card)==18){
			return $this->idcard_checksum18($id_card);
		}elseif((strlen($id_card)==15)){
			$id_card=$this->idcard_15to18($id_card);
			return $this->idcard_checksum18($id_card);
		}else{
			return false;
		}
	}
	// 计算身份证校验码，根据国家标准GB 11643-1999
	public function idcard_verify_number($idcard_base){
		if(strlen($idcard_base)!=17){
			return false;
		}
		//加权因子
		$factor=array(7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2);
		//校验码对应值
		$verify_number_list=array('1','0','X','9','8','7','6','5','4','3','2');
		$checksum=0;
		for($i=0;$i<strlen($idcard_base);$i++){
			$checksum += substr($idcard_base,$i,1) * $factor[$i];
		}
		$mod=$checksum % 11;
		$verify_number=$verify_number_list[$mod];
		return $verify_number;
	}
	// 将15位身份证升级到18位
	public function idcard_15to18($idcard){
		if(strlen($idcard)!=15){
			return false;
		}else{
			// 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
			if(array_search(substr($idcard,12,3),array('996','997','998','999')) !== false){
				$idcard=substr($idcard,0,6).'18'.substr($idcard,6,9);
			}else{
				$idcard=substr($idcard,0,6).'19'.substr($idcard,6,9);
			}
		}
		$idcard=$this->idcard_verify_number($idcard);
		return $idcard;
	}
	// 18位身份证校验码有效性检查
	public function idcard_checksum18($idcard){
		if(strlen($idcard)!=18){
			return false;
		}
		$idcard_base=substr($idcard,0,17);
		if($this->idcard_verify_number($idcard_base)!=strtoupper(substr($idcard,17,1))){
			return false;
		}else{
			return true;
		}
	}

	public function doMobileRenzheng() {
		$member = $this->Mcheckmember();
		if($member['rztype'] > 0){
			$message = "您已认证通过！";
			include $this->template('error');
			exit;
		}
		include $this->template('renzheng');
	}
	
	public function doMobileGerenrz() {
		global $_W,$_GPC;
		$member = $this->Mcheckmember();
		if($member['rztype'] > 0){
			$message = "您已认证通过！";
			include $this->template('error');
			exit;
		}
		$memberrz = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERRZ)." WHERE openid = '{$member['openid']}'");
		if($memberrz['rztype'] == 2){
			header("Location:".$this->createMobileUrl('qiyerz'));
		}
		include $this->template('gerenrz');
	}
	
	public function doMobileDogerenrz() {
		global $_W,$_GPC;
		if(!checksubmit('submit')){
			exit;
		}
		$member = $this->Mcheckmember();
		if(empty($_GPC['rzrealname'])){
			$resArr['error'] = 1;
			$resArr['message'] = "请输入姓名！";
			echo json_encode($resArr);
			exit;
		}
		$isidcard = $this->isCard($_GPC['rzidcard']);
		if(empty($isidcard)){
			$resArr['error'] = 1;
			$resArr['message'] = "请输入正确的身份证号！";
			echo json_encode($resArr);
			exit;
		}
		if(empty($_GPC['idcard1'])){
			$resArr['error'] = 1;
			$resArr['message'] = "请上传身份证正面照！";
			echo json_encode($resArr);
			exit;
		}
		if(empty($_GPC['idcard2'])){
			$resArr['error'] = 1;
			$resArr['message'] = "请上传身份证反面照！";
			echo json_encode($resArr);
			exit;
		}
		if(empty($_GPC['rztelphone'])){
			$resArr['error'] = 1;
			$resArr['message'] = "请输入手机号！";
			echo json_encode($resArr);
			exit;
		}
		$memberrz = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERRZ)." WHERE openid = '{$member['openid']}'");
		if(!empty($memberrz)){
			$data = array(
				'rzrealname'=>$_GPC['rzrealname'],
				'rzidcard'=>$_GPC['rzidcard'],
				'idcard1'=>$_GPC['idcard1'],
				'idcard2'=>$_GPC['idcard2'],
				'rztelphone'=>$_GPC['rztelphone'],
				'isjujue'=>0,
			);
			pdo_update(BEST_MEMBERRZ,$data,array('openid'=>$member['openid']));
			$resArr['rzprice'] = 0;
			$resArr['error'] = 0;
			$resArr['message'] = "重新提交资料成功！";
			echo json_encode($resArr);
			exit;
		}else{
			if($this->module['config']['gerenfee'] <= 0){
				$rzstatus = 1;
			}else{
				$rzstatus = 0;
			}
			$data = array(
				'weid'=>$_W['uniacid'],
				'openid'=>$member['openid'],
				'rzrealname'=>$_GPC['rzrealname'],
				'rzidcard'=>$_GPC['rzidcard'],
				'idcard1'=>$_GPC['idcard1'],
				'idcard2'=>$_GPC['idcard2'],
				'rztelphone'=>$_GPC['rztelphone'],
				'rztype'=>1,
				'rzordersn'=>date('Ymd').random(12,1),
				'rztime'=>TIMESTAMP,
				'rzprice'=>$this->module['config']['gerenfee'],
				'rzstatus'=>$rzstatus,
			);
			pdo_insert(BEST_MEMBERRZ,$data);
			$resArr['rzprice'] = $this->module['config']['gerenfee'];
			$resArr['error'] = 0;
			$resArr['message'] = "申请个人认证成功！";
			echo json_encode($resArr);
			exit;
		}
	}
	
	public function doMobileQiyerz() {
		global $_W,$_GPC;
		$member = $this->Mcheckmember();
		if($member['rztype'] > 0){
			$message = "您已认证通过！";
			include $this->template('error');
			exit;
		}
		$memberrz = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERRZ)." WHERE openid = '{$member['openid']}'");
		if($memberrz['rztype'] == 1){
			header("Location:".$this->createMobileUrl('gerenrz'));
		}
		include $this->template('qiyerz');
	}
	
	public function doMobileDoqiyerz() {
		global $_W,$_GPC;
		if(!checksubmit('submit')){
			exit;
		}
		$member = $this->Mcheckmember();
		if(empty($_GPC['rzqiyename'])){
			$resArr['error'] = 1;
			$resArr['message'] = "请输入企业名称！";
			echo json_encode($resArr);
			exit;
		}
		if(empty($_GPC['rzsanzheng'])){
			$resArr['error'] = 1;
			$resArr['message'] = "请上传三合一营业许可证！";
			echo json_encode($resArr);
			exit;
		}
		if(empty($_GPC['rzrealname'])){
			$resArr['error'] = 1;
			$resArr['message'] = "请输入法人姓名！";
			echo json_encode($resArr);
			exit;
		}
		$isidcard = $this->isCard($_GPC['rzidcard']);
		if(empty($isidcard)){
			$resArr['error'] = 1;
			$resArr['message'] = "请输入正确的身份证号！";
			echo json_encode($resArr);
			exit;
		}
		if(empty($_GPC['idcard1'])){
			$resArr['error'] = 1;
			$resArr['message'] = "请上传身份证正面照！";
			echo json_encode($resArr);
			exit;
		}
		if(empty($_GPC['idcard2'])){
			$resArr['error'] = 1;
			$resArr['message'] = "请上传身份证反面照！";
			echo json_encode($resArr);
			exit;
		}
		if(empty($_GPC['rztelphone'])){
			$resArr['error'] = 1;
			$resArr['message'] = "请输入手机号！";
			echo json_encode($resArr);
			exit;
		}
		$memberrz = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERRZ)." WHERE openid = '{$member['openid']}'");
		if(!empty($memberrz)){
			$data = array(
				'rzqiyename'=>$_GPC['rzqiyename'],
				'rzsanzheng'=>$_GPC['rzsanzheng'],
				'rzrealname'=>$_GPC['rzrealname'],
				'rzidcard'=>$_GPC['rzidcard'],
				'idcard1'=>$_GPC['idcard1'],
				'idcard2'=>$_GPC['idcard2'],
				'rztelphone'=>$_GPC['rztelphone'],
				'isjujue'=>0,
			);
			pdo_update(BEST_MEMBERRZ,$data,array('openid'=>$member['openid']));
			$resArr['rzprice'] = 0;
			$resArr['error'] = 0;
			$resArr['message'] = "重新提交资料成功！";
			echo json_encode($resArr);
			exit;
		}else{
			if($this->module['config']['qiyefee'] <= 0){
				$rzstatus = 1;
			}else{
				$rzstatus = 0;
			}
			$data = array(
				'weid'=>$_W['uniacid'],
				'openid'=>$member['openid'],
				'rzqiyename'=>$_GPC['rzqiyename'],
				'rzsanzheng'=>$_GPC['rzsanzheng'],
				'rzrealname'=>$_GPC['rzrealname'],
				'rzidcard'=>$_GPC['rzidcard'],
				'idcard1'=>$_GPC['idcard1'],
				'idcard2'=>$_GPC['idcard2'],
				'rztelphone'=>$_GPC['rztelphone'],
				'rztype'=>2,
				'rzordersn'=>date('Ymd').random(12,1),
				'rztime'=>TIMESTAMP,
				'rzprice'=>$this->module['config']['qiyefee'],
				'rzstatus'=>$rzstatus,
			);
			pdo_insert(BEST_MEMBERRZ,$data);
			$resArr['rzprice'] = $this->module['config']['qiyefee'];
			$resArr['error'] = 0;
			$resArr['message'] = "申请企业认证成功！";
			echo json_encode($resArr);
			exit;
		}
	}
	
	public function doMobileRenzhengpay() {
		global $_W,$_GPC;
		$member = $this->Mcheckmember();
		$memberrz = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERRZ)." WHERE openid = '{$member['openid']}'");
		if($memberrz['rzstatus'] == 1){
			$message = "您已认证通过！";
			include $this->template('error');
			exit;
		}
		include $this->template('renzhengpay');
	}
	
	public function doMobileSqtzpay() {
		global $_W,$_GPC;
		$member = $this->Mcheckmember();
		$tzorder = pdo_fetch("SELECT * FROM ".tablename(BEST_TZORDER)." WHERE openid = '{$member['openid']}'");
		if($tzorder['status'] == 1){
			$message = "您已付款成功！";
			include $this->template('error');
			exit;
		}
		include $this->template('sqtzpay');
	}
	
	public function doMobileSqtzpay2() {
		global $_W,$_GPC;
		$member = $this->Mcheckmember();
		$tzorder = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE openid = '{$member['openid']}' AND weid = {$_W['uniacid']}");
		if($tzorder['status'] == 1){
			$message = "您已付款成功！";
			include $this->template('error');
			exit;
		}
		include $this->template('sqtzpay2');
	}
	
	public function doMobileMyinjl() {
		include_once ROOT_PATH.'inc/mobile/myinjl.php';
	}
	
	public function doMobileMyfbjl() {
		include_once ROOT_PATH.'inc/mobile/myfbjl.php';
	}
	
	public function jlorderrefund($jlid){
		global $_W,$_GPC;
		$orders = pdo_fetchall("SELECT a.* FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.jlid = {$jlid} AND b.status >= 1 AND b.status <= 4 AND a.orderid = b.id GROUP BY a.goodsid");
		foreach($orders as $k=>$v){
			$hasjt = pdo_fetch("SELECT id FROM ".tablename(BEST_MEMBERGOODSJIETI)." WHERE goodsid = {$v['goodsid']}");
			if(!empty($hasjt)){
				$lastorder = pdo_fetch("SELECT a.price FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.jlid = {$jlid} AND b.status >= 1 AND b.status <= 4 AND a.orderid = b.id AND a.goodsid = {$v['goodsid']} ORDER BY a.price ASC");
				if(!empty($lastorder)){
					$zuidiprice = $lastorder['price'];
					$allorder = pdo_fetchall("SELECT a.id,a.price,a.total,a.orderid,b.price as oprice,b.from_user,b.status,b.jlopenid FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.jlid = {$v['jlid']} AND b.status >= 1 AND b.status <= 4 AND a.orderid = b.id AND a.goodsid = {$v['goodsid']}");
					foreach($allorder as $kk=>$vv){
						if($vv['price'] > $zuidiprice){
							$data = array();
							$tuiprice = ($vv['price']-$zuidiprice)*$vv['total'];
							$data['jltuiprice'] = $tuiprice;
							$data['price'] = $vv["oprice"]-$tuiprice;
							pdo_update(BEST_ORDER,$data,array('id'=>$vv['orderid']));
							pdo_update(BEST_ORDERGOODS,array('price'=>$zuidiprice),array('id'=>$vv['id']));
							
							$datamoney = array();
							$datamoney['weid'] = $_W['uniacid'];
							$datamoney['openid'] = $vv['from_user'];
							$datamoney['money'] = $data['jltuiprice'];
							$datamoney['time'] = TIMESTAMP;
							$datamoney['orderid'] = $vv['orderid'];
							$datamoney['explain'] = "团购订单退差价";
							$datamoney['candotime'] = TIMESTAMP;
							pdo_insert(BEST_MEMBERACCOUNT,$datamoney);
							
							//扣除商家
							if($vv['status'] == 4){
								$datamoney2 = array();
								$datamoney2['weid'] = $_W['uniacid'];
								$datamoney2['openid'] = $vv['jlopenid'];
								$datamoney2['money'] = -$data['jltuiprice'];
								$datamoney2['time'] = TIMESTAMP;
								$datamoney2['orderid'] = $vv['orderid'];
								$datamoney2['explain'] = "团购订单退差价";
								$datamoney2['candotime'] = TIMESTAMP;
								pdo_insert(BEST_MEMBERACCOUNT,$datamoney2);
							}
						}
					}
				}
			}
		}
	}
	
	public function doMobileAccount() {
		include_once ROOT_PATH.'inc/mobile/account.php';
	}
	
	public function doMobileProfile() {
		include_once ROOT_PATH.'inc/mobile/profile.php';
	}
	
	public function doMobileMyyunfei() {
		include_once ROOT_PATH.'inc/mobile/myyunfei.php';
	}
	
	public function doMobileZitidian() {
		include_once ROOT_PATH.'inc/mobile/zitidian.php';
	}
	
	public function doMobileHexiaoyuan() {
		include_once ROOT_PATH.'inc/mobile/hexiaoyuan.php';
	}
	
	public function doMobileFabu() {
		global $_W,$_GPC;
		$member = $this->Mcheckmember();
		
		$mygoodsku = pdo_fetchall("SELECT * FROM ".tablename(BEST_MEMBERGOODSKU)." WHERE weid = {$_W['uniacid']} AND openid = '{$member['openid']}'");
		foreach($mygoodsku as $k=>$v){
			$thumbs = unserialize($v['thumbs']);
			$mygoodsku[$k]['thumb'] = tomedia($thumbs[0]);
		}
		$zitidian = pdo_fetch("SELECT id FROM ".tablename(BEST_ZITIDIAN)." WHERE openid = '{$member['openid']}'");
		include $this->template('fabu');
	}
	
	public function doMobileDofabu() {
		global $_W,$_GPC;
		if(!checksubmit('submit')){
			exit;
		}
		$member = $this->Mcheckmember();
		$datahd['weid'] = $_W['uniacid'];
		$datahd['time'] = TIMESTAMP;
		$datahd['openid'] = $member['openid'];
		$datahd['nickname'] = $member['nickname'];
		$datahd['avatar'] = $member['avatar'];
		$datahd['title'] = $_GPC['title'];
		$datahd['canziti'] = intval($_GPC['canziti']);
		$datahd['cansh'] = intval($_GPC['cansh']);
		if(empty($datahd['title'])){
			$resArr['error'] = 1;
			$resArr['message'] = "请填写团购名称！";
			echo json_encode($resArr);
			exit;
		}
		$datahd['des'] = $_GPC['des'];
		if(empty($datahd['des'])){
			$resArr['error'] = 1;
			$resArr['message'] = "请填写团购介绍！";
			echo json_encode($resArr);
			exit;
		}
		if(empty($_GPC['thumbs'])){
			$resArr['error'] = 1;
			$resArr['message'] = "请上传团购图片介绍！";
			echo json_encode($resArr);
			exit;
		}
		$datahd['thumbs'] = serialize($_GPC['thumbs']);
		$datahd['starttime'] = strtotime($_GPC['starttime']);
		$datahd['endtime'] = strtotime($_GPC['endtime']);
		$datahd['yunfei'] = $_GPC['yunfei'];
		$datahd['manjian'] = $_GPC['manjian'];
		if(empty($datahd['starttime']) || empty($datahd['endtime'])){
			$resArr['error'] = 1;
			$resArr['message'] = "请选择团购开始时间和截止时间！";
			echo json_encode($resArr);
			exit;
		}
		if($datahd['starttime'] > $datahd['endtime']){
			$resArr['error'] = 1;
			$resArr['message'] = "团购开始时间不能大于截止时间！";
			echo json_encode($resArr);
			exit;
		}
		if($datahd['canziti'] == 0 && $datahd['cansh'] == 0){
			$resArr['error'] = 1;
			$resArr['message'] = "自提与送货必须选择至少选择其中一项！";
			echo json_encode($resArr);
			exit;
		}
		if($datahd['canziti'] == 1){
			$zitidian = pdo_fetch("SELECT id FROM ".tablename(BEST_ZITIDIAN)." WHERE openid = '{$member['openid']}'");
			if(empty($zitidian)){
				$resArr['error'] = 1;
				$resArr['message'] = "您还没有创建自提点，不能选择支持自提。请至个人中心添加自提点！";
				echo json_encode($resArr);
				exit;
			}
		}
		$goodsname = $_GPC['goodsname'];
		if(empty($goodsname)){
			$resArr['error'] = 1;
			$resArr['message'] = "请上传商品！";
			echo json_encode($resArr);
			exit;
		}
		
		foreach($goodsname as $k=>$v){
			$data['title'] = $v;
			if(empty($data['title'])){
				$resArr['error'] = 1;
				$resArr['message'] = "请填写商品名称！";
				echo json_encode($resArr);
				exit;
			}
			$data['normalprice'] = $_GPC['normalprice'][$k];
			if(empty($data['normalprice'])){
				$resArr['error'] = 1;
				$resArr['message'] = "请填写商品价格！";
				echo json_encode($resArr);
				exit;
			}
			$data['total'] = $_GPC['total'][$k];
			if(empty($data['total'])){
				$resArr['error'] = 1;
				$resArr['message'] = "请填写商品库存！";
				echo json_encode($resArr);
				exit;
			}
			$goodsthumbskey = 'goodsthumbs'.$k;
			if(empty($_GPC[$goodsthumbskey])){
				$resArr['error'] = 1;
				$resArr['message'] = "请上传商品图片！";
				echo json_encode($resArr);
				exit;
			}			
			$jietipricekey = 'jietiprice'.$k;
			$jietinumstartkey = 'jietinumstart'.$k;
			$jietinumkey = 'jietinum'.$k;
			$lastshuliang = 0;
			$lastprice = 0;
			foreach($_GPC[$jietipricekey] as $kk=>$vv){
				$datajt['jietiprice'] = $vv;
				$datajt['jietinumstart'] = $_GPC[$jietinumstartkey][$kk];
				$datajt['jietinum'] = $_GPC[$jietinumkey][$kk];
				if(empty($datajt['jietiprice']) || empty($datajt['jietinum'])){
					$resArr['error'] = 1;
					$resArr['message'] = "请填写完整的阶梯价格和阶梯数量！";
					echo json_encode($resArr);
					exit;
				}
				if($_GPC[$jietinumstartkey][0] != 0){
					$resArr['error'] = 1;
					$resArr['message'] = "第一个阶梯起始数量应该为0！";
					echo json_encode($resArr);
					exit;
				}
				if($_GPC[$jietinumstartkey][$kk] <= $lastshuliang){
					if($lastshuliang != 0){
						$resArr['error'] = 1;
						$resArr['message'] = "阶梯起始数量不能小于上一个阶梯的结束数量！";
						echo json_encode($resArr);
						exit;
					}
				}
				if($vv >= $lastprice){
					if($lastshuliang != 0){
						$resArr['error'] = 1;
						$resArr['message'] = "阶梯价格不能大于等于上一个阶梯的价格！";
						echo json_encode($resArr);
						exit;
					}
				}
				$lastshuliang = $_GPC[$jietinumkey][$kk];
				$lastprice = $vv;
			}
		}
		if($this->module['config']['iszfjlsh'] == 0){
			$datahd['status'] = 1;
		}
		$datahd['address'] = $_GPC['address'];
		$datahd['jingdu'] = $_GPC['jingdu'];
		$datahd['weidu'] = $_GPC['weidu'];
		$datahd['xingyun'] = $_GPC['xingyun'];
		pdo_insert(BEST_MEMBERHUODONG,$datahd);
		$hdid = pdo_insertid();
		foreach($goodsname as $k=>$v){
			$data['weid'] = $_W['uniacid'];
			$data['openid'] = $member['openid'];
			$data['title'] = $v;
			$data['normalprice'] = $_GPC['normalprice'][$k];
			$data['total'] = $_GPC['total'][$k];
			$data['optionname'] = $_GPC['optionname'][$k];
			$data['xiangounum'] = $_GPC['xiangounum'][$k];
			$data['createtime'] = TIMESTAMP;
			$data['hdid'] = $hdid;
			$goodsthumbskey = 'goodsthumbs'.$k;
			$data['thumbs'] = serialize($_GPC[$goodsthumbskey]);
			pdo_insert(BEST_MEMBERGOODS,$data);
			$goodsid = pdo_insertid();
			
			$jietipricekey = 'jietiprice'.$k;
			$jietinumstartkey = 'jietinumstart'.$k;
			$jietinumkey = 'jietinum'.$k;
			foreach($_GPC[$jietipricekey] as $kk=>$vv){
				$datajt['goodsid'] = $goodsid;
				$datajt['jietiprice'] = $vv;
				$datajt['jietinumstart'] = $_GPC[$jietinumstartkey][$kk];
				$datajt['jietinum'] = $_GPC[$jietinumkey][$kk];
				$datajt['displayorder'] = $kk;
				pdo_insert(BEST_MEMBERGOODSJIETI,$datajt);
			}
		}
		$resArr['error'] = 0;
		$resArr['message'] = "添加成功！";
		echo json_encode($resArr);
		exit;
	}
	
	public function doMobileMygoodsku() {
		include_once ROOT_PATH.'inc/mobile/mygoodsku.php';
	}
	
	public function doMobileGoodskud() {
		global $_W, $_GPC;
		$id = intval($_GPC['id']);
		$goodsnow = intval($_GPC['goodsnow']);
		$goods = pdo_fetch("SELECT * FROM " . tablename(BEST_MEMBERGOODSKU) . " WHERE weid = {$_W['uniacid']} AND id = {$id}");
		$thumbs = unserialize($goods['thumbs']);
		if(!empty($thumbs)){
			$imghtml = '';
			foreach($thumbs as $k=>$v){
				$imghtml .= '<div class="items text-c">
								<img src="'.tomedia($v).'" />
								<div class="delimg">删除</div>
								<input type="hidden" name="goodsthumbs'.$goodsnow.'[]" value="'.$v.'" />
							</div>';
			}
		}
		$resArr['error'] = 0;
		$resArr['goods'] = $goods;
		$resArr['thumbs'] = $imghtml;
		echo json_encode($resArr);
		exit;
	}
	
	public function doMobileMerchant() {
		include_once ROOT_PATH.'inc/mobile/merchantcenter.php';
	}
	
	public function doMobileMerchantteam() {
		include_once ROOT_PATH.'inc/mobile/merchantteam.php';
	}
	
	public function doMobileSqtz() {
		include_once ROOT_PATH.'inc/mobile/sqtz.php';
	}
	
	public function doMobileMerchantorder() {
		include_once ROOT_PATH.'inc/mobile/merchantorder.php';
	}
	
	public function doMobileMerchantsaomahxy() {
		global $_W, $_GPC;
		$member = $this->Mcheckmember();
		$merchant_id = intval($_GPC['merchant_id']);
		if (empty($merchant_id)) {
			$message = '参数错误！';
			include $this->template('error');
			exit;
		}
		$has = pdo_fetch("SELECT id FROM ".tablename(BEST_HEXIAOYUAN)." WHERE merchant_id = {$merchant_id} AND openid = '{$member['openid']}'");
		if (!empty($has)) {
			$message = '你已绑定成为核销员！';
			include $this->template('error');
			exit;
		}
		$data = array(			
			'weid' => intval($_W['uniacid']),
			'name' => $member['nickname'],
			'openid' => $member['openid'],
			'merchant_id'=>$merchant_id,
		);
		pdo_insert(BEST_HEXIAOYUAN, $data);
		$message = '绑定成为核销员成功！';
		include $this->template('error');
		exit;
	}
	
	
	public function doMobileSaomahxy() {
		global $_W, $_GPC;
		$member = $this->Mcheckmember();
		$toopenid = trim($_GPC['toopenid']);
		if (empty($toopenid)) {
			$message = '参数错误！';
			include $this->template('error');
			exit;
		}
		$has = pdo_fetch("SELECT id FROM ".tablename(BEST_HEXIAOYUAN)." WHERE fopenid = '{$toopenid}' AND openid = '{$member['openid']}'");
		if (!empty($has)) {
			$message = '你已绑定成为核销员！';
			include $this->template('error');
			exit;
		}
		$data = array(			
			'weid' => intval($_W['uniacid']),
			'name' => $member['nickname'],
			'openid' => $member['openid'],
			'fopenid'=>$toopenid,
		);
		pdo_insert(BEST_HEXIAOYUAN, $data);
		$message = '绑定成为核销员成功！';
		include $this->template('error');
		exit;
	}
	
	public function doMobileJlhexiao() {
		include_once ROOT_PATH.'inc/mobile/jlhexiao.php';
	}
	
	public function doMobileMerchanthexiao() {
		global $_W, $_GPC;
		$member = $this->Mcheckmember();
		$op = trim($_GPC['op']);
		if($op == ''){
			$orderid = intval($_GPC['id']);
			$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND weid = {$_W['uniacid']} AND status = 1 AND ztdid > 0");
			if (empty($orderres)) {
				$message = '抱歉，没有该订单信息或订单已经核销！';
				include $this->template('error');
				exit;
			}
			
			$merchant = pdo_fetch("SELECT id,openid FROM ".tablename(BEST_MERCHANT)." WHERE id = {$orderres['merchant_id']}");
			if($member['openid'] != $merchant['openid']){
				$hexiaoyuan = pdo_fetch("SELECT * FROM ".tablename(BEST_HEXIAOYUAN)." WHERE weid = {$_W['uniacid']} AND openid = '{$member['openid']}' AND merchant_id = {$merchant['id']}");
				if (empty($hexiaoyuan)) {
					$message = '抱歉，你不是核销员！';
					include $this->template('error');
					exit;
				}
			}
			
			$allgoods = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$orderid} AND hexiaonum != total");
			foreach($allgoods as $k=>$v){
				$allgoods[$k]['left'] = $v['total'] - $v['hexiaonum'];
			}
			
			$ordermember = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$orderres['from_user']}'");
			include $this->template('merchanthexiao');
		}
		if($op == 'do'){
			if(!checksubmit('submit')){
				exit;
			}
			$orderid = intval($_GPC['orderid']);
			$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND weid = {$_W['uniacid']} AND status = 1 AND ztdid > 0");
			if (empty($orderres)) {
				$resArr['error'] = 1;
				$resArr['message'] = '抱歉，没有该订单信息！';
				echo json_encode($resArr);
				exit();
			}
			$merchant = pdo_fetch("SELECT id,openid FROM ".tablename(BEST_MERCHANT)." WHERE id = {$orderres['merchant_id']}");
			if($member['openid'] != $merchant['openid']){
				$hexiaoyuan = pdo_fetch("SELECT * FROM ".tablename(BEST_HEXIAOYUAN)." WHERE weid = {$_W['uniacid']} AND openid = '{$member['openid']}' AND merchant_id = {$merchant['id']}");
				if (empty($hexiaoyuan)) {
					$resArr['error'] = 1;
					$resArr['message'] = '抱歉，你不是核销员！';
					echo json_encode($resArr);
					exit();
				}
			}
			
			foreach($_GPC['ordergoodsid'] as $k=>$v){
				$hxnum = intval($_GPC['num'][$k]);
				$goodsres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE id = {$v}");
				if($hxnum > 0){
					if($hxnum > ($goodsres['total']-$goodsres['hexiaonum'])){
						$resArr['error'] = 1;
						$tipmsg = $goodsres['optionname'] == '' ? '['.$goodsres['goodsname'].']的核销数量超过限制！' : '['.$goodsres['goodsname'].'（'.$goodsres['optionname'].'）]的核销数量超过限制！';
						$resArr['message'] = $tipmsg;
						echo json_encode($resArr);
						exit();
					}
				}else{
					$resArr['error'] = 1;
					$tipmsg = $goodsres['optionname'] == '' ? '['.$goodsres['goodsname'].']的核销数量必须大于0！' : '['.$goodsres['goodsname'].'（'.$goodsres['optionname'].'）]的核销数量必须大于0！';
					$resArr['message'] = $tipmsg;
					echo json_encode($resArr);
					exit();
				}
			}

			foreach($_GPC['ordergoodsid'] as $k=>$v){
				$hxnum = intval($_GPC['num'][$k]);
				$goodsres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE id = {$v}");
				if($hxnum > 0){
					$data['hexiaonum'] = $goodsres['hexiaonum']+$hxnum;
					pdo_update(BEST_ORDERGOODS,$data,array('id'=>$v));
				}
			}
			$isallhx = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE weid = {$_W['uniacid']} AND orderid = {$orderid} AND total != hexiaonum");
			if(empty($isallhx)){
				$data2['status'] = 4;
				pdo_update(BEST_ORDER,$data2,array('id'=>$orderid));
				if($orderres['isdmfk'] == 0){
					//利润写进代理商数据库，同时设置可提现时间
					$hasmerchantaccount = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANTACCOUNT)." WHERE merchant_id = {$orderres['merchant_id']} AND orderid = {$orderres['id']}");
					if(empty($hasmerchantaccount)){
						$datamerchant['weid'] = $_W['uniacid'];
						$datamerchant['merchant_id'] = $orderres['merchant_id'];
						$datamerchant['money'] = $orderres['alllirun'];
						$datamerchant['time'] = TIMESTAMP;
						$datamerchant['explain'] = '代理团结算';
						$datamerchant['orderid'] = $orderres['id'];
						$datamerchant['candotime'] = TIMESTAMP + ($this->module['config']['dltxhour'])*3600;
						pdo_insert(BEST_MERCHANTACCOUNT,$datamerchant);
					}
				}
				$resArr['error'] = 0;
				$resArr['message'] = '核销订单成功[全部]！';
				echo json_encode($resArr);
				exit();
			}else{
				$resArr['error'] = 0;
				$resArr['message'] = '核销订单成功[部分]！';
				echo json_encode($resArr);
				exit();
			}		
		}
	}
	
	public function doMobileMerchantaccount() {
		include_once ROOT_PATH.'inc/mobile/merchantaccount.php';
	}
	
	public function doMobileMerchantprofile(){
		include_once ROOT_PATH.'inc/mobile/merchantprofile.php';
	}
	
	public function doMobileMerchantztd(){
		include_once ROOT_PATH.'inc/mobile/merchantztd.php';
	}
	
	public function doMobileMerchanthxy(){
		include_once ROOT_PATH.'inc/mobile/merchanthxy.php';
	}
	
	public function doMobileMerchanttz(){
		include_once ROOT_PATH.'inc/mobile/merchanttz.php';
	}
	
	public function randomkeys($length){
		$info="";
		$pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
		for($i=0;$i<$length;$i++) {
			$info .= $pattern{mt_rand(0,35)};    //生成php随机数
		}
		return $info;
	}
	
	public function doMobileMerchanthd(){
		global $_W,$_GPC;
		$merchant = $this->checkmergentauth();
		if($merchant['xqz'] == 1){
			$condition = "weid = {$_W['uniacid']}";
		}else{
			$condition = "weid = {$_W['uniacid']} AND isxq = 0";
		}
		
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_HUODONG)." WHERE ".$condition);
		$psize = 10;
		$allpage = ceil($total/$psize)+1;
		$page = intval($_GPC["page"]);
		$pindex = max(1, $page);
		$hdlist = pdo_fetchall("SELECT * FROM ".tablename(BEST_HUODONG)." WHERE ".$condition." ORDER BY endtime DESC LIMIT ".($pindex - 1)*$psize.",".$psize);
		
		$status = intval($_GPC['status']);
		$merchanthdlist = array();
		foreach($hdlist as $k=>$v){
			$merchanthd = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANTHD)." WHERE hdid = {$v['id']} AND merchant_id = {$merchant['id']}");
			if(empty($merchanthd)){
				if($status == 0){
					$merchanthdlist[$k] = $v;
				}
			}else{
				if($status == 1){
					$merchanthdlist[$k] = $v;
					$merchanthdlist[$k]['merchanthdid'] = $merchanthd['id'];
				}
			}
		}
		
		$isajax = intval($_GPC['isajax']);
		if($isajax == 1){
			$html = '';
			foreach($merchanthdlist as $k=>$v){	
				if($status == 0){
					if($v['endtime'] > TIMESTAMP){
						$dohtml = '<div class="right">
										<a href="javascript:;" class="cjhd" data-id="'.$v['id'].'">参加</a>
									</div>';
					}else{
						$dohtml = '<div class="right iconfont" style="color:#999;font-size:1.3rem;line-height:1.3rem;">&#xe6a3;</div>';
					}
				}else{
					$dohtml = '<div class="right">
									<a href="'.$this->createMobileUrl('myhddetail',array('id'=>$v['merchanthdid'])).'">查看</a>
								</div>';
					if($v['endtime'] < TIMESTAMP){
						$dohtml .= '<div class="iconfont" style="color:#999;font-size:1.3rem;line-height:1.3rem;position:absolute;width:1.3rem;height:1.3rem;right:1.5rem;top:0.2rem;">&#xe6a3;</div>';
					}
				}
				$titlehtml = $v['isxq'] == 1 ? '<span>[社区团]</span>' : $v['title'];
				$html .= '<div class="item flex">
							<div class="left">
								<div class="title textellipsis2">'.$titlehtml.'</div>
								<div class="time">
									<div>开始时间：'.date("Y-m-d H:i:s",$v['starttime']).'</div>
									<div>结束时间：'.date("Y-m-d H:i:s",$v['endtime']).'</div>
								</div>
							</div>
							'.$dohtml.'
						</div>';
			}
			echo $html;
			exit;
		}else{
			include $this->template('merchanthd');
		}
	}
	
	public function doMobileDohd(){
		global $_W,$_GPC;
		$merchant = $this->checkmergentauth();
		if(!checksubmit('submit')){
			$resArr['error'] = 1;
			$resArr['message'] = "请不要重复提交！";
			echo json_encode($resArr);
			exit;
		}
		$hdid = intval($_GPC['hdid']);
		$hdres = pdo_fetch("SELECT * FROM ".tablename(BEST_HUODONG)." WHERE weid = {$_W['uniacid']} AND id = {$hdid}");
		if(empty($hdres)){
			$resArr['error'] = 1;
			$resArr['message'] = "不存在该活动！";
			echo json_encode($resArr);
			exit;
		}
		if($merchant['id'] == 0){
			$resArr['error'] = 1;
			$resArr['message'] = "你不能参与这个活动！";
			echo json_encode($resArr);
			exit;
		}
		if($hdres['endtime'] < TIMESTAMP){
			$resArr['error'] = 1;
			$resArr['message'] = "活动已经结束，不能参加！";
			echo json_encode($resArr);
			exit;
		}

		$hasmerchanthd = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANTHD)." WHERE merchant_id = {$merchant['id']} AND hdid = {$hdid}");
		if(empty($hasmerchanthd)){
			$data2['weid'] = $_W['uniacid'];
			$data2['hdid'] = $hdid;
			$data2['merchant_id'] = $merchant['id'];
			$data2['time'] = TIMESTAMP;
			$data2['sharetitle'] = $hdres['sharetitle'];
			$data2['sharethumb'] = $hdres['sharethumb'];
			$data2['sharedes'] = $hdres['sharedes'];
			$data2['canziti'] = 1;
			$data2['isxq'] = $hdres['isxq'];
			pdo_insert(BEST_MERCHANTHD,$data2);
			$mhdid = pdo_insertid();
			
			$hdgoods = pdo_fetchall("SELECT * FROM ".tablename(BEST_HUODONGGOODS)." WHERE weid = {$_W['uniacid']} AND hdid = {$hdid}");
			foreach($hdgoods as $k=>$v){
				$data = array();
				$goodsres = pdo_fetch("SELECT id,hasoption FROM ".tablename(BEST_GOODS)." WHERE weid = {$_W['uniacid']} AND id = {$v['goodsid']}");
				if($goodsres['hasoption'] == 1){
					$goodsoptions = pdo_fetchall("SELECT id FROM ".tablename(BEST_GOODSOPTION)." WHERE goodsid = {$goodsres['id']}");
					foreach($goodsoptions as $kk=>$vv){
						$data3 = array(); 
						$data3['weid'] = $_W['uniacid'];
						$data3['mhdid'] = $mhdid;
						$data3['optionid'] = $vv['id'];
						$data3['goodsid'] = $v['goodsid'];
						$data3['time'] = TIMESTAMP;
						pdo_insert(BEST_MERCHANTHDGOODS,$data3);
					}
				}else{
					$data['weid'] = $_W['uniacid'];
					$data['mhdid'] = $mhdid;
					$data['optionid'] = 0;
					$data['goodsid'] = $v['goodsid'];
					$data['time'] = TIMESTAMP;
					pdo_insert(BEST_MERCHANTHDGOODS,$data);
				}
			}
			$resArr['error'] = 0;
			$resArr['mhdid'] = $mhdid;
			$resArr['message'] = "参加活动成功！";
			echo json_encode($resArr);
			exit;
		}else{
			$resArr['error'] = 1;
			$resArr['message'] = "您已参加这个活动！";
			echo json_encode($resArr);
			exit;
		}
	}
	
	public function doMobileMyhddetail() {
		global $_W,$_GPC;
		$merchant = $this->checkmergentauth();
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if($operation == 'display'){
			$id = intval($_GPC['id']);
			$merchanthd = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANTHD)." WHERE weid = {$_W['uniacid']} AND merchant_id = {$merchant['id']} AND id = {$id}");
			if(empty($merchanthd)){
				$message = '不存在该活动！';
				include $this->template('error');
				exit;	
			}
			$hdres = pdo_fetch("SELECT * FROM ".tablename(BEST_HUODONG)." WHERE weid = {$_W['uniacid']} AND id = {$merchanthd['hdid']}");
			$merchantgoods = pdo_fetchall("SELECT * FROM ".tablename(BEST_MERCHANTHDGOODS)." WHERE mhdid = {$id} AND weid = {$_W['uniacid']}");
			foreach($merchantgoods as $k=>$v){
				$merchantgoods[$k]['goods'] = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE weid = {$_W['uniacid']} AND id = {$v['goodsid']}");
				if($v['optionid'] > 0){
					$merchantgoods[$k]['option'] = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODSOPTION)." WHERE id = {$v['optionid']}");
					$merchantgoods[$k]['lirun'] = $merchantgoods[$k]['option']['normalprice']-$merchantgoods[$k]['option']['dailiprice'];
					$merchantgoods[$k]['sales'] = pdo_fetchcolumn("SELECT SUM(a.total),b.id FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.mhdid = {$id} AND a.optionid = {$v['optionid']} AND b.status >= 1 AND a.orderid = b.id");
					$merchantgoods[$k]['sales'] = empty($merchantgoods[$k]['sales']) ? 0 : $merchantgoods[$k]['sales'];
				}else{
					$merchantgoods[$k]['lirun'] = $merchantgoods[$k]['goods']['normalprice']-$merchantgoods[$k]['goods']['dailiprice'];
					$merchantgoods[$k]['sales'] = pdo_fetchcolumn("SELECT SUM(a.total),b.id FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.mhdid = {$id} AND a.goodsid = {$v['goodsid']} AND b.status >= 1 AND a.orderid = b.id");
					$merchantgoods[$k]['sales'] = empty($merchantgoods[$k]['sales']) ? 0 : $merchantgoods[$k]['sales'];
				}
			}
			$allprice = pdo_fetchcolumn("SELECT SUM(price) FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND merchant_id = {$merchant['id']} AND mhdid = {$id} AND status >= 1");
			$allprice = empty($allprice) ? "0.00" : $allprice;
			$alllirun = pdo_fetchcolumn("SELECT SUM(alllirun) FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND merchant_id = {$merchant['id']} AND mhdid = {$id} AND status >= 1");
			$alllirun = empty($alllirun) ? "0.00" : $alllirun;
			$allordernum = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND merchant_id = {$merchant['id']} AND mhdid = {$id} AND status >= 1");
			include $this->template('myhddetail');
		}elseif($operation == 'do'){
			$id = intval($_GPC['id']);
			$data['sharetitle'] = trim($_GPC['sharetitle']);
			$data['sharedes'] = trim($_GPC['sharedes']);
			$data['sharethumb'] = trim($_GPC['sharethumb']);
			$data['yunfei'] = $_GPC['yunfei'];
			$data['manjian'] = $_GPC['manjian'];
			$data['canziti'] = intval($_GPC['canziti']);
			$data['cansonghuo'] = intval($_GPC['cansonghuo']);
			pdo_update(BEST_MERCHANTHD,$data,array('id'=>$id));
			foreach($_GPC['merhdgid'] as $k=>$v){
				if($_GPC['dlprice'][$k] > 0){
					pdo_update(BEST_MERCHANTHDGOODS,array('dlprice'=>$_GPC['dlprice'][$k]),array('id'=>$v));
				}
			}
			$resArr['error'] = 0;
			$resArr['message'] = '更新活动信息成功！';
			echo json_encode($resArr);
			exit;
		}
	}
	
	public function doMobileHdpeihuodan() {
		include_once ROOT_PATH.'inc/mobile/hdpeihuodan.php';
	}
	
	public function doMobileDaohuotz() {
		include_once ROOT_PATH.'inc/mobile/daohuotz.php';
	}
	
	private function checkmergentauth(){
		global $_W;
		$merchant = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE openid = '{$_W['fans']['from_user']}' AND weid = {$_W['uniacid']}");
		if(empty($merchant) || $merchant['status'] == 0){
			header("Location:".$this->createMobileUrl('merchantcenter'));
		}
		return $merchant;
	}
	
	public function doMobileHuodong() {
		global $_W,$_GPC;
		$member = $this->Mcheckmember();
		$mhdid = intval($_GPC['id']);
		$merchanthd = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANTHD)." WHERE weid = {$_W['uniacid']} AND id = {$mhdid}");
		$hdres = pdo_fetch("SELECT * FROM ".tablename(BEST_HUODONG)." WHERE weid = {$_W['uniacid']} AND id = {$merchanthd['hdid']}");
		$merchanthd['daojishi'] = 1;
		if($hdres['pstype'] == 1){
			$cansonghuo = $merchanthd['cansonghuo'];
			$canziti = $merchanthd['canziti'];
			$manjian = $merchanthd['manjian'];
		}else{
			$cansonghuo = 1;
			$canziti = 0;
			$manjian = $hdres['manjian'];
		}
		if($hdres['tqjs'] == 1){
			$iserror = 1;
			$errormessage = '活动已经提前结束';
			$merchanthd['daojishi'] = 0;
		}
		if($hdres['starttime'] > TIMESTAMP){
			$iserror = 1;
			$errormessage = '活动还未开始';
		}
		if($hdres['endtime'] < TIMESTAMP){
			$iserror = 1;
			$errormessage = '活动已经结束';
			$merchanthd['daojishi'] = 0;
		}
		$merchant = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} AND id = {$merchanthd['merchant_id']}");		
		$ztdlist = pdo_fetchall("SELECT * FROM ".tablename(BEST_ZITIDIAN)." WHERE openid = '{$merchant['openid']}' AND weid = {$_W['uniacid']} AND ztdtype = 1");
		
		$goodslist = pdo_fetchall("SELECT goodsid FROM ".tablename(BEST_MERCHANTHDGOODS)." WHERE mhdid = {$mhdid} GROUP BY goodsid");
		foreach($goodslist as $k=>$v){
			$hdgoods = pdo_fetch("SELECT displayorder FROM ".tablename(BEST_HUODONGGOODS)." WHERE weid = {$_W['uniacid']} AND hdid = {$merchanthd['hdid']} AND goodsid = {$v['goodsid']}");
			$goodsres = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE weid = {$_W['uniacid']} AND id = {$v['goodsid']}");
			
			
			$mhdoptions = pdo_fetchall("SELECT * FROM ".tablename(BEST_MERCHANTHDGOODS)." WHERE mhdid = {$mhdid} AND goodsid = {$v['goodsid']}");
			foreach($mhdoptions as $kk=>$vv){
				if($vv['optionid'] > 0){
					$option = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODSOPTION)." WHERE id = {$vv['optionid']}");
					if(empty($option)){
						unset($mhdoptions[$kk]);
					}else{
						if($vv['dlprice'] > $option['dailiprice']){
							$mhdoptions[$kk]['saleprice'] = $vv['dlprice'];
						}else{
							$mhdoptions[$kk]['saleprice'] = $option['normalprice'];
						}
						$option['sales'] = pdo_fetchcolumn("SELECT SUM(a.total) FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.optionid = {$option['id']} AND a.orderid = b.id AND b.hdid = {$merchanthd['hdid']} AND b.status >= 0");
						$option['sales'] = empty($option['sales']) ? $goodsres['basicsales'] : $goodsres['basicsales']+$option['sales'];
						if($option['xiangounum'] > 0){
							$optionmy['sales'] = pdo_fetchcolumn("SELECT SUM(a.total) FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.optionid = {$option['id']} AND a.orderid = b.id AND b.from_user = '{$member['openid']}' AND b.hdid = {$merchanthd['hdid']} AND b.status >= 0");
							$optionmy['sales'] = empty($optionmy['sales']) ? 0 : $optionmy['sales'];
							$option['xiangounum'] = $option['xiangounum']-$optionmy['sales'];
							if($option['stock'] > $option['xiangounum']){
								$option['canbuy'] = $option['xiangounum'];
							}
						}else{
							$option['canbuy'] = $option['stock'];
						}
						$mhdoptions[$kk]['option'] = $option;
					}
				}else{
					if($vv['dlprice'] > $goodsres['dailiprice']){
						$mhdoptions[$kk]['saleprice'] = $vv['dlprice'];
					}else{
						$mhdoptions[$kk]['saleprice'] = $goodsres['normalprice'];
					}
					$mhdoptions[$kk]['option'] = "";
				}
			}
			$goodslist[$k]['options'] = $mhdoptions;
			
			$thumbs = unserialize($goodsres['thumbs']);
			$goodsres['sales'] = pdo_fetchcolumn("SELECT SUM(a.total) FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.goodsid = {$goodsres['id']} AND a.orderid = b.id AND b.hdid = {$merchanthd['hdid']} AND b.status >= 0");
			$goodsres['sales'] = empty($goodsres['sales']) ? $goodsres['basicsales'] : $goodsres['basicsales']+$goodsres['sales'];
			if($goodsres['xiangounum'] > 0){
				$goodsresmy['sales'] = pdo_fetchcolumn("SELECT SUM(a.total) FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.goodsid = {$goodsres['id']} AND a.orderid = b.id AND b.from_user = '{$member['openid']}' AND b.hdid = {$merchanthd['hdid']} AND b.status >= 0");
				$goodsresmy['sales'] = empty($goodsresmy['sales']) ? 0 : $goodsresmy['sales'];
				$goodsres['xiangounum'] = $goodsres['xiangounum']-$goodsresmy['sales'];
				if($goodsres['total'] > $goodsres['xiangounum']){
					$goodsres['canbuy'] = $goodsres['xiangounum'];
				}
			}else{
				$goodsres['canbuy'] = $goodsres['total'];
			}
			$goodslist[$k]['goods'] = $goodsres;
			if(empty($thumbs)){
				$goodslist[$k]['count'] = 1;
				$goodslist[$k]['thumb1'] = "";
				$goodslist[$k]['thumb2'] = "";
			}else{
				$goodslist[$k]['count'] = count($thumbs)+1;
				$goodslist[$k]['thumb1'] = $thumbs[0];
				$goodslist[$k]['thumb2'] = $thumbs[1];
			}
			$goodslist[$k]['displayorder'] = $hdgoods['displayorder'];
		}
		array_multisort(array_column($goodslist,'displayorder'),SORT_DESC,$goodslist);
		

		$buylistthree = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE mhdid = {$merchanthd['id']} AND isjl = 0 AND status >= 1 ORDER BY createtime DESC LIMIT 10");
		foreach($buylistthree as $k=>$v){
			$buylistthree[$k]['member'] = pdo_fetch("SELECT avatar,nickname FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$v['from_user']}'");
			$buylistthree[$k]['goods'] = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$v['id']}");
		}

		$shareurl = $_W["siteroot"].'app/'.str_replace("./","",$this->createMobileUrl('huodong',array('id'=>$mhdid)));		
		$shenglist = pdo_fetchall("SELECT * FROM ".tablename(BEST_CITY)." WHERE type = 1");
		include $this->template('huodong');
	}
	
	public function doMobileGetcitys2() {
		global $_W,$_GPC;
		$fcode = trim($_GPC['fcode']);
		$citys = pdo_fetchall("SELECT name,code FROM ".tablename(BEST_CITY)." WHERE fcode = '{$fcode}' AND type = 2");		
		foreach($citys as $k=>$v){
			$html .= '<div class="messi-liandong-item-item erjiquyu textellipsis1" data-code="'.$v['code'].'">'.$v['name'].'</div>';
		}
		echo $html;
		exit;
	}
	
	public function doMobileGetdistricts2() {
		global $_W,$_GPC;
		$fcode = trim($_GPC['fcode']);
		$districts = pdo_fetchall("SELECT name FROM ".tablename(BEST_CITY)." WHERE fcode = '{$fcode}' AND type = 3");		
		foreach($districts as $k=>$v){
			$html .= '<div class="messi-liandong-item-item sanjiquyu textellipsis1" data-code="'.$v['code'].'">'.$v['name'].'</div>';
		}
		echo $html;
		exit;
	}
	
	public function doMobileGdetail() {
		global $_W,$_GPC;
		$id = intval($_GPC['id']);
		$goods = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE id = {$id} AND weid = {$_W['uniacid']}");
		$goodsthumbsarr[0] = tomedia($goods['thumb']);
		$thumbs = unserialize($goods['thumbs']);
		foreach($thumbs as $k=>$v){
			array_push($goodsthumbsarr,tomedia($v));
		}
		include $this->template('gdetail');
	}
	
	public function doMobileGgdetail() {
		global $_W,$_GPC;
		$member = $this->Mcheckmember();
		$xqid = intval($_GPC['xqid']);
		$xqmsg = $this->getxqmsg($xqid);
		$hdres = $xqmsg['hdres'];
		
		$id = intval($_GPC['id']);
		$goods = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE id = {$id} AND weid = {$_W['uniacid']}");
		if($goods['hasoption'] == 1){
			$goods['options'] = pdo_fetchall("SELECT * FROM ".tablename(BEST_GOODSOPTION)." WHERE goodsid = {$goods['id']}");
		}
		$goodsthumbsarr[0] = tomedia($goods['thumb']);
		$thumbs = unserialize($goods['thumbs']);
		foreach($thumbs as $k=>$v){
			array_push($goodsthumbsarr,tomedia($v));
		}

		$price = $goods['normalprice'];
		$scprice = $goods['scprice'];
		$salelist = pdo_fetchall("SELECT a.*,b.from_user FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.goodsid = {$id} AND a.orderid = b.id AND b.hdid = {$hdres['id']} AND b.status >= 1 ORDER BY b.createtime LIMIT 3");

		foreach($salelist as $kk=>$vv){
			$membersa = pdo_fetch("SELECT nickname,avatar FROM ".tablename(BEST_MEMBER)." WHERE weid = {$_W['uniacid']} AND openid = '{$vv['from_user']}'");
			$salelist[$kk]['nickname'] = $membersa['nickname'];
			$salelist[$kk]['avatar'] = $membersa['avatar'];
		}
			
		$data['views'] = $goods['views']+1;
		pdo_update(BEST_GOODS,$data,array('id'=>$goods['id']));
		
		$cartnum = pdo_fetchcolumn("SELECT SUM(total) FROM ".tablename(BEST_CART)." WHERE xqid = {$xqid} AND hdid = {$hdres['id']} AND openid = '{$member['openid']}'");
		$shareurl = $_W["siteroot"].'app/'.str_replace("./","",$this->createMobileUrl('ggdetail',array('id'=>$id,'optionid'=>$optionid,'xqid'=>$xqid)));
		$sharethumb = $goodsthumbsarr[0];
		include $this->template('ggdetail');
	}
	
	public function doMobileMyorder() {
		global $_W,$_GPC;
		$member = $this->Mcheckmember();
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if($operation == 'display'){
			$status = intval($_GPC["status"]);
			$pstype = intval($_GPC["pstype"]);
			if($status != 99){
				if($status == 0){
					$conditions = "weid = {$_W['uniacid']} AND isjl = 0 AND from_user = '{$member['openid']}' AND status = {$status}";
				}else{
					if($pstype == 0){
						$conditions = "weid = {$_W['uniacid']} AND isjl = 0 AND from_user = '{$member['openid']}' AND status = {$status} AND (pstype = 0 OR pstype = 3)";
					}else{
						$conditions = "weid = {$_W['uniacid']} AND isjl = 0 AND from_user = '{$member['openid']}' AND status = {$status} AND (pstype = 1 OR pstype = 4)";
					}
				}
			}else{
				$conditions = "weid = {$_W['uniacid']} AND isjl = 0 AND from_user = '{$member['openid']}'";
			}
			$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_ORDER)." WHERE ".$conditions);
			$psize = 10;
			$allpage = ceil($total/$psize)+1;
			$page = intval($_GPC["page"]);
			$pindex = max(1, $page);
			$orderlist = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE ".$conditions." ORDER BY createtime DESC LIMIT ".($pindex - 1)*$psize.",".$psize);
			foreach($orderlist as $k=>$v){
				$orderlist[$k]['gnum'] = pdo_fetchcolumn("SELECT SUM(total) FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$v['id']}");
				$orderlist[$k]['goodslist'] = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$v['id']}");
			}
			$isajax = intval($_GPC['isajax']);
			if($isajax == 1){
				$html = '';
				foreach($orderlist as $k=>$v){
					if($v['status'] == 0){
						$statustext = '<span class="wei">待付款</span>';
					}
					if($v['status'] == 1){
						$statustext = $v['pstype'] == 0 || $v['pstype'] == 3 ? '<span class="wei">待配送</span>' :'<span class="wei">待自提</span>';
					}
					if($v['status'] == 2){
						$statustext = '<span class="yi">已发货</span>';
					}
					if($v['status'] == 4){
						$statustext = '<span class="yi">已完成</span>';
					}
					if($v['status'] == -1){
						$statustext = '<span class="yi">已取消</span>';
					}
					if($v['status'] == -2){
						$statustext = '<span class="wei">退款中</span>';
					}
					if($v['status'] == -3){
						$statustext = '<span class="yi">已退款</span>';
					}
					$ghtml = '<div class="goods">';
					foreach($v['goodslist'] as $kk=>$vv){
						$ophtml = $vv['optionname'] == "" ? '' : '<span>['.$vv['optionname'].']</span>';
						$ghtml .= '<div class="goods-item flex">
										<div class="gname">'.$ophtml.$vv['goodsname'].'</div>
										<div class="gnum">&times; '.$vv['total'].'</div>
									</div>';
					}
					$ghtml .= '</div>';
					$html .= '<div class="orderitem yinying">
								<div class="name flex">
									<div class="ordersn">订单编号：'.$v['ordersn'].'</div>
									<div class="orderstatus">'.$statustext.'</div>
								</div>
								'.$ghtml.'
								<div class="price flex">
									<div class="orderprice">共'.$v['gnum'].'件，<span>￥</span><span class="da">'.$v['price'].'</span></div>
									<a class="orderbutton" href="'.$this->createMobileUrl('myorder',array('op'=>'detail','ordersn'=>$v['ordersn'])).'">订单详情</a>
								</div>
							</div>';
				}
				echo $html;
				exit;
			}else{
				if($status == 0){
					$title = '待付款订单';
				}
				if($status == 1){
					$title = $pstype == 0 || $pstype == 3 ? '待配送订单' :'待自提订单';
				}
				if($status == 2){
					$title = '已发货订单';
				}
				if($status == 99){
					$title = '全部订单';
				}
				include $this->template('myorder');
			};		
		}elseif($operation == 'detail'){
			$ordersn = trim($_GPC['ordersn']);
			$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE ordersn = '{$ordersn}' AND from_user = '{$member['openid']}'");
			if(empty($orderres)){
				$message = '没有该订单！';
				include $this->template('error');
				exit;
			}
			$huodongres = pdo_fetch("SELECT endtime FROM ".tablename(BEST_HUODONG)." WHERE id = {$orderres['hdid']}");
			if($huodongres['endtime'] < TIMESTAMP){
				$orderres['canfukuan'] = 0;
			}else{
				$orderres['canfukuan'] = 1;
			}
			
			$ordergoods = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$orderres['id']}");
			if($orderres['status'] == 0 && $orderres['canfukuan'] = 1){
				foreach($ordergoods as $k=>$v){					
					if($v['optionid'] > 0){
						$optionres = pdo_fetch("SELECT stock FROM ".tablename(BEST_GOODSOPTION)." WHERE id = {$v['optionid']}");
						if($optionres['stock'] < $v['total']){
							$orderres['canfukuan'] = 0;
						}
					}else{
						$goodsres = pdo_fetch("SELECT total FROM ".tablename(BEST_GOODS)." WHERE id = {$v['goodsid']}");
						if($goodsres['total'] < $v['total']){
							$orderres['canfukuan'] = 0;
						}
					}
				}
			}else{
				$orderres['canfukuan'] = 0;
			}
			if($orderres['expresscom'] != 'ZFPS' && $orderres['expresscom'] != 'SF' && $orderres['expresscom'] != ''){
				include_once(ROOT_PATH.'Express.class.php');
				$idkdn = $this->module['config']['kdnid'];
				$keykdn = $this->module['config']['kdnkey'];
				$shipperCode = $orderres['expresscom'];//快递公司简称，官方有文档
				$logisticCode = $orderres['expresssn'];//快递单号//
				$a = new Express(); 
				$logisticResult = $a->getOrderTracesByJson($idkdn,$keykdn,KDN_URL,$shipperCode,$logisticCode);
				$data = json_decode($logisticResult,true);
				if($data['State'] != "0"){
					$expressres = $data['Traces'];
					rsort($expressres);
				}
			}
			$address = explode("|",$orderres['address']);
			$xiaoqures = pdo_fetch("SELECT name FROM ".tablename(BEST_XIAOQU)." WHERE id = {$orderres['xqid']}");
			$orderres['xiaoquname'] = empty($xiaoqures) ? "" : $xiaoqures['name'];
			include $this->template('myorderdetail');
		}elseif($operation == 'shouhuo'){
			$orderid = intval($_GPC['orderid']);
			$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND status = 2 AND from_user = '{$member['openid']}'");
			if(empty($orderres)){
				$resArr['error'] = 1;
				$resArr['message'] = '没有该订单！';
				echo json_encode($resArr);
				exit();
			}
			$data['status'] = 4;
			pdo_update(BEST_ORDER,$data,array('id'=>$orderres['id']));
			if($orderres['isdmfk'] == 0){
				//利润写进代理商数据库，同时设置可提现时间
				$hasmerchantaccount = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANTACCOUNT)." WHERE merchant_id = {$orderres['merchant_id']} AND orderid = {$orderres['id']}");
				if(empty($hasmerchantaccount)){
					$datamerchant['weid'] = $_W['uniacid'];
					$datamerchant['merchant_id'] = $orderres['merchant_id'];
					$datamerchant['money'] = $orderres['alllirun'];
					$datamerchant['time'] = TIMESTAMP;
					$datamerchant['explain'] = '代理团结算';
					$datamerchant['orderid'] = $orderres['id'];
					$datamerchant['candotime'] = TIMESTAMP + ($this->module['config']['dltxhour'])*3600;
					pdo_insert(BEST_MERCHANTACCOUNT,$datamerchant);
				}
			}
			$resArr['error'] = 0;
			$resArr['message'] = '确认收货成功！';
			echo json_encode($resArr);
			exit();
		}elseif($operation == 'refund'){
			$orderid = intval($_GPC['orderid']);
			$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND (status = 1 OR status = 2) AND from_user = '{$member['openid']}'");
			if(empty($orderres)){
				$message = '没有该订单！';
				include $this->template('error');
				exit;
			}
			if($orderres['isdmfk'] == 1){
				$message = '该订单不能退款！';
				include $this->template('error');
				exit;
			}
			if($orderres['cantktime'] < TIMESTAMP){
				$message = '已经超过允许的退款时间！';
				include $this->template('error');
				exit;
			}
			$refund_price = $orderres['price']-$orderres['yunfei'];
			include $this->template('myorderrefund');
		}elseif($operation == 'dorefund'){
			$orderid = intval($_GPC['orderid']);
			$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE id = {$orderid} AND (status = 1 OR status = 2) AND from_user = '{$member['openid']}'");
			if(empty($orderres)){
				$resArr['error'] = 1;
				$resArr['message'] = '没有该订单！';
				echo json_encode($resArr);
				exit();
			}
			if($orderres['isdmfk'] == 1){
				$resArr['error'] = 1;
				$resArr['message'] = '该订单不能退款！';
				echo json_encode($resArr);
				exit();
			}
			if($orderres['cantktime'] < TIMESTAMP){
				$resArr['error'] = 1;
				$resArr['message'] = '已经超过允许的退款时间！';
				echo json_encode($resArr);
				exit();
			}
			
			$refund_price = $_GPC['refund_price'];
			if($refund_price <= 0){
				$resArr['error'] = 1;
				$resArr['message'] = '请填写退款金额！';
				echo json_encode($resArr);
				exit();
			}
			$can_refund_price = $orderres['price']-$orderres['yunfei'];
			if($refund_price > $can_refund_price){
				$resArr['error'] = 1;
				$resArr['message'] = '退款金额超限！';
				echo json_encode($resArr);
				exit();
			}
			
			$refund_desc = trim($_GPC['refund_desc']);
			if(empty($refund_desc)){
				$resArr['error'] = 1;
				$resArr['message'] = '请填写退款原因！';
				echo json_encode($resArr);
				exit();
			}
			
			$data['refund_desc'] = $refund_desc;
			$data['tktime'] = TIMESTAMP;
			$data['refund_price'] = $refund_price;
			$data['status'] = -2;
			pdo_update(BEST_ORDER,$data,array('id'=>$orderres['id']));
			$resArr['error'] = 0;
			$resArr['message'] = '申请退款成功！';
			echo json_encode($resArr);
			exit();
		}elseif ($operation == 'cancelorder') {
			$ordersn = trim($_GPC['ordersn']);
			$order = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE ordersn = '{$ordersn}' AND status = 0 AND from_user = '{$member['openid']}'");
			if(empty($order)){
				$resArr['error'] = 1;
				$resArr['message'] = '不存在该订单！';
				echo json_encode($resArr);
				exit();
			}else{
				$datacancel['status'] = -1;
				pdo_update(BEST_ORDER,$datacancel,array('id'=>$order['id']));
				$resArr['error'] = 0;
				$resArr['message'] = '取消订单成功！';
				echo json_encode($resArr);
				exit();
			}
		}else{
			$message = '请求方式不存在！';
			include $this->template('error');
			exit;
		}
	}
	
	public function doMobileSuborder() {
		global $_W,$_GPC;
		if(!checksubmit('submit')){
			exit;
		}
		$member = $this->Mcheckmember();
		$gunms = $_GPC['gnum'];
		$allnum = 0;
		$allprice = 0;
		$alllirun = 0;
		$hdres = pdo_fetch("SELECT * FROM ".tablename(BEST_HUODONG)." WHERE weid = {$_W['uniacid']} AND id = {$_GPC['hdid']}");
		if($hdres['tqjs'] == 1){
			$resArr['error'] = 1;
			$resArr['message'] = "活动已经提前结束！";
			echo json_encode($resArr);
			exit;
		}
		if($hdres['starttime'] > TIMESTAMP){
			$resArr['error'] = 1;
			$resArr['message'] = "活动还未开始！";
			echo json_encode($resArr);
			exit;
		}
		if($hdres['endtime'] < TIMESTAMP){
			$resArr['error'] = 1;
			$resArr['message'] = "活动已经结束！";
			echo json_encode($resArr);
			exit;
		}
		$pstype = intval($_GPC['pstype']);
		if($pstype != 0 && $pstype != 1){
			$resArr['error'] = 1;
			$resArr['message'] = "请选择配送方式！";
			echo json_encode($resArr);
			exit;
		}
		
		foreach($gunms as $k=>$v){
			$goodsid = intval($_GPC['goodsid'][$k]);
			$optionid = intval($_GPC['optionid'][$k]);
			if($v > 0){
				$merchanthdgoods = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANTHDGOODS)." WHERE goodsid = {$goodsid} AND optionid = {$optionid} AND mhdid = {$_GPC['mhdid']}");
				$goodsres = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE weid = {$_W['uniacid']} AND id = {$goodsid}");
				$optionres = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODSOPTION)." WHERE goodsid = {$goodsid} AND id = {$optionid}");
				if(!empty($optionres)){
					if($v > $optionres['stock']){
						$resArr['error'] = 1;
						$resArr['message'] = "商品".$goodsres['title']."[".$optionres['title']."]"."库存不足！";
						echo json_encode($resArr);
						exit;
					}
					if($v > $optionres['xiangounum'] && $optionres['xiangounum'] > 0){
						$resArr['error'] = 1;
						$resArr['message'] = "商品".$goodsres['title']."[".$optionres['title']."]"."限购".$optionres['xiangounum']."件！";
						echo json_encode($resArr);
						exit;
					}
					if($merchanthdgoods['dlprice'] >= $optionres['dailiprice']){
						$allprice += $merchanthdgoods['dlprice']*$v;
						$alllirun += ($merchanthdgoods['dlprice']-$optionres['dailiprice'])*$v;
					}else{
						$allprice += $optionres['normalprice']*$v;
						$alllirun += ($optionres['normalprice']-$optionres['dailiprice'])*$v;
					}
				}else{
					if($v > $goodsres['total']){
						$resArr['error'] = 1;
						$resArr['message'] = "商品".$goodsres['title']."库存不足！";
						echo json_encode($resArr);
						exit;
					}
					if($v > $goodsres['xiangounum'] && $goodsres['xiangounum'] > 0){
						$resArr['error'] = 1;
						$resArr['message'] = "商品".$goodsres['title']."限购".$goodsres['xiangounum']."件！";
						echo json_encode($resArr);
						exit;
					}
					if($merchanthdgoods['dlprice'] >= $goodsres['dailiprice']){
						$allprice += $merchanthdgoods['dlprice']*$v;
						$alllirun += ($merchanthdgoods['dlprice']-$goodsres['dailiprice'])*$v;
					}else{
						$allprice += $goodsres['normalprice']*$v;
						$alllirun += ($goodsres['normalprice']-$goodsres['dailiprice'])*$v;
					}
				}
			}
			$allnum += $v;
		}
		if($allnum <= 0){
			$resArr['error'] = 1;
			$resArr['message'] = "请选择需要购买的商品！";
			echo json_encode($resArr);
			exit;
		}
		if($allprice <= 0){
			$resArr['error'] = 1;
			$resArr['message'] = "订单总金额不得少于0元！";
			echo json_encode($resArr);
			exit;
		}
		$data['price'] = $allprice;
		$data['alllirun'] = $alllirun;
		if($pstype == 0){
			$shname = trim($_GPC['shname']);
			$shphone = trim($_GPC['shphone']);
			$shcity = trim($_GPC['shcity']);
			$shaddress = trim($_GPC['shaddress']);
			if(empty($shname)){
				$resArr['error'] = 1;
				$resArr['message'] = "请填写收货人姓名！";
				echo json_encode($resArr);
				exit;
			}
			if(!$this->isMobile($shphone)){
				$resArr['error'] = 1;
				$resArr['message'] = "请填写正确的收货人手机号码！";
				echo json_encode($resArr);
				exit;
			}
			if(empty($shcity)){
				$resArr['error'] = 1;
				$resArr['message'] = "请选择区域！";
				echo json_encode($resArr);
				exit;
			}
			if(empty($shaddress)){
				$resArr['error'] = 1;
				$resArr['message'] = "请填写详细地址！";
				echo json_encode($resArr);
				exit;
			}
			if($hdres['pstype'] == 0){
				if($hdres['yfid'] > 0){
					$diquarr = explode(" ",$shcity);
					$sheng = $diquarr[0];
					$shi = $diquarr[1];
					$xian = $diquarr[2];
					$yfsheng1 = pdo_fetch("SELECT * FROM ".tablename(BEST_YUNFEISHENG)." WHERE yfid = {$hdres['yfid']} AND diqutype = 3 AND name = '{$sheng}' AND city = '{$shi}' AND xian = '{$xian}'");
					$yfsheng2 = pdo_fetch("SELECT * FROM ".tablename(BEST_YUNFEISHENG)." WHERE yfid = {$hdres['yfid']} AND diqutype = 2 AND name = '{$sheng}' AND city = '{$shi}' AND xian = ''");
					$yfsheng3 = pdo_fetch("SELECT * FROM ".tablename(BEST_YUNFEISHENG)." WHERE yfid = {$hdres['yfid']} AND diqutype = 1 AND name = '{$sheng}' AND city = '' AND xian = ''");
					if(empty($yfsheng1) && empty($yfsheng2) && empty($yfsheng3)){
						$resArr['error'] = 1;
						$resArr['message'] = "不在活动售卖区域不能提交订单！";
						echo json_encode($resArr);
						exit;
					}
				}

				if($data['price'] >= $hdres['manjian']){
					$data['yunfei'] = 0;
				}else{
					if(!empty($yfsheng1)){
						$data['yunfei'] = $yfsheng1['money'];
					}
					if(!empty($yfsheng2) && empty($yfsheng1)){
						$data['yunfei'] = $yfsheng2['money'];
					}
					if(!empty($yfsheng3) && empty($yfsheng1) && empty($yfsheng2)){
						$data['yunfei'] = $yfsheng3['money'];
					}
				}
				$data['remark'] = $_GPC['remark'];
			}else{
				$merchanthd = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANTHD)." WHERE weid = {$_W['uniacid']} AND id = {$_GPC['mhdid']}");
				if($data['price'] >= $merchanthd['manjian']){
					$data['yunfei'] = 0;
				}else{
					$data['yunfei'] = $merchanthd['yunfei'];
				}
				$pstype = 3;
				$data['alllirun'] = $data['alllirun'] + $data['yunfei'];
				$data['remark'] = $_GPC['remark2'];
			}
			$datam['shname'] = $shname;
			$datam['shphone'] = $shphone;
			$datam['shcity'] = $shcity;
			$datam['shaddress'] = $shaddress;
			$data['address'] = $shname."|".$shphone."|".$shcity."|".$shaddress;
			$data['price'] = $data['price']+$data['yunfei'];
			$data['isdmfk'] = intval($_GPC['isdmfk']);
			if($data['isdmfk'] == 1){
				$data['status'] = 2;
			}
		}else{
			$shname = $datam['shname'] = trim($_GPC['shname2']);
			if(empty($shname)){
				$resArr['error'] = 1;
				$resArr['message'] = "请填写取货人人姓名！";
				echo json_encode($resArr);
				exit;
			}
			$shphone = $datam['shphone'] = trim($_GPC['shphone2']);
			if(!$this->isMobile($shphone)){
				$resArr['error'] = 1;
				$resArr['message'] = "请填写自提所需的手机号码！";
				echo json_encode($resArr);
				exit;
			}
			$ztdid = intval($_GPC['ztdid']);
			$ztdres = pdo_fetch("SELECT * FROM ".tablename(BEST_ZITIDIAN)." WHERE id = {$ztdid}");
			if(empty($ztdres)){
				$resArr['error'] = 1;
				$resArr['message'] = "请选择自提点！";
				echo json_encode($resArr);
				exit;
			}
			$data['ztdid'] = $ztdid;
			$data['ztdaddress'] = $ztdres['address'];
			$data['ztdjingdu'] = $ztdres['jingdu'];
			$data['ztdweidu'] = $ztdres['weidu'];
			$data['address'] = $shphone."|".$shname;
			$data['yunfei'] = 0;
			if($hdres['pstype'] == 1){
				$pstype = 4;
			}
			$data['isdmfk'] = intval($_GPC['isdmfk']);
			if($data['isdmfk'] == 1){
				$data['status'] = 1;
			}
		}
		if($hdres['autofield'] == 1){
			$isidcard = $this->isCard($_GPC['idcard']);
			if(empty($isidcard)){
				$resArr['error'] = 1;
				$resArr['message'] = "请输入正确的身份证号！";
				echo json_encode($resArr);
				exit;
			}
			$data['otheraddress'] = $_GPC['idcard']."(身份证)";
		}
		if($hdres['autofield'] == 2){
			if(empty($_GPC['wxcode'])){
				$resArr['error'] = 1;
				$resArr['message'] = "请填写微信号！";
				echo json_encode($resArr);
				exit;
			}
			$data['otheraddress'] = $_GPC['wxcode']."(微信号)";
		}
		$data['weid'] = $_W['uniacid'];
		$data['pstype'] = $pstype;
		$data['from_user'] = $member['openid'];
		$data['ordersn'] = date('Ymd').random(13,1);
		$data['merchant_id'] = intval($_GPC['merchant_id']);
		$data['createtime'] = TIMESTAMP;
		$data['hdid'] = intval($_GPC['hdid']);
		$data['mhdid'] = intval($_GPC['mhdid']);
		if(isset($this->module['config']['dltkhour'])){
			$data['cantktime'] = $hdres['endtime'] - ($this->module['config']['dltkhour'])*3600;
		}
		pdo_insert(BEST_ORDER, $data);
		$orderid = pdo_insertid();

		pdo_update(BEST_MEMBER,$datam,array('openid'=>$member['openid']));
		foreach($gunms as $k=>$v){			
			$goodsid = intval($_GPC['goodsid'][$k]);
			$optionid = intval($_GPC['optionid'][$k]);
			if($v > 0){
				$merchanthdgoods = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANTHDGOODS)." WHERE goodsid = {$goodsid} AND optionid = {$optionid} AND mhdid = {$_GPC['mhdid']}");
				$goodsres = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE weid = {$_W['uniacid']} AND id = {$goodsid}");
				$optionres = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODSOPTION)." WHERE goodsid = {$goodsid} AND id = {$optionid}");
				if(!empty($optionres)){
					if($merchanthdgoods['dlprice'] >= $optionres['dailiprice']){
						$datao['price'] = $merchanthdgoods['dlprice'];
						$datao['lirun'] = ($merchanthdgoods['dlprice']-$optionres['dailiprice'])*$v;
					}else{
						$datao['price'] = $optionres['normalprice'];
						$datao['lirun'] = ($optionres['normalprice']-$optionres['dailiprice'])*$v;
					}
					$datao['weid'] = $_W['uniacid'];
					$datao['optionid'] = $optionid;
					$datao['total'] = $v;
					$datao['cbprice'] = $optionres['chengbenprice'];
					$datao['dlprice'] = $optionres['dailiprice'];
					$datao['goodsid'] = $goodsid;
					$datao['createtime'] = TIMESTAMP;
					$datao['orderid'] = $orderid;
					$datao['goodsname'] = $goodsres['title'];
					$datao['optionname'] = $optionres['title'];
				}else{
					if($merchanthdgoods['dlprice'] >= $goodsres['dailiprice']){						
						$datao['price'] = $merchanthdgoods['dlprice'];
						$datao['lirun'] = ($merchanthdgoods['dlprice']-$goodsres['dailiprice'])*$v;
					}else{	
						$datao['price'] = $goodsres['normalprice'];
						$datao['lirun'] = ($goodsres['normalprice']-$goodsres['dailiprice'])*$v;
					}
					$datao['weid'] = $_W['uniacid'];
					$datao['optionid'] = 0;
					$datao['total'] = $v;
					$datao['cbprice'] = $goodsres['chengbenprice'];
					$datao['dlprice'] = $goodsres['dailiprice'];
					$datao['goodsid'] = $goodsid;
					$datao['createtime'] = TIMESTAMP;
					$datao['orderid'] = $orderid;
					$datao['goodsname'] = $goodsres['title'];
					$datao['optionname'] = "";
				}
				$datao['hdid'] = intval($_GPC['hdid']);
				$datao['mhdid'] = intval($_GPC['mhdid']);
				pdo_insert(BEST_ORDERGOODS,$datao);
			}
		}
		$resArr['error'] = 0;
		$resArr['status'] = $data['status'];
		$resArr['fee'] = $allprice+$data['yunfei'];
		$resArr['ordertid'] = $data['ordersn'];
		$resArr['message'] = "提交订单成功！";
		echo json_encode($resArr);
		exit;
	}
	
	public function payResult($params) {
		global $_W;
		if ($params['result'] == 'success' && $params['from'] == 'notify') {
			$sql = 'SELECT * FROM '.tablename('core_paylog').' WHERE `tid`=:tid';
			$pars = array();
			$pars[':tid'] = $params['tid'];
			$log = pdo_fetch($sql, $pars);
			$paydetail = $log['tag'];
			$logtag = unserialize($log['tag']);
			$ordersnlen = strlen($params['tid']);
			//处理逻辑
			if($ordersnlen == 18){
				pdo_update(BEST_MERCHANT,array('transid'=>$logtag['transaction_id'],'paydetail'=>$paydetail),array('ordersn'=>$params['tid']));
				$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE ordersn = '{$params['tid']}' AND weid = {$_W['uniacid']}");
				
				$yqmer = pdo_fetch("SELECT id,istz FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} AND openid = '{$orderres['tzfopenid']}'");
				pdo_update(BEST_MERCHANT,array('fopenid'=>$orderres['tzfopenid'],array('id'=>$orderres['id'])));
				if($yqmer['istz'] == 0 && !empty($yqmer)){
					pdo_update(BEST_MERCHANT,array('istz'=>1,'tztime'=>TIMESTAMP),array('openid'=>$orderres['tzfopenid'],'weid'=>$_W['uniacid']));
				}
				
				if($orderres['tzyongjin'] > 0){
					$datamerchant['weid'] = $_W['uniacid'];
					$datamerchant['merchant_id'] = $yqmer['id'];
					$datamerchant['money'] = $orderres['tzyongjin'];
					$datamerchant['time'] = TIMESTAMP;
					$datamerchant['explain'] = '邀请团长加入奖励';
					pdo_insert(BEST_MERCHANTACCOUNT,$datamerchant);
				}
			}
			if($ordersnlen == 19){
				pdo_update(BEST_TZORDER,array('status'=>1,'transid'=>$logtag['transaction_id'],'paydetail'=>$paydetail),array('ordersn'=>$params['tid']));
				$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_TZORDER)." WHERE ordersn = '{$params['tid']}' AND weid = {$_W['uniacid']}");
				$data['istz'] = 1;
				$data['tztime'] = TIMESTAMP;
				$data['tzintype'] = 1;
				pdo_update(BEST_MERCHANT,$data,array('openid'=>$orderres['openid']));
			}
			if($ordersnlen == 20){
				pdo_update(BEST_MEMBERRZ,array('rzstatus'=>1,'rztransid'=>$logtag['transaction_id'],'rzpaydetail'=>$paydetail),array('rzordersn'=>$params['tid']));
				//$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERRZ)." WHERE rzordersn = '{$params['tid']}' AND weid = {$_W['uniacid']}");
				//pdo_update(BEST_MEMBER,array('rztype'=>$orderres['rztype']),array('openid'=>$orderres['openid']));
				//$or_paysuccess_redirect = $_W["siteroot"].'app/'.str_replace("./","",$this->createMobileUrl("my"));
			}
			if($ordersnlen == 21){
				pdo_update(BEST_ORDER,array('status'=>1,'transid'=>$logtag['transaction_id'],'paydetail'=>$paydetail),array('ordersn'=>$params['tid']));
				$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE ordersn = '{$params['tid']}' AND weid = {$_W['uniacid']}");
				$ordergoods = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$orderres['id']}");
				foreach($ordergoods as $k=>$v){
					$goodsres = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE id = {$v['goodsid']} AND weid = {$_W['uniacid']}");
					$datagoods['total'] = $goodsres['total']-$v['total'];
					pdo_update(BEST_GOODS,$datagoods,array('id'=>$v['goodsid']));
					if($v['optionid'] > 0){
						$goodsoptionres = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODSOPTION)." WHERE id = {$v['optionid']}");
						$datagoodsoption['stock'] = $goodsoptionres['stock']-$v['total'];
						pdo_update(BEST_GOODSOPTION,$datagoodsoption,array('id'=>$v['optionid']));
					}
				}
				if($this->module['config']['istplon'] == 1){
					if($orderres['pstype'] == 0 || $orderres['pstype'] == 3){
						$address_tplarr = explode("|",$orderres['address']);
						$realname_tpl = $address_tplarr[0];
						$phone_tpl = $address_tplarr[1];
						$address_tpl = $address_tplarr[2].$address_tplarr[3];
					}else{
						$realname_tpl = $address_tpl = '自提订单无需信息';
						$phone_tpl = $orderres['address'];
					}
					$merchant = pdo_fetch("SELECT openid FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} AND id = {$orderres['merchant_id']}");
					$or_paysuccess_redirect = $_W["siteroot"].'app/'.str_replace("./","",$this->createMobileUrl("merchantorder",array('op'=>'detail','id'=>$orderres['id'])));
					$postdata = array(
						'first' => array(
							'value' => "用户下单通知！",
							'color' => '#ff510'
						),
						'keyword1' => array(
							'value' => $orderres['ordersn'],
							'color' => '#ff510'
						),
						'keyword2' => array(
							'value' => $orderres['price'],
							'color' => '#ff510'
						),
						'keyword3' => array(
							'value' => $realname_tpl,
							'color' => '#ff510'
						),
						'keyword4' => array(
							'value' => $phone_tpl,
							'color' => '#ff510'
						),
						'keyword5' => array(
							'value' => $address_tpl,
							'color' => '#ff510'
						),
						'Remark' => array(
							'value' => "" ,
							'color' => '#ff510'
						),							
					);
					$account_api = WeAccount::create();
					$account_api->sendTplNotice($merchant['openid'],$this->module['config']['nt_order_new'],$postdata,$or_paysuccess_redirect,'#FF5454');
				}
			}
			if($ordersnlen == 22){
				pdo_update(BEST_ORDER,array('status'=>1,'transid'=>$logtag['transaction_id'],'paydetail'=>$paydetail),array('ordersn'=>$params['tid']));
				$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE ordersn = '{$params['tid']}' AND weid = {$_W['uniacid']}");
				
				$memberhd = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERHUODONG)." WHERE id = {$orderres['jlid']} AND weid = {$_W['uniacid']}");
				$datamemberhd['inpeople'] = $memberhd['inpeople']+1;
				$xingyunarr = explode("-",$memberhd['xingyun']);
				if(in_array($datamemberhd['inpeople'],$xingyunarr) && !empty($memberhd['xingyun'])){
					pdo_update(BEST_ORDER,array('xingyun'=>1),array('id'=>$orderres['id']));
				}
				pdo_update(BEST_MEMBERHUODONG,$datamemberhd,array('id'=>$orderres['jlid']));
				
				$ordergoods = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$orderres['id']}");
				foreach($ordergoods as $k=>$v){
					$goodsres = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERGOODS)." WHERE id = {$v['goodsid']} AND weid = {$_W['uniacid']}");
					$datagoods['total'] = $goodsres['total']-$v['total'];
					$datagoods['inpeople'] = $goodsres['inpeople']+1;
					pdo_update(BEST_MEMBERGOODS,$datagoods,array('id'=>$v['goodsid']));
				}

				if($this->module['config']['istplon'] == 1){
					if($orderres['pstype'] == 0 || $orderres['pstype'] == 3){
						$address_tplarr = explode("|",$orderres['address']);
						$realname_tpl = $address_tplarr[0];
						$phone_tpl = $address_tplarr[1];
						$address_tpl = $address_tplarr[2].$address_tplarr[3];
					}else{
						$realname_tpl = $address_tpl = '自提订单无需信息';
						$phone_tpl = $orderres['address'];
					}
					$or_paysuccess_redirect2 = $_W["siteroot"].'app/'.str_replace("./","",$this->createMobileUrl("myfbjl",array('op'=>'orderdetail','orderid'=>$orderres['id'])));
					$postdata2 = array(
						'first' => array(
							'value' => "用户下单通知！",
							'color' => '#ff510'
						),
						'keyword1' => array(
							'value' => $orderres['ordersn'],
							'color' => '#ff510'
						),
						'keyword2' => array(
							'value' => $orderres['price'],
							'color' => '#ff510'
						),
						'keyword3' => array(
							'value' => $realname_tpl,
							'color' => '#ff510'
						),
						'keyword4' => array(
							'value' => $phone_tpl,
							'color' => '#ff510'
						),
						'keyword5' => array(
							'value' => $address_tpl,
							'color' => '#ff510'
						),
						'Remark' => array(
							'value' => "" ,
							'color' => '#ff510'
						),							
					);
					$account_api = WeAccount::create();
					$account_api->sendTplNotice($memberhd['openid'],$this->module['config']['nt_order_new'],$postdata2,$or_paysuccess_redirect2,'#FF5454');
				}
	
			}			
		}
		if ($params['from'] == 'return') {
			if ($params['result'] == 'success') {
				message('支付成功！',referer() , 'success');
			} else {
				message('支付失败！',referer(), 'error');
			}
		}
	}
	
	
	public function doWebOrder() {
		include_once ROOT_PATH.'inc/web/order.php';
	}
	
	public function doWebGcat() {
		include_once ROOT_PATH.'inc/web/gcat.php';
	}
	
	public function doWebTongbu() {
		include_once ROOT_PATH.'inc/web/tongbu.php';
	}
	
	public function doWebHuodong() {
		include_once ROOT_PATH.'inc/web/huodong.php';
	}
	
	public function doWebQkhaibao() {
		global $_GPC, $_W;
		$path = HB_ROOT_TD;
		$this->deldir($path);
		
		$path2 = HB_ROOT_ZFJL;
		$this->deldir($path2);
		
		$path3 = HB_ROOT_SHEQU;
		$this->deldir($path3);
		
		$path4 = HB_ROOT_SHEQUINDEX;
		$this->deldir($path4);
		
		$path5 = HB_ROOT_ALLHX;
		$this->deldir($path5);
		message('操作成功！', "", 'success');
	}
	
	public function deldir($path){
	   //如果是目录则继续
	   if(is_dir($path)){
		//扫描一个文件夹内的所有文件夹和文件并返回数组
	   $p = scandir($path);
	   foreach($p as $val){
		//排除目录中的.和..
		if($val !="." && $val !=".."){
		 //如果是目录则递归子目录，继续操作
		 if(is_dir($path.$val)){
		  //子目录中操作删除文件夹和文件
		 $this-> deldir($path.$val.'/');
		  //目录清空后删除空文件夹
		  @rmdir($path.$val.'/');
		 }else{
		  //如果是文件直接删除
		  unlink($path.$val);
		 }
		}
	   }
	  }
	}
	
	public function doWebGoods() {
		global $_GPC, $_W;
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'post') {
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE id = {$id}");
				if (empty($item)) {
					message('抱歉，商品不存在或是已经删除！', '', 'error');
				}
				$options = pdo_fetchall("select * from ".tablename(BEST_GOODSOPTION)." where goodsid={$id} order by displayorder ASC");	
				$piclist = unserialize($item['thumbs']);				
			}
			$gcatlist = pdo_fetchall("SELECT * FROM ".tablename(BEST_GCAT)." WHERE weid = {$_W['uniacid']} ORDER BY displayorder ASC");
			if (checksubmit('submit')) {
				if (empty($_GPC['title'])) {
					message('请输入商品名称！');
				}
				if (empty($_GPC['ftitle'])) {
					message('请输入商品简称！');
				}
				if (empty($_GPC['thumb'])) {
					message('请上传商品图片！');
				}
				$data = array(
					'weid' => intval($_W['uniacid']),
					'title' => $_GPC['title'],
					'ftitle' => $_GPC['ftitle'],
					'title2' => $_GPC['title2'],
					'title3' => $_GPC['title3'],
					'shotdes' => $_GPC['shotdes'],
					'cateid'=>intval($_GPC['cateid']),
					'thumb' => $_GPC['thumb'],
					'changthumb' => $_GPC['changthumb'],
					'createtime' => TIMESTAMP,
					'total' => intval($_GPC['total']),
					'normalprice' => $_GPC['normalprice'],
					'chengbenprice' => $_GPC['chengbenprice'],
					'dailiprice' => $_GPC['dailiprice'],
					'scprice' => $_GPC['scprice'],
					'xiangounum' => intval($_GPC['xiangounum']),
					'tiaoma' => $_GPC['tiaoma'],
					'des' => $_GPC['des'],
					'views' => intval($_GPC['views']),
					'displayorder' => intval($_GPC['displayorder']),
					'autofield' => intval($_GPC['autofield']),
					'basicsales' => intval($_GPC['basicsales']),
					
					'canddbuy' => intval($_GPC['canddbuy']),
					'shownoxq' => intval($_GPC['shownoxq']),
				);
				if ($data['total'] === -1) {
					$data['total'] = 0;
				}
				if(empty($_GPC['thumbs'])){
					$_GPC['thumbs'] = array();
				}
				if(is_array($_GPC['thumbs'])){
					$data['thumbs'] = serialize($_GPC['thumbs']);
				}
				if (empty($id)) {
					pdo_insert(BEST_GOODS, $data);
					$id = pdo_insertid();
				} else {
					unset($data['createtime']);
					pdo_update(BEST_GOODS, $data, array('id' => $id));					
				}
				
				//处理规格
				$totalstocks = 0;
				$option_ids = $_POST['option_id'];
				$option_titles = $_POST['option_title'];
				$option_normalprices = $_POST['option_normalprice'];
				$option_chengbenprices = $_POST['option_chengbenprice'];
				$option_dailiprices = $_POST['option_dailiprice'];
				$option_scprices = $_POST['option_scprice'];
				$option_xiangounums = $_POST['option_xiangounum'];
				$option_stocks = $_POST['option_stock'];
				$len = count($option_ids);
				$optionids = array();
				for ($k = 0; $k < $len; $k++) {
					$option_id = "";
					$get_option_id = $option_ids[$k];
					$a = array(
						"title" => $option_titles[$k],
						"normalprice" => $option_normalprices[$k],
						"chengbenprice" => $option_chengbenprices[$k],
						"dailiprice" => $option_dailiprices[$k],
						'scprice' => $option_scprices[$k],
						'xiangounum' => $option_xiangounums[$k],
						"stock" => $option_stocks[$k],
						"displayorder" => $k,
						"goodsid" => $id,
					);
					if (!is_numeric($get_option_id)) {
						pdo_insert(BEST_GOODSOPTION, $a);
						$option_id = pdo_insertid();
					} else {
						pdo_update(BEST_GOODSOPTION, $a, array('id' => $get_option_id));
						$option_id = $get_option_id;
					}
					$optionids[] = $option_id;
					$totalstocks += $option_stocks[$k];
				}
				if (count($optionids) > 0) {
					pdo_query("delete from " . tablename(BEST_GOODSOPTION) . " where goodsid = {$id} and id not in ( " . implode(',', $optionids) . ")");
					pdo_update(BEST_GOODS,array('total'=>$totalstocks,'hasoption'=>1),array('id'=>$id));
				}else{
					pdo_query("delete from " . tablename(BEST_GOODSOPTION) . " where goodsid = {$id}");
					pdo_update(BEST_GOODS,array('hasoption'=>0),array('id'=>$id));
				}

				message('操作成功！', $this->createWebUrl('goods', array('op' => 'display', 'id' => $id)), 'success');
			}
		} elseif ($operation == 'display') {
			if (!empty($_GPC['displayorder'])) {
				foreach ($_GPC['displayorder'] as $id => $displayorder) {
					pdo_update(BEST_GOODS, array('displayorder' => $displayorder), array('id' => $id, 'weid' => $_W['uniacid']));
				}
				message('商品排序更新成功！', $this->createWebUrl('goods', array('op' => 'display')), 'success');
			}
	
			$pindex = max(1, intval($_GPC['page']));
			$psize = 10;
			$condition = ' WHERE weid = :weid';
			$params = array(':weid' => $_W['uniacid']);
			if (!empty($_GPC['keyword'])) {
				$condition .= ' AND `title` LIKE :title';
				$params[':title'] = '%' . trim($_GPC['keyword']) . '%';
			}

			$sql = 'SELECT COUNT(*) FROM ' . tablename(BEST_GOODS) . $condition;
			$total = pdo_fetchcolumn($sql, $params);
			if (!empty($total)) {
				$sql = 'SELECT * FROM '.tablename(BEST_GOODS).$condition.' ORDER BY displayorder DESC,createtime DESC,
						`id` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
				$list = pdo_fetchall($sql, $params);
				$pager = pagination($total, $pindex, $psize);
			}
		}elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			
			/*$huodonggoods = pdo_fetchall("SELECT hdid FROM ".tablename(BEST_HUODONGGOODS)." WHERE weid = {$_W['uniacid']} AND goodsid = {$id}");
			foreach($huodonggoods as $k=>$v){
				$huodong = pdo_fetch("SELECT title,endtime FROM ".tablename(BEST_HUODONG)." WHERE weid = {$_W['uniacid']} AND id = {$v['hdid']}");
				if($huodong['endtime'] > TIMESTAMP){
					message('该商品参与的活动：'.$huodong['title'].'。还未结束，不能删除！', $this->createWebUrl('goods', array('op' => 'display', 'id' => $id)), 'error');
				}
			}*/
			pdo_delete(BEST_GOODS,array('id'=>$id));
			pdo_delete(BEST_CART,array('goodsid'=>$id));
			pdo_delete(BEST_GOODSOPTION,array('goodsid'=>$id));
			pdo_delete(BEST_HUODONGGOODS,array('goodsid'=>$id));
			pdo_delete(BEST_MERCHANTHDGOODS,array('goodsid'=>$id));
			message('操作成功！', $this->createWebUrl('goods', array('op' => 'display')), 'success');
		}
		include $this->template('web/goods');
	}
	public function doWebOption() {
		$tag = random(32);
		global $_GPC;
		include $this->template('web/option');
	}
	
	public function doWebMember() {
		include_once ROOT_PATH.'inc/web/member.php';
	}
	
	public function doWebMerchant() {
		include_once ROOT_PATH.'inc/web/merchant.php';
	}
	
	public function doWebTixian() {
		include_once ROOT_PATH.'inc/web/tixian.php';
	}
	
	public function doWebYunfei() {
		include_once ROOT_PATH.'inc/web/yunfei.php';
	}
	
	public function doWebJielong() {
		include_once ROOT_PATH.'inc/web/jielong.php';
	}
	
	public function doWebJlorder() {
		include_once ROOT_PATH.'inc/web/jlorder.php';
	}
	
	public function doWebJiesuan() {
		include_once ROOT_PATH.'inc/web/jiesuan.php';
	}
	
	public function doWebRefund() {
		include_once ROOT_PATH.'inc/web/refund.php';
	}
	
	public function doWebRenzheng() {
		include_once ROOT_PATH.'inc/web/renzheng.php';
	}
	
	public function doWebTeamjiang() {
		include_once ROOT_PATH.'inc/web/teamjiang.php';
	}
	
	public function doWebVillage() {
		include_once ROOT_PATH.'inc/web/village.php';
	}
	
	public function doMobileGetmedia(){
		global $_W, $_GPC;
		include_once ROOT_PATH.'ImageCrop.class.php';
		$access_token = WeAccount::token();
		$media_id = $_GPC['media_id'];
        $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".$access_token."&media_id=".$media_id;		
		$response = ihttp_get($url);
		if(is_error($response)) {			
			$resarr['error'] = 1;
			$resarr['message'] = "访问公众平台接口失败, 错误: {$response['message']}";
			die(json_encode($resarr));
		}
		
		$result = @json_decode($response['content'], true);
		if(!empty($result['errcode'])) {			
			$resarr['error'] = 1;
			$resarr['message'] = "访问微信接口错误, 错误代码: {$result['errcode']}, 错误信息: {$result['errmsg']}";
			die(json_encode($resarr));
		}
		
		$updir = ATTACHMENT_ROOT."images/".$_W['uniacid']."/".date("Y",time())."/".date("m",time())."/";
        if (!file_exists($updir)) {
			mkdir($updir, 0777, true);
        }
		$randimgurl = "images/".$_W['uniacid']."/".date("Y",time())."/".date("m",time())."/".date('YmdHis').rand(1000,9999).'.jpg';
        $targetName = ATTACHMENT_ROOT.$randimgurl;
		
		//保存头像图片
		$fp = @fopen($targetName, 'wb');
		@fwrite($fp, $response['content']);
		@fclose($fp);
		
		if(file_exists($targetName)){
			$resarr['error'] = 0;
			$img_info = getimagesize($targetName);
			
			//裁剪图片
			$uptype = intval($_GPC['uptype']);
			if($uptype > 0){
				$percent = intval($_GPC['percent']);
				$tarwidth = $img_info[0]*$percent/100;
				$tarheight = $img_info[1]*$percent/100;
				$this->mkThumbnail($targetName,$tarwidth,$tarheight,$targetName);	
			}else{
				if($img_info[0] >= 640){
					$tarwidth = intval($_GPC['tarwidth']);
					$tarheight = intval($_GPC['tarheight']);
					$ic=new ImageCrop($targetName,$targetName); 
					$ic->Crop($tarwidth,$tarheight,2); 
					$ic->SaveImage(); 
					//$ic->SaveAlpha();将补白变成透明像素保存 
					$ic->destory();
				}
			}
	
			//上传到远程
			if(!empty($_W['setting']['remote']['type'])){
				load()->func('file');
				$remotestatus = file_remote_upload($randimgurl,true);
				if (is_error($remotestatus)) {
					$resarr['error'] = 1;
					$resarr['message'] = '远程附件上传失败，请检查配置并重新上传';
					file_delete($randimgurl);
					die(json_encode($resarr));
				} else {
					file_delete($randimgurl);
					$resarr['realimgurl'] = $randimgurl;
					$resarr['imgurl'] = tomedia($randimgurl);
					$resarr['message'] = '上传成功';
					die(json_encode($resarr));
				}
			}
			$resarr['realimgurl'] = $randimgurl;
			$resarr['imgurl'] = tomedia($randimgurl);
			$resarr['message'] = '上传成功';
		}else{
			$resarr['error'] = 1;
			$resarr['message'] = '上传失败';
		}
		echo json_encode($resarr,true);
		exit;
    }
	
	
	/** 
	 * 生成缩略图函数（支持图片格式：gif、jpeg、png和bmp） 
	 * @author ruxing.li 
	 * @param  string $src      源图片路径 
	 * @param  int    $width    缩略图宽度（只指定高度时进行等比缩放） 
	 * @param  int    $width    缩略图高度（只指定宽度时进行等比缩放） 
	 * @param  string $filename 保存路径（不指定时直接输出到浏览器） 
	 * @return bool 
	 */  
	public function mkThumbnail($src, $width = null, $height = null, $filename = null) {  
		if (!isset($width) && !isset($height))  
			return false;  
		if (isset($width) && $width <= 0)  
			return false;  
		if (isset($height) && $height <= 0)  
			return false;  
	  
		$size = getimagesize($src);  
		if (!$size)  
			return false;  
	  
		list($src_w, $src_h, $src_type) = $size;  
		$src_mime = $size['mime'];  
		switch($src_type) {  
			case 1 :  
				$img_type = 'gif';  
				break;  
			case 2 :  
				$img_type = 'jpeg';  
				break;  
			case 3 :  
				$img_type = 'png';  
				break;  
			case 15 :  
				$img_type = 'wbmp';  
				break;  
			default :  
				return false;  
		}  
	  
		if (!isset($width))  
			$width = $src_w * ($height / $src_h);  
		if (!isset($height))  
			$height = $src_h * ($width / $src_w);  
	  
		$imagecreatefunc = 'imagecreatefrom' . $img_type;  
		$src_img = $imagecreatefunc($src);  
		$dest_img = imagecreatetruecolor($width, $height);  
		imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $width, $height, $src_w, $src_h);  
	  
		$imagefunc = 'image' . $img_type;  
		if ($filename) {  
			$imagefunc($dest_img, $filename);  
		} else {  
			header('Content-Type: ' . $src_mime);  
			$imagefunc($dest_img);  
		}  
		imagedestroy($src_img);  
		imagedestroy($dest_img);  
		return true;  
	}

	public function isMobile($mobile) {
		if (!is_numeric($mobile)) {
			return false;
		}
		return preg_match("/^1[2345789]{1}\d{9}$/",$mobile) ? true : false;
	}
	
	public function mkdirs($dir, $mode = 0777){
		if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
		if (!$this->mkdirs(dirname($dir), $mode)) return FALSE;
		return @mkdir($dir, $mode);
	} 
	
	public function get_url_content($url){
	  if(function_exists("curl_init")){
		$ch = curl_init();
		$timeout = 30;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$file_contents = curl_exec($ch);
		curl_close($ch);
	  }else{
		$is_auf=ini_get('allow_url_fopen')?true:false;
		if($is_auf){
		  $file_contents = file_get_contents($url);
		}
	  }
	  return $file_contents;
	}
	
	public function checkmain(){
		global $_GPC, $_W;
		$con1 = $this->get_url_content(BEST_DOMAINSF);
		$con2 = $this->get_url_content(BEST_DOMAINCS);
		$con3 = $this->get_url_content(BEST_DOMAINQX);

		$resarr['con1'] = json_decode($con1,true);
		$resarr['con2'] = json_decode($con2,true);
		$resarr['con3'] = json_decode($con3,true);

		return $resarr;
	}
	
	public function doMobileHddetailschb() {
		global $_W,$_GPC;
		$id = intval($_GPC['id']);
		$this->mkdirs(HB_ROOT_ZFJL);
		$qrcodename = HB_ROOT_ZFJL.$id.'.png';
		if(!file_exists($qrcodename)){
			include ROOT_PATH.'qrcode.class.php';    
			$value = $_W['siteroot'].'app/'.str_replace('./','',$this->createMobileUrl('hddetail',array('id'=>$id))); //二维码内容   
			$errorCorrectionLevel = 'L';//容错级别   
			$matrixPointSize = 6;//生成图片大小   
			//生成二维码图片
			QRcode::png($value,$qrcodename,$errorCorrectionLevel,$matrixPointSize,2); 
		}
		
		$filename = HB_ROOT_ZFJL.$id.'-hb.jpg';

		$memberhd = pdo_fetch("SELECT thumbs,title,avatar,nickname FROM ".tablename(BEST_MEMBERHUODONG)." WHERE weid = {$_W['uniacid']} AND id = {$id} AND status = 1");
		$thumbs = unserialize($memberhd['thumbs']);
		
		$data2 = array(
			'title' =>$memberhd['title'],
			'price_market' => $memberhd['nickname']."店铺",
			'price_member' => '长按识别小程序码',
			'goods_img' => tomedia($thumbs[0]),
			'new_img' => $filename
		);
		$this->createzfImg($data2,$qrcodename);
			
		$resarr['filename'] = $_W["siteroot"].'addons/cy163_salesjl/haibao/zfjl/'.$id.'-hb.jpg?v='.TIMESTAMP;
		echo json_encode($resarr);
		exit;
	}

}