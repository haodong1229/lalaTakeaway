<?php  defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$_W["page"]["title"] = "新建自定义菜单";
$id = intval($_GPC["id"]);
if( 0 < $id ) 
{
	$_W["page"]["title"] = "编辑菜单";
}
if( $_W["ispost"] ) 
{
	$data = $_GPC["menu"];
	$data = base64_encode(json_encode($data));
	set_plugin_config("managerApp.menu", $data);
	imessage(error(0, "添加成功"), iurl("managerApp/menu", array( "id" => $id )), "ajax");
}
mload()->model("manage");
$menu = get_manager_menu();
include(itemplate("menu"));
?>