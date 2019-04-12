<?php  defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$op = (trim($_GPC["op"]) ? trim($_GPC["op"]) : "app");
$_config = get_system_config("app");
$downurls = array( "customer" => array( "ios" => MODULE_URL . "resource/apps/" . $_W["uniacid"] . "/ios/customer.apk", "android" => MODULE_URL . "resource/apps/" . $_W["uniacid"] . "/android/customer.apk" ) );
if( $op == "app" ) 
{
	$_W["page"]["title"] = "顾客app设置";
	if( $_W["ispost"] ) 
	{
		$login = array( "qq" => intval($_GPC["qq"]), "wx" => intval($_GPC["wx"]) );
		$data = array( "webtype" => trim($_GPC["webtype"]), "iosstatus" => intval($_GPC["iosstatus"]), "iosurl" => trim($_GPC["iosurl"]), "iosmenu" => intval($_GPC["iosmenu"]), "serial_sn" => trim($_GPC["customer"]["serial_sn"]), "appid" => trim($_GPC["customer"]["appid"]), "key" => trim($_GPC["customer"]["key"]), "login" => $login, "ios_download_link" => trim($_GPC["customer"]["ios_download_link"]), "android_download_link" => MODULE_URL . "resource/apps/" . $_W["uniacid"] . "/android/customer.apk" );
		set_system_config("app.customer", $data);
		imessage(error(0, "设置app参数成功"), "refresh", "ajax");
	}
	$app = get_system_config("app.customer");
	$menus = pdo_getall("tiny_wmall_diypage_menu", array( "uniacid" => $_W["uniacid"], "version" => 2 ), array( "id", "name" ));
}
include(itemplate("app"));
?>