<?php  defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$key = "we7_wmall_member_session_" . $_W["uniacid"];
isetcookie($key, "", -100);
if( $_W["ispost"] ) 
{
	imessage(error(0, "退出成功"), imurl("wmall/auth/login"), "ajax");
}
header("location:" . imurl("wmall/auth/login"));
exit();
?>