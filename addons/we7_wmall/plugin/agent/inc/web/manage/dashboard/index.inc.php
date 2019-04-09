<?php 
defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$op = (trim($_GPC["op"]) ? trim($_GPC["op"]) : "index");
if( $op == "index" ) 
{
    $_W["page"]["title"] = "运营概括";
    $stat = array(  );
    $condition = " where uniacid = :uniacid and agentid = :agentid and is_pay = 1 and stat_day = :stat_day and order_type <= 2";
    $params = array( ":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"], ":stat_day" => date("Ymd") );
    $stat["total_wait_handel"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . (string) $condition . " and status = 1", $params));
    $stat["total_wait_delivery"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . (string) $condition . " and status = 3", $params));
    $stat["total_wait_refund"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . (string) $condition . " and refund_status = 1", $params));
    $stat["total_wait_reply"] = intval(pdo_fetchcolumn("select count(*) from " . tablename("tiny_wmall_order") . (string) $condition . " and is_remind = 1", $params));
}

include(itemplate("dashboard/index"));

