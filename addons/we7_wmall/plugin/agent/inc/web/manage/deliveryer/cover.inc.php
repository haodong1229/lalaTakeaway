<?php  defined("IN_IA") or exit( "Access Denied" );
mload()->model("deliveryer");
global $_W;
global $_GPC;
$op = (trim($_GPC["op"]) ? trim($_GPC["op"]) : "list");
if( $op == "list" ) 
{
	$_W["page"]["title"] = "配送员入口";
	$urls = array( "register" => imurl("delivery/auth/register", array( ), true), "login" => imurl("delivery/auth/login", array( ), true) );
	include(itemplate("deliveryer/cover"));
}
?>