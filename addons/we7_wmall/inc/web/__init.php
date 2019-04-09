<?php 







defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
mload()->func("web");
mload()->func("tpl.web");
mload()->model("common");
mload()->model("store");
mload()->model("order");
$_W["we7_wmall"]["global"] = get_global_config();
if( $_W["we7_wmall"]["global"]["development"] == 1 ) 
{
    ini_set("display_errors", "1");
    error_reporting(30719 ^ 8);
}

$_W["is_agent"] = is_agent();
$_W["agentid"] = intval($_W["agentid"]);
if( $_W["is_agent"] ) 
{
    mload()->model("agent");
    $_W["agents"] = get_agents();
}

$_W["we7_wmall"]["config"] = get_system_config();
if( empty($_W["page"]["copyright"]["sitename"]) ) 
{
    $_W["page"]["copyright"]["sitename"] = $_W["we7_wmall"]["config"]["mall"]["title"];
}

$mobile_template = $_W["we7_wmall"]["config"]["mall"]["template_mobile"];
if( empty($mobile_template) ) 
{
    $mobile_template = "default";
}

define("WE7_WMALL_TPL_ROOT", WE7_WMALL_LOCAL . "/template/mobile/wmall/" . $mobile_template . "/");
define("WE7_WMALL_TPL_URL", WE7_WMALL_URL . "/template/mobile/wmall/" . $mobile_template . "/");
$permissions = array( "manager" => array( "controller" => array( "system" ) ), "operator" => array( "controller" => array( "system", "perm" ) ), "merchanter" => array( "controller" => array( "common", "store", "oauth" ) ), "agenter" => array( "controller" => array( "common", "order", "paycenter", "merchant", "store", "dashboard", "deliveryer", "clerk", "memmber", "finance", "config", "service", "plugin", "statcenter", "oauth", "plugin", "errander", "bargain", "agent", "diypage" ) ) );
if( !empty($_W["isfounder"]) ) 
{
    $_W["role"] = "founder";
}

$permission = true;
if( $_W["role"] == "manager" ) 
{
    if( in_array($_W["_controller"], $permissions["manager"]["controller"]) ) 
    {
        $permission = false;
    }

}
else
{
    if( $_W["role"] == "operator" ) 
    {
        if( in_array($_W["_controller"], $permissions["operator"]["controller"]) ) 
        {
            $permission = false;
        }

    }
    else
    {
        if( $_W["role"] == "merchanter" ) 
        {
            if( !in_array($_W["_controller"], $permissions["merchanter"]["controller"]) ) 
            {
                $permission = false;
            }

        }
        else
        {
            if( $_W["role"] == "agenter" && !in_array($_W["_controller"], $permissions["agenter"]["controller"]) && !defined("IN_GOHOME") ) 
            {
                $permission = false;
            }

        }

    }

}

if( !$permission ) 
{
    if( $_W["ispost"] ) 
    {
        imessage(error(-1, "您没有权限进行该操作"), "", "ajax");
    }

    imessage("您没有权限进行该操作", "", "info");
}

$_W["perms"] = "all";
if( $_W["role"] == "operator" ) 
{
    $user = get_user();
    if( empty($user["status"]) ) 
    {
        imessage("您的账号已禁用，请联系管理员！", "", "error");
    }

    $_W["perms"] = $user["perms"];
    $perm = (string) $_W["_controller"] . "." . $_W["_action"];
    if( !check_perm($perm, true) ) 
    {
        $_W["_plugin"]["name"] = $_W["_controller"];
        imessage("您没有权限进行该操作！", "", "error");
    }

}

