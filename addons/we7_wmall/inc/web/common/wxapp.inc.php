<?php  defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$op = trim($_GPC["op"]);
if( $op == "link" ) 
{
	$getScene = trim($_GPC["scene"]);
	if( empty($getScene) ) 
	{
		$getScene = "page";
	}
	($getScene == "vuepage" ? "vuepage" : exit);
}
if( $op == "icon" ) 
{
	$type = trim($_GPC["type"]);
	if( empty($type) ) 
	{
		$type = "wmall";
	}
	include(itemplate("public/wxappIcon"));
}
?>