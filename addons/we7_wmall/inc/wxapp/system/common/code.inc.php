<?php 
defined("IN_IA") or exit( "Access Denied" );
mload()->model("sms");
global $_W;
global $_GPC;
$sid = intval($_GPC["sid"]);
$mobile = trim($_GPC["mobile"]);
if( $mobile == "" ) 
{
    imessage(error(-1, "请输入手机号"), "", "ajax");
}

if( !is_validMobile($mobile) ) 
{
    imessage(error(-1, "手机号格式错误"), "", "ajax");
}

$captcha = trim($_GPC["captcha"]);
if( !empty($captcha) ) 
{
    $hash = md5($captcha . $_W["config"]["setting"]["authkey"]);
    if( $_GPC["__code"] != $hash ) 
    {
        imessage(error(-1, "图形验证码错误, 请重新输入"), "", "ajax");
    }

}

$sql = "DELETE FROM " . tablename("uni_verifycode") . " WHERE `createtime`<" . (TIMESTAMP - 1800);
pdo_query($sql);
$sql = "SELECT * FROM " . tablename("uni_verifycode") . " WHERE `receiver`=:receiver AND `uniacid`=:uniacid";
$pars = array(  );
$pars[":receiver"] = $mobile;
$pars[":uniacid"] = $_W["uniacid"];
$row = pdo_fetch($sql, $pars);
$record = array(  );
if( !empty($row) ) 
{
    if( 20 <= $row["total"] ) 
    {
        imessage(error(-1, "您的操作过于频繁,请稍后再试"), "", "ajax");
    }

    $code = $row["verifycode"];
    $record["total"] = $row["total"] + 1;
}
else
{
    $code = random(6, true);
    $record["uniacid"] = $_W["uniacid"];
    $record["receiver"] = $mobile;
    $record["verifycode"] = $code;
    $record["total"] = 1;
    $record["createtime"] = TIMESTAMP;
}

if( !empty($row) ) 
{
    pdo_update("uni_verifycode", $record, array( "id" => $row["id"] ));
}
else
{
    pdo_insert("uni_verifycode", $record);
}

$content = array( "code" => $code );
$config_sms = $_W["we7_wmall"]["config"]["sms"]["template"];
$result = sms_send($config_sms["verify_code_tpl"], $mobile, $content, $sid);
if( is_error($result) ) 
{
    slog("alidayuSms", "阿里大鱼短信通知验证码", $content, $result["message"]);
    imessage(error(-1, $result["message"]), "", "ajax");
}

imessage(error(0, "success"), "", "ajax");

