<?php  defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
mload()->model("deliveryer");
$op = (trim($_GPC["op"]) ? trim($_GPC["op"]) : "stat");
$deliveryer_alls = deliveryer_all();
if( $op == "stat" ) 
{
	$_W["page"]["title"] = "接单统计";
	$condition = " where uniacid = :uniacid and agentid = :agentid";
	$params = array( ":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"] );
	$deliveryer_id = intval($_GPC["deliveryer_id"]);
	if( 0 < $deliveryer_id ) 
	{
		$condition .= " and id = :id";
		$params[":id"] = $deliveryer_id;
	}
	$deliveryers = pdo_fetchall("select * from " . tablename("tiny_wmall_deliveryer") . $condition . " order by id desc", $params);
	if( !empty($_GPC["addtime"]) ) 
	{
		$starttime = strtotime($_GPC["addtime"]["start"]);
		$endtime = strtotime($_GPC["addtime"]["end"]) + 86399;
	}
	else 
	{
		$today = strtotime(date("Y-m-d"));
		$starttime = strtotime("-15 day", $today);
		$endtime = $today + 86399;
	}
	$condition = " where uniacid = :uniacid and agentid = :agentid and addtime >= :starttime and addtime <= :endtime and deliveryer_id > 0 and delivery_type = 2";
	$params = array( ":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":starttime" => $starttime, ":endtime" => $endtime );
	if( 0 < $deliveryer_id ) 
	{
		$condition .= " and deliveryer_id = :deliveryer_id";
		$params[":deliveryer_id"] = $deliveryer_id;
	}
	$finish = pdo_fetchall("select count(*) as total,deliveryer_id from " . tablename("tiny_wmall_order") . $condition . " and delivery_status = 5 group by deliveryer_id", $params, "deliveryer_id");
	$timeout = pdo_fetchall("select count(*) as total,deliveryer_id from " . tablename("tiny_wmall_order") . $condition . " and delivery_status = 5 and is_timeout = 1 group by deliveryer_id", $params, "deliveryer_id");
	$wait_pickup = pdo_fetchall("select count(*) as total,deliveryer_id from " . tablename("tiny_wmall_order") . $condition . " and delivery_status = 7 group by deliveryer_id", $params, "deliveryer_id");
	$deliverying = pdo_fetchall("select count(*) as total,deliveryer_id from " . tablename("tiny_wmall_order") . $condition . " and delivery_status = 4 group by deliveryer_id", $params, "deliveryer_id");
}
if( $op == "list" ) 
{
	$_W["page"]["title"] = "接单记录";
	if( !empty($_GPC["addtime"]) ) 
	{
		$starttime = strtotime($_GPC["addtime"]["start"]);
		$endtime = strtotime($_GPC["addtime"]["end"]) + 86399;
	}
	else 
	{
		$today = strtotime(date("Y-m-d"));
		$starttime = strtotime("-15 day", $today);
		$endtime = $today + 86399;
	}
	$condition = " where uniacid = :uniacid and agentid = :agentid and addtime >= :starttime and addtime <= :endtime and deliveryer_id > 0 and delivery_type = 2";
	$params = array( ":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":starttime" => $starttime, ":endtime" => $endtime );
	$deliveryer_id = intval($_GPC["deliveryer_id"]);
	if( 0 < $deliveryer_id ) 
	{
		$condition .= " and deliveryer_id = :deliveryer_id";
		$params[":deliveryer_id"] = $deliveryer_id;
	}
	$pindex = max(1, intval($_GPC["page"]));
	$psize = 15;
	$total = pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . $condition, $params);
	$orders = pdo_fetchall("select * from " . tablename("tiny_wmall_order") . $condition . " order by id desc limit " . ($pindex - 1) * $psize . "," . $psize, $params);
	if( !empty($orders) ) 
	{
		foreach( $orders as &$val ) 
		{
			$val["time_interval"] = order_time_analyse($val["id"]);
		}
	}
	$pager = pagination($total, $pindex, $psize);
	$order_delivery_status = order_delivery_status();
}
include(itemplate("order/records"));
?>