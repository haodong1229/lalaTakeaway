<?php  defined("IN_IA") or exit( "Access Denied" );
mload()->model("plugin");
mload()->model("cloud");
global $_W;
global $_GPC;
$_W["page"]["title"] = "应用中心";
$_W["plugin_types"] = plugin_types();
$plugins = plugin_fetchall();
$perms = get_agent_perm("plugins");
$_W["plugins"] = array( );
foreach( $plugins as $row ) 
{
	if( !empty($perms) && !in_array($row["name"], $perms) ) 
	{
		continue;
	}
	$_W["plugins"][$row["type"]][] = $row;
	$i++;
}
if( !$i ) 
{
	imessage("没有可用的插件,请联系平台管理员开通", "", "info");
}
include(itemplate("plugin/index"));
?>