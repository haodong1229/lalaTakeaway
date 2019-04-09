<?php 
defined("IN_IA") or exit( "Access Denied" );
/*淘宝柠檬鱼科技 https://shop486845690.taobao.com*/

function qrcode_wxapp_url($params)
{
    global $_W;
    $path = "we7_wmall/wxappqrcode/" . $params["type"] . "/" . $_W["uniacid"] . "/" . $params["name"] . ".png";
    $allpath = tomedia($path);
    if( ifile_exists($allpath) ) 
    {
        return $allpath;
    }

    $params = array( "url" => $params["url"], "scene" => $params["scene"], "name" => $path );
    $res = qrcode_wxapp_build($params);
    if( is_error($res) ) 
    {
        return error(-1, "生成小程序码失败:" . $res["message"]);
    }

    return tomedia($path);
}

function qrcode_wxapp_build($params = array(  ))
{
    $url = $params["url"];
    if( empty($url) ) 
    {
        return error(-1, "链接不能为空");
    }

    $scene = $params["scene"];
    mload()->model("cloud");
    $content = cloud_w_wxapp_getanywxacode($url, $scene);
    if( is_error($content) ) 
    {
        return $content;
    }

    $name = ifile_write($content, $params["name"]);
    return $name;
}

function qrcode_wechat_build($params = array(  ))
{
    global $_W;
    $scene_str = $params["scene_str"];
    $qrcode = pdo_fetch("SELECT * FROM " . tablename("qrcode") . " WHERE uniacid = :uniacid AND scene_str = :scene_str", array( ":uniacid" => $_W["uniacid"], ":scene_str" => $scene_str ));
    if( !empty($qrcode) ) 
    {
        $rule_keyword = pdo_get("rule_keyword", array( "uniacid" => $_W["uniacid"], "content" => $qrcode["keyword"] ));
        if( !empty($rule_keyword) ) 
        {
            $rule = pdo_get("rule", array( "uniacid" => $_W["uniacid"], "id" => $rule_keyword["rid"] ));
            if( !empty($rule) ) 
            {
                return $qrcode;
            }

        }

    }

    if( $_W["account"]["level"] != 4 ) 
    {
        return error(-1, "您的公众号不是认证服务号，没有创建二维码的权限");
    }

    if( empty($params["qrcode_type"]) ) 
    {
        return error(-1, "二维码类型不能为空");
    }

    if( empty($params["module"]) ) 
    {
        $params["module"] = "we7_wmall";
    }

    if( empty($params["scene_str"]) ) 
    {
        return error(-1, "二维码场景值不能为空");
    }

    if( empty($params["keyword"]) ) 
    {
        $params["keyword"] = $params["scene_str"];
    }

    $acc = WeAccount::create($_W["acid"]);
    if( $params["qrcode_type"] == "fixed" ) 
    {
        $barcode = array( "expire_seconds" => "", "action_name" => "QR_LIMIT_STR_SCENE", "action_info" => array( "scene" => array( "scene_str" => $params["scene_str"] ) ) );
        $result = $acc->barCodeCreateFixed($barcode);
    }
    else
    {
        $barcode = array( "expire_seconds" => ($params["expire_seconds"] ? $params["expire_seconds"] : 2592000), "action_name" => "QR_STR_SCENE", "action_info" => array( "scene" => array( "scene_str" => $params["scene_str"] ) ) );
        $result = $acc->barCodeCreateDisposable($barcode);
    }

    if( is_error($result) ) 
    {
        return error(-1, "生成微信二维码出错,错误详情:" . $result["message"]);
    }

    $qrcode = array( "uniacid" => $_W["uniacid"], "acid" => $_W["acid"], "qrcid" => "", "scene_str" => $params["scene_str"], "keyword" => $params["keyword"], "name" => $params["name"], "model" => ($params["qrcode_type"] == "fixed" ? 1 : 2), "ticket" => $result["ticket"], "url" => $result["url"], "expire" => $result["expire_seconds"], "createtime" => TIMESTAMP, "status" => "1", "type" => "we7_wmall" );
    pdo_insert("qrcode", $qrcode);
    $rule = array( "uniacid" => $_W["uniacid"], "name" => $params["name"], "module" => "we7_wmall", "status" => 1 );
    pdo_insert("rule", $rule);
    $rid = pdo_insertid();
    $keyword = array( "uniacid" => $_W["uniacid"], "module" => "we7_wmall", "content" => $params["keyword"], "status" => 1, "type" => 1, "displayorder" => 1, "rid" => $rid );
    pdo_insert("rule_keyword", $keyword);
    $kid = pdo_insertid();
    $data = array( "uniacid" => $_W["uniacid"], "sid" => 0, "type" => ($params["type"] ? $params["type"] : "spread"), "rid" => $rid, "table_id" => 0, "extra" => iserializer(array( "uid" => $params["uid"] )) );
    pdo_insert("tiny_wmall_reply", $data);
    $reply_id = pdo_insertid();
    $qrcode = array( "ticket" => $result["ticket"], "url" => $result["url"] );
    return $qrcode;
}

function qrcode_url($ticket)
{
    return "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . urlencode($ticket);
}

function qrcode_normal_build($params)
{
    global $_W;
    if( empty($params["url"]) ) 
    {
        return error(-1, "链接不能为空");
    }

    $path = MODULE_ROOT . "/resource/poster/qrcode/" . $_W["uniacid"] . "/";
    if( !is_dir($path) ) 
    {
        load()->func("file");
        mkdirs($path);
    }

    $file = md5(base64_encode($params["url"])) . ".jpg";
    $qrcode_file = $path . $file;
    if( !is_file($qrcode_file) ) 
    {
        require(IA_ROOT . "/framework/library/qrcode/phpqrcode.php");
        QRcode::png($params["url"], $qrcode_file, QR_ECLEVEL_L, 4);
    }

    return (string) $_W["siteroot"] . "addons/we7_wmall/resource/poster/qrcode/" . $_W["uniacid"] . "/" . $file;
}


