<?php 
define("IN_MOBILE", true);
global $_GPC;
require("../../../../framework/bootstrap.inc.php");
require("../../../../addons/we7_wmall/payment/__init.php");
require("../../../../addons/we7_wmall/class/TyAccount.class.php");
$orderid = (trim($_GPC["orderid"]) ? trim($_GPC["orderid"]) : trim($_GPC["dingdanID"]));
if( empty($orderid) ) 
{
    echo "OK";
    exit();
}

$order = pdo_get("tiny_wmall_order", array( "print_sn" => $orderid ));
if( empty($order) ) 
{
    echo "OK";
    exit();
}

$_W["weid"] = $order["uniacid"];
$_W["uniacid"] = $_W["weid"];
$_W["account"] = uni_fetch($_W["uniacid"]);
$_W["uniaccount"] = $_W["account"];
$_W["acid"] = $_W["uniaccount"]["acid"];
$site = WeUtility::createModuleSite("we7_wmall");
if( !is_error($site) ) 
{
    $method = "printResult";
    if( method_exists($site, $method) ) 
    {
        $ret = array(  );
        $ret["uniacid"] = $_W["uniacid"];
        $ret["acid"] = $_W["acid"];
        $ret["result"] = "success";
        $ret["from"] = "notify";
        $ret["order_id"] = $order["id"];
        $ret["order"] = $order;
        $site->$method($ret);
        exit( "success" );
    }

}


