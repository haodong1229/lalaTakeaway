<?php 
defined("IN_IA") or exit( "Access Denied" );
/*淘宝柠檬鱼科技 https://shop486845690.taobao.com*/

function get_majia_agent($key = "")
{
    $userAgent = $_SERVER["HTTP_USER_AGENT"];
    if( empty($userAgent) ) 
    {
        return "";
    }

    $info = strstr($userAgent, "MAGAPPX");
    $info = explode("|", $info);
    $agent = array( "version" => $info[1], "client" => $info[2], "site" => $info[3], "device" => $info[4], "sign" => $info[5], "signCloud" => $info[6], "token" => $info[7] );
    if( !empty($key) ) 
    {
        return $agent[$key];
    }

    return $agent;
}

function get_user_info()
{
    global $_GPC;
    $token = $_GPC["__majia_token"];
    if( empty($token) ) 
    {
        return error(-1, "用户token信息为空");
    }

    $config = get_plugin_config("majiaApp");
    if( empty($config["hostname"]) ) 
    {
        return error(-1, "后台未设置客户端域名");
    }

    if( empty($config["appsecret"]) ) 
    {
        return error(-1, "后台未设置appsecret");
    }

    load()->func("communication");
    $url = "http://" . $config["hostname"] . "/mag/cloud/cloud/getUserInfo?token=" . $token . "&secret=" . $config["appsecret"];
    $response = ihttp_get($url);
    if( is_error($response) ) 
    {
        return $response;
    }

    $result = json_decode($response["content"], true);
    if( $result["success"] != 1 ) 
    {
        return error(-1, (string) $result["code"] . ":" . $result["msg"]);
    }

    return $result["data"];
}

function majiapay_build($params)
{
    global $_W;
    $config = get_plugin_config("majiaApp");
    if( empty($config["appsecret"]) ) 
    {
        return error(-1, "后台未设置appsecret");
    }

    if( empty($config["hostname"]) ) 
    {
        return error(-1, "后台未设置hostname");
    }

    $params = array_elements(array( "title", "uniontid", "amount", "des", "user_id", "remark", "trade_no" ), $params);
    unset($params["uniontid"]);
    $sign_params = array( "uniacid" => $_W["uniacid"], "timestamp" => TIMESTAMP, "trade_no" => $params["trade_no"] );
    $sign_params["sign"] = build_majia_sign($sign_params);
    $params["callback"] = WE7_WMALL_URL . "payment/majia/notify.php?" . http_build_query($sign_params, "&");
    $params["secret"] = $config["appsecret"];
    load()->func("communication");
    $url = "http://" . $config["hostname"] . "/core/pay/pay/unifiedOrder?" . http_build_query($params, "&");
    $response = ihttp_get($url);
    if( is_error($response) ) 
    {
        return $response;
    }

    $result = json_decode($response["content"], true);
    if( $result["success"] != 1 ) 
    {
        return error(-1, (string) $result["code"] . ":" . $result["msg"]);
    }

    return $result["data"]["unionOrderNum"];
}

function get_order_pay_status($order_id)
{
    load()->func("communication");
    $config = get_plugin_config("majiaApp");
    if( empty($config["hostname"]) || empty($config["appsecret"]) ) 
    {
        return error(-1, "请设置客户端域名和appsecret");
    }

    $url = "http://" . $config["hostname"] . "/core/pay/pay/orderStatusQuery?unionOrderNum=" . $order_id . "&secret=" . $config["appsecret"];
    $response = ihttp_get($url);
    if( is_error($response) ) 
    {
        return $response;
    }

    $result = json_decode($response["content"], true);
    if( $result["success"] != 1 ) 
    {
        return error(-1, $result["msg"]);
    }

    return $result["paycode"];
}

function build_majia_sign($params)
{
    global $_W;
    $secret = get_plugin_config("majiaApp.appsecret");
    if( empty($secret) ) 
    {
        return error(-1, "后台没有设置appsecret信息");
    }

    unset($params["sign"]);
    ksort($params);
    $string = "";
    foreach( $params as $key => $val ) 
    {
        if( empty($val) ) 
        {
            continue;
        }

        $string .= (string) $key . "=" . $val . "&";
    }
    $string = trim($string, "&");
    $string = $string . "&secret=" . $secret;
    $string = md5($string);
    $result = strtoupper($string);
    return $result;
}


