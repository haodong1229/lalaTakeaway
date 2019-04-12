<?php  defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$op = (trim($_GPC["op"]) ? trim($_GPC["op"]) : "app");
$_config = get_system_config("app");
$downurls = array( "deliveryer" => array( "ios" => MODULE_URL . "resource/apps/" . $_W["uniacid"] . "/ios/deliveryman_1.0.apk", "android" => MODULE_URL . "resource/apps/" . $_W["uniacid"] . "/android/deliveryman_1.0.apk", "apk" => MODULE_ROOT . "/resource/apps/" . $_W["uniacid"] . "/android/deliveryman_1.0.apk" ) );
if( $op == "app" ) 
{
	$_W["page"]["title"] = "配送员app设置";
	if( $_W["ispost"] ) 
	{
		if( $_GPC["form_type"] == "setting_app" ) 
		{
			$data = array( "serial_sn" => trim($_GPC["deliveryer"]["serial_sn"]), "push_key" => trim($_GPC["deliveryer"]["push_key"]), "push_secret" => trim($_GPC["deliveryer"]["push_secret"]), "push_tags" => $_config["deliveryer"]["push_tags"], "ios_build_type" => intval($_GPC["deliveryer"]["ios_build_type"]), "android_version" => intval($_GPC["deliveryer"]["android_version"]), "version" => array( "ios" => trim($_GPC["deliveryer"]["version"]["ios"]), "android" => 1 ), "ios_download_link" => trim($_GPC["deliveryer"]["ios_download_link"]), "android_download_link" => MODULE_URL . "resource/apps/" . $_W["uniacid"] . "/android/deliveryman_1.0.apk" );
			if( empty($_config["deliveryer"]["push_tags"]) ) 
			{
				$data["push_tags"] = array( "working" => random(10), "rest" => random(10) );
			}
			set_system_config("app.deliveryer", $data);
			imessage(error(0, "设置app参数成功"), "refresh", "ajax");
		}
		else 
		{
			if( $_GPC["form_type"] == "upload_file" ) 
			{
				set_time_limit(0);
				$file = upload_file($_FILES["file"], "app", "deliveryman_1.0.apk", "resource/apps/" . $_W["uniacid"] . "/android/");
				if( is_error($file) ) 
				{
					imessage(error(-1, $file["message"]), "", "ajax");
				}
				imessage(error(0, "上传APP安装包成功"), "refresh", "ajax");
			}
		}
	}
	$app = get_system_config("app.deliveryer");
}
include(itemplate("app"));
?>