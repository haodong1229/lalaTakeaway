<?php  defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$_W["page"]["title"] = "统一收银台";
icheckauth();
$_config = $_W["we7_wmall"]["config"];
$id = intval($_GPC["id"]);
$type = trim($_GPC["order_type"]);
if( empty($id) || empty($type) ) 
{
	imessage("订单id不能为空", "", "error");
}
$tables = array( "takeout" => "tiny_wmall_order", "deliveryCard" => "tiny_wmall_delivery_cards_order", "errander" => "tiny_wmall_errander_order", "recharge" => "tiny_wmall_member_recharge", "freelunch" => "tiny_wmall_freelunch_partaker", "peerpay" => "tiny_wmall_order_peerpay_payinfo", "paybill" => "tiny_wmall_paybill_order", "mealRedpacket_plus" => "tiny_wmall_superredpacket_meal_order" );
$order = pdo_get($tables[$type], array( "uniacid" => $_W["uniacid"], "id" => $id ));
if( empty($order) ) 
{
	imessage("订单不存在或已删除", "", "error");
}
if( !empty($order["is_pay"]) ) 
{
	imessage("该订单已付款", "", "info");
}
$order_sn = ($order["ordersn"] ? $order["ordersn"] : $order["order_sn"]);
$record = pdo_get("tiny_wmall_paylog", array( "uniacid" => $_W["uniacid"], "order_id" => $id, "order_type" => $type, "order_sn" => $order_sn ));
if( empty($record) ) 
{
	$record = array( "uniacid" => $_W["uniacid"], "agentid" => $order["agentid"], "uid" => $_W["member"]["uid"], "order_sn" => $order_sn, "order_id" => $id, "order_type" => $type, "fee" => $order["final_fee"], "status" => 0, "addtime" => TIMESTAMP );
	pdo_insert("tiny_wmall_paylog", $record);
	$record["id"] = pdo_insertid();
}
else 
{
	if( $record["status"] == 1 ) 
	{
		imessage("该订单已支付,请勿重复支付", "", "error");
	}
}
$logo = $_config["mall"]["logo"];
if( $type == "takeout" ) 
{
	store_business_hours_init($order["sid"]);
	$store = pdo_get("tiny_wmall_store", array( "uniacid" => $_W["uniacid"], "id" => $order["sid"] ), array( "title", "logo", "is_rest" ));
	if( $store["is_rest"] == 1 ) 
	{
		imessage(array( "title" => "门店已打烊,换个店铺下单哇！", "btn_text" => "看看其他店铺" ), imurl("wmall/home/index"), "info");
	}
	$logo = $store["logo"];
}
$routers = array( "takeout" => array( "title" => (string) $store["title"] . "-" . $record["order_sn"], "url_pay" => imurl("system/paycenter/pay", array( "id" => $order["id"], "order_type" => "takeout", "type" => 1 ), true), "url_detail" => imurl("wmall/order/index/detail", array( "id" => $order["id"] ), true) ), "errander" => array( "title" => "配送会员卡-" . $record["order_sn"], "url_pay" => imurl("system/paycenter/pay", array( "id" => $order["id"], "order_type" => "errander", "type" => 1 ), true), "url_detail" => imurl("errander/order/detail", array( "id" => $order["id"] ), true) ), "deliveryCard" => array( "title" => "配送会员卡-" . $record["order_sn"], "url_pay" => imurl("deliveryCard/apply/index", array( ), true), "url_detail" => imurl("member/mine", array( ), true) ), "recharge" => array( "title" => "账户充值-" . $record["order_sn"], "url_pay" => imurl("system/paycenter/pay", array( "id" => $order["id"], "order_type" => "recharge", "type" => 1 ), true), "url_detail" => imurl("member/mine", array( ), true) ), "freelunch" => array( "title" => "霸王餐-" . $record["order_sn"], "url_pay" => imurl("system/paycenter/pay", array( "id" => $order["id"], "order_type" => "recharge", "type" => 1 ), true), "url_detail" => imurl("freeLunch/freeLunch/partake_success", array( ), true) ), "peerpay" => array( "title" => "帮人代付-" . $record["order_sn"], "url_pay" => imurl("system/paycenter/pay", array( "id" => $order["id"], "order_type" => "peerpay", "type" => 1 ), true), "url_detail" => imurl("system/paycenter/peerpay/paylist", array( "id" => $paylist["pid"] ), true) ), "paybill" => array( "title" => "买单-" . $record["order_sn"], "url_pay" => imurl("system/paycenter/pay", array( "id" => $order["id"], "order_type" => "paybill", "type" => 1 ), true), "url_detail" => imurl("member/mine", array( ), true) ), "mealRedpacket_plus" => array( "title" => "套餐红包Plus-" . $record["order_sn"], "url_pay" => imurl("system/paycenter/pay", array( "id" => $order["id"], "order_type" => "mealRedpacket_plus", "type" => 1 ), true), "url_detail" => imurl("mealRedpacket/plus/plusOrder", array( ), true) ) );
$title = $routers[$type]["title"];
$data = array( "title" => $title, "logo" => $logo, "fee" => $record["fee"] );
pdo_update("tiny_wmall_paylog", array( "data" => iserializer($data) ), array( "id" => $record["id"] ));
$params = array( "module" => "we7_wmall", "ordersn" => $record["order_sn"], "tid" => $record["order_sn"], "user" => $_W["member"]["uid"], "openid" => $order["openid"], "fee" => $record["fee"], "title" => $title, "order_type" => $type, "orderType" => $order["order_type"], "sid" => $order["sid"], "url_detail" => $routers[$type]["url_detail"] );
$url_pay = $url_detail = "";
if( $type == "takeout" ) 
{
	$config_takeout = $_W["we7_wmall"]["config"]["takeout"]["order"];
	if( is_array($config_takeout) && 0 < $config_takeout["pay_time_limit"] ) 
	{
		$params["pay_endtime"] = $order["addtime"] + $config_takeout["pay_time_limit"] * 60;
		$params["pay_endtime_cn"] = date("Y/m/d H:i:s", $params["pay_endtime"]);
		if( $params["pay_endtime"] < TIMESTAMP ) 
		{
			$params["pay_endtime"] = 0;
		}
	}
}
else 
{
	if( $type == "errander" ) 
	{
		$config_errander = get_plugin_config("errander.order");
		if( is_array($config_errander) && 0 < $config_errander["pay_time_limit"] ) 
		{
			$params["pay_endtime"] = $order["addtime"] + $config_errander["pay_time_limit"] * 60;
			$params["pay_endtime_cn"] = date("Y/m/d H:i:s", $params["pay_endtime"]);
			if( $params["pay_endtime"] < TIMESTAMP ) 
			{
				$params["pay_endtime"] = 0;
			}
		}
	}
}
$log = pdo_get("core_paylog", array( "uniacid" => $_W["uniacid"], "module" => $params["module"], "tid" => $params["tid"] ));
if( empty($log) ) 
{
	$log = array( "uniacid" => $_W["uniacid"], "acid" => $_W["acid"], "openid" => ($params["openid"] ? $params["openid"] : $params["user"]), "module" => $params["module"], "uniontid" => date("YmdHis") . random(14, 1), "tid" => $params["tid"], "fee" => $params["fee"], "card_fee" => $params["fee"], "status" => "0", "is_usecard" => "0" );
	pdo_insert("core_paylog", $log);
}
else 
{
	if( $log["status"] == 1 ) 
	{
		imessage("该订单已支付,请勿重复支付", "", "error");
	}
}
if( $order["final_fee"] == 0 ) 
{
	$params = base64_encode(json_encode($params));
	header("location:" . imurl("system/paycenter/cash/credit", array( "params" => $params )));
	exit();
}
if( $type == "peerpay" ) 
{
	$params = base64_encode(json_encode($params));
	header("location:" . imurl("system/paycenter/cash/wechat", array( "params" => $params )));
	exit();
}
$payment = get_available_payment($type, $order["sid"], false, $order["order_type"]);
if( empty($payment) ) 
{
	imessage("没有有效的支付方式, 请联系网站管理员.", "", "error");
}
if( $type == "takeout" && $order["pay_type"] == "peerpay" ) 
{
	header("location:" . imurl("system/paycenter/peerpay/message", array( "id" => $record["id"] )));
	exit();
}
$pay_type = (!empty($_GPC["pay_type"]) ? trim($_GPC["pay_type"]) : $order["pay_type"]);
if( !is_h5app() && !is_qianfan() && !is_majia() && $pay_type && !$_GPC["type"] && in_array($pay_type, $payment) ) 
{
	$params = base64_encode(json_encode($params));
	header("location:" . imurl("system/paycenter/cash/" . $pay_type, array( "params" => $params )));
	exit();
}
if( is_h5app() ) 
{
	$payinfo = array( "mallName" => $_config["mall"]["title"], "money" => $log["card_fee"], "ordersn" => $log["uniontid"], "desc" => $title, "attach" => (string) $_W["uniacid"] . ":h5app" );
}
else 
{
	if( is_qianfan() ) 
	{
		$type_pay = get_plugin_config("qianfanApp.type_pay_id");
		$payinfo = array( "type" => $type_pay, "item" => array( array( "title" => $title, "cover" => "", "num" => 1, "gold_cost" => 0, "cash_cost" => ($_W["member"]["uid"] == "112813212" ? 0.01 : $log["card_fee"]) ) ), "address" => array( "name" => $order["username"], "mobile" => $order["mobile"], "address" => $order["address"] ), "send_type" => 0, "allow_pay_type" => 14, "uniontid" => $log["uniontid"], "tid" => $params["tid"], "url_pay" => $routers[$type]["url_pay"], "url_detail" => $routers[$type]["url_detail"] );
		include(itemplate("paycenter/qianfan"));
		exit();
	}
	if( is_majia() ) 
	{
		mload()->model("plugin");
		pload()->model("majiaApp");
		$payinfo = array( "uniontid" => $log["uniontid"], "orderNum" => $log["uniontid"], "trade_no" => $log["uniontid"], "title" => $title, "amount" => $log["card_fee"], "money" => $log["card_fee"], "user_id" => $_W["member"]["uid_majia"], "des" => "", "remark" => "", "url_pay" => $routers[$type]["url_pay"], "url_detail" => $routers[$type]["url_detail"] );
		$result = majiapay_build($payinfo);
		if( is_error($result) ) 
		{
			imessage($result["message"], "", "error");
		}
		$payinfo["unionOrderNum"] = $result;
		$payinfo["payWay"] = array( "wallet" => 1, "weixin" => 1, "alipay" => 1 );
		include(itemplate("paycenter/majia"));
		exit();
	}
}
$slides = sys_fetch_slide("paycenter");
include(itemplate("paycenter/index"));
?>