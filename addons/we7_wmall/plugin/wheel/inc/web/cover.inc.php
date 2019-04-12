<?php  defined("IN_IA") or exit( "Access Denied" );
mload()->model("deliveryer");
global $_W;
global $_GPC;
$op = (trim($_GPC["op"]) ? trim($_GPC["op"]) : "index");
if( $op == "index" ) 
{
	$_W["page"]["title"] = "入口链接";
	$records_url = ivurl("pages/wheel/record", array( ), true);
}
include(itemplate("cover"));
?>