if( defined("IN_MERCHANT") ) 
{
    if( !in_array($_W["role"], array( "manager", "operator", "founder", "merchanter", "agenter" )) && empty($_W["we7_wmall"]["store"]) && (empty($_W["_action"]) || $_W["_op"] != "login") ) 
    {
        imessage("抱歉，您无权进行该操作，请先登录！", iurl("store/oauth/login"), "info");
    }

    if( $_W["_op"] != "login" ) 
    {
        if( $_W["role"] == "merchanter" ) 
        {
            $sid = intval($_W["we7_wmall"]["store"]["id"]);
        }
        else
        {
            if( !empty($_GPC["_sid"]) ) 
            {
                $sid = intval($_GPC["_sid"]);
                isetcookie("__sid", $sid, 86400);
            }
            else
            {
                $sid = intval($_GPC["__sid"]);
            }

            if( $_GPC["add_store"] == 1 ) 
            {
                $sid = 0;
            }

        }

        $_W["we7_wmall"]["sid"] = $sid;
        isetcookie("__sid", $sid, 86400);
        $store = pdo_get("tiny_wmall_store", array( "uniacid" => $_W["uniacid"], "id" => $sid ));
        $store["data"] = iunserializer($store["data"]);
        $_W["store"] = $store;
        if( $_W["is_agent"] && $_W["role"] == "agenter" && $store["agentid"] != $_W["agentid"] ) 
        {
            imessage("您好," . $_W["we7_wmall"]["agent"]["realname"] . ",该门店不属于您的管辖,您无权操作", referer(), "error");
        }

        $_W["agentid"] = $store["agentid"];
        if( !$_GPC["add_store"] && empty($store) ) 
        {
            imessage("门店不存在或已删除！", referer(), "error");
        }

        if( $_W["role"] == "agenter" && $_W["store"]["agentid"] != $_W["agentid"] ) 
        {
            imessage("该门店不属于您的管辖,您无权操作！", referer(), "error");
        }

    }

}
else
{
    if( defined("IN_AGENT") ) 
    {
        if( !in_array($_W["role"], array( "manager", "founder", "agenter" )) && (empty($_W["_action"]) || $_W["_action"] != "login") ) 
        {
            imessage("抱歉，您无权进行该操作，请先登录！", iurl("oauth/login", array( "agent" => 1 )), "info");
        }

        if( $_W["_action"] != "login" ) 
        {
            if( $_W["role"] == "agenter" ) 
            {
                $agent_id = intval($_W["we7_wmall"]["agent"]["id"]);
            }
            else
            {
                if( !empty($_GPC["_agent_id"]) ) 
                {
                    $agent_id = intval($_GPC["_agent_id"]);
                    isetcookie("__agent_id", $sid, 86400);
                }
                else
                {
                    $agent_id = intval($_GPC["__agent_id"]);
                }

            }

            $_W["agentid"] = $agent_id;
            isetcookie("__agent_id", $agent_id, 86400);
            $_W["agent"] = $agent = pdo_get("tiny_wmall_agent", array( "id" => $agent_id ), array( "id", "title", "area", "realname" ));
            if( empty($agent) ) 
            {
                imessage("代理不存在或已删除！", referer(), "error");
            }

        }

    }

}

$_W["isoperator"] = $_W["role"] == "operator";
$_W["ismanager"] = $_W["role"] == "manager" || !empty($_W["isfounder"]);
$_W["isagenter"] = $_W["role"] == "agenter" || !empty($_W["isfounder"]);
$_W["role_cn"] = "平台创始人";
if( $_W["role"] == "manager" ) 
{
    $_W["role_cn"] = "公众号管理员:" . $_W["user"]["username"];
}
else
{
    if( $_W["role"] == "operator" ) 
    {
        $_W["role_cn"] = "公众号操作员:" . $_W["user"]["username"];
    }
    else
    {
        if( $_W["role"] == "merchanter" ) 
        {
            $_W["role_cn"] = "店铺管理员:" . $_W["user"]["username"];
        }
        else
        {
            if( $_W["role"] == "agenter" ) 
            {
                $_W["role_cn"] = "代理商:" . $_W["agent"]["realname"];
            }

        }

    }

}
?>