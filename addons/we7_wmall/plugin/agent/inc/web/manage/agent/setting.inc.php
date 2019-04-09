<?php 
defined("IN_IA") or exit( "Access Denied" );
mload()->model("agent");
global $_W;
global $_GPC;
$_W["page"]["title"] = "代理账号设置";
if( $_W["ispost"] ) 
{
    $mobile = trim($_GPC["mobile"]);
    if( !is_validMobile($mobile) ) 
    {
        imessage(error(-1, "手机号格式错误"), referer(), "ajax");
    }

    $is_exist = pdo_fetch("select id from " . tablename("tiny_wmall_agent") . " where uniacid = :uniacid and id != :id and mobile = :mobile", array( ":uniacid" => $_W["uniacid"], ":id" => $_W["agentid"], ":mobile" => $mobile ));
    if( !empty($is_exist) ) 
    {
        imessage(error(-1, "该手机号已被其他代理注册"), referer(), "ajax");
    }

    $data = array( "title" => trim($_GPC["title"]), "realname" => trim($_GPC["realname"]), "mobile" => $mobile );
    $password = trim($_GPC["password"]);
    if( !empty($password) ) 
    {
        $data["salt"] = random(6);
        $data["password"] = md5(md5($data["salt"] . $password) . $data["salt"]);
    }

    pdo_update("tiny_wmall_agent", $data, array( "id" => $_W["agentid"], "uniacid" => $_W["uniacid"] ));
    imessage(error(0, "代理账号设置成功"), referer(), "ajax");
}

$agent = get_agent($_W["agentid"], array( "title", "realname", "mobile", "password" ));
include(itemplate("agent/setting"));

