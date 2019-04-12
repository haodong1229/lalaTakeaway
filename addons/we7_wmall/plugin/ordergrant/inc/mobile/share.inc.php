<?php  defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$op = (trim($_GPC["op"]) ? trim($_GPC["op"]) : "list");
if( $op == "list" ) 
{
	$_W["page"]["title"] = "订单分享";
	$condition = " where a.uniacid = :uniacid and a.is_share = 1";
	$params = array( ":uniacid" => $_W["uniacid"] );
	$id = intval($_GPC["min"]);
	if( 0 < $id ) 
	{
		$condition .= " and a.id < :id";
		$params[":id"] = $id;
	}
	$comments = pdo_fetchall("select a.id as aid, a.*,b.final_fee from " . tablename("tiny_wmall_order_comment") . " as a left join " . tablename("tiny_wmall_order") . " as b on a.oid = b.id" . $condition . " order by a.id desc limit 15", $params, "aid");
	$min = 0;
	if( !empty($comments) ) 
	{
		foreach( $comments as &$row ) 
		{
			$row["score"] = ($row["delivery_service"] + $row["goods_quality"]) * 10;
			$row["avatar"] = (tomedia($row["avatar"]) ? tomedia($row["avatar"]) : WE7_WMALL_TPL_URL . "static/img/head.png");
			$row["addtime"] = date("Y-m-d H:i", $row["addtime"]);
			$row["store"] = pdo_get("tiny_wmall_store", array( "uniacid" => $_W["uniacid"], "id" => $row["sid"] ), array( "title", "logo", "delivery_time" ));
			$row["logo"] = tomedia($row["store"]["logo"]);
			$row["goods"] = pdo_fetchall("select a.goods_title,a.goods_num,b.thumb from " . tablename("tiny_wmall_order_stat") . " as a left join " . tablename("tiny_wmall_goods") . " as b on a.goods_id = b.id where a.uniacid = :uniacid and a.oid = :oid", array( ":uniacid" => $_W["uniacid"], ":oid" => $row["oid"] ));
			foreach( $row["goods"] as &$val ) 
			{
				$val["thumb"] = tomedia($val["thumb"]);
			}
			$row["activity"] = store_fetch_activity($row["sid"]);
		}
		$min = min(array_keys($comments));
	}
	if( $_W["ispost"] ) 
	{
		$comments = array_values($comments);
		$respon = array( "errno" => 0, "message" => $comments, "min" => $min );
		imessage($respon, "", "ajax");
	}
}
if( $op == "detail" ) 
{
	$id = intval($_GPC["id"]);
	$order = order_fetch($id);
	if( empty($order) ) 
	{
		imessage("订单不存在或已删除", "", "info");
	}
	$store = store_fetch($order["sid"]);
	$_W["page"]["title"] = $store["title"];
	$activity = store_fetch_activity($order["sid"]);
	$goods = order_fetch_goods($order["id"]);
	$discounts = order_fetch_discount($id);
	$comment = pdo_get("tiny_wmall_order_comment", array( "uniacid" => $_W["uniacid"], "oid" => $id ));
	if( !empty($comment["data"]) ) 
	{
		$comment["data"] = iunserializer($comment["data"]);
	}
	$comment["score"] = ($comment["delivery_service"] + $comment["goods_quality"]) * 10;
	$hot_goods = pdo_fetchall("select id,title,price,sailed,thumb from " . tablename("tiny_wmall_goods") . " where uniacid = :uniacid and sid = :sid and is_hot = 1 order by id desc limit 6", array( ":uniacid" => $_W["uniacid"], ":sid" => $order["sid"] ));
}
if( $op == "grant" ) 
{
	$id = intval($_GPC["id"]);
	$order = order_fetch($id);
	if( empty($order) ) 
	{
		imessage(error(-1, "订单不存在或已删除"), "", "ajax");
	}
	$comment = pdo_get("tiny_wmall_order_comment", array( "uniacid" => $_W["uniacid"], "oid" => $id ));
	if( empty($comment) ) 
	{
		imessage(error(-1, "请评论后再进行分享"), "", "ajax");
	}
	if( $comment["is_share"] == 1 ) 
	{
		imessage(error(-1, "该订单已分享,重复分享不获取奖励"), "", "ajax");
	}
	if( TIMESTAMP - $order["endtime"] < $config_share["share_grant_days_limit"] * 86400 ) 
	{
		$grant = 0;
		$sum = pdo_fetchcolumn("select sum(`grant`) from " . tablename("tiny_wmall_order_grant_record") . " where uniacid = :uniacid and uid = :uid and type = 5", array( ":uniacid" => $_W["uniacid"], ":uid" => $_W["member"]["uid"] ));
		if( $sum < $config_share["share_grant_max"] ) 
		{
			$grant = $config_share["share_grant"];
		}
		if( 0 < $grant ) 
		{
			$insert = array( "uniacid" => $_W["uniacid"], "uid" => $_W["member"]["uid"], "oid" => $id, "grant" => $grant, "credittype" => $config_share["grantType"], "type" => 5, "stat_month" => date("Ym"), "addtime" => TIMESTAMP, "mark" => "分享订单奖励" . $grant . $config_share["grantType_cn"] );
			pdo_insert("tiny_wmall_order_grant_record", $insert);
			$log = array( $_W["member"]["uid"], $insert["mark"] );
			member_credit_update($_W["member"]["uid"], $config_share["grantType"], $grant, $log);
		}
	}
	pdo_update("tiny_wmall_order_comment", array( "is_share" => 1 ), array( "uniacid" => $_W["uniacid"], "oid" => $id ));
	imessage(error(0, "分享成功,奖励" . $grant . $config_share["grantType_cn"]), "", "ajax");
}
include(itemplate("share"));
?>