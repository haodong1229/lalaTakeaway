<?php 
defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$ta = (trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index");
mload()->model("plugin");
pload()->model("kanjia");
if( $ta == "post" ) 
{
    $config = get_plugin_config("gohome.basic");
    if( $config["status"]["kanjia"] != 1 ) 
    {
        imessage(error(-1, "砍价功能暂时关闭，详情请联系平台管理员"), "", "ajax");
    }

    $id = intval($_GPC["id"]);
    if( $_W["ispost"] ) 
    {
        $value = $_GPC["data"];
        $data = array( "uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "sid" => $sid, "cateid" => intval($value["cateid"]), "oldprice" => floatval($value["oldprice"]), "price" => floatval($value["price"]), "submitmoneylimit" => intval($value["submitmoneylimit"]), "starttime" => strtotime($value["starttime_cn"]), "endtime" => strtotime($value["endtime_cn"]), "helplimit" => intval($value["helplimit"]), "dayhelplimit" => intval($value["dayhelplimit"]), "joinlimit" => intval($value["joinlimit"]), "usestatus" => intval($value["usestatus"]), "falsejoinnum" => intval($value["falsejoinnum"]), "falselooknum" => intval($value["falselooknum"]), "falsesharenum" => intval($value["falsesharenum"]), "displayorder" => intval($value["displayorder"]), "status" => intval($value["status"]), "code" => trim($value["code"]), "name" => trim($value["name"]), "total" => intval($value["total"]), "thumb" => trim($value["thumb"]), "unit" => trim($value["unit"]), "detail" => htmlspecialchars_decode($value["detail"]), "addtime" => TIMESTAMP );
        $data["thumbs"] = array(  );
        if( !empty($value["thumbs"]) ) 
        {
            foreach( $value["thumbs"] as $val ) 
            {
                if( empty($val) ) 
                {
                    continue;
                }

                $data["thumbs"][] = trim($val);
            }
        }

        $data["thumbs"] = iserializer($data["thumbs"]);
        $data["rules"] = array(  );
        foreach( $value["rules"] as $key => $val ) 
        {
            $data["rules"][] = array( "range" => $val["range"], "range_start" => $val["range_start"], "range_end" => $val["range_end"] );
        }
        if( empty($data["rules"]) ) 
        {
            imessage(error(-1, "没有设置有效的砍价规则"), "", "ajax");
        }
        else
        {
            $data["rules"] = iserializer($data["rules"]);
        }

        $data["share"] = array(  );
        $share_thumb = (trim($value["share_thumb"]) ? trim($value["share_thumb"]) : trim($value["thumb"]));
        $share_title = (trim($value["share_title"]) ? trim($value["share_title"]) : trim($value["name"]));
        $share_detail = trim($value["share_detail"]);
        $data["share"] = array( "share_thumb" => $share_thumb, "share_title" => $share_title, "share_detail" => $share_detail );
        $data["share"] = iserializer($data["share"]);
        if( !empty($id) ) 
        {
            pdo_update("tiny_wmall_kanjia", $data, array( "uniacid" => $_W["uniacid"], "id" => $id ));
        }
        else
        {
            pdo_insert("tiny_wmall_kanjia", $data);
        }

        imessage(error(0, "编辑商品成功"), iurl("store/kanjia/activity/list"), "ajax");
    }

    if( $id ) 
    {
        $item = kanjia_get_activity($id);
        if( empty($item) ) 
        {
            imessage(error(-1, "商品不存在或已删除"), "", "ajax");
        }

    }

    $category = kanjia_get_categorys();
    $item["category_title"] = $category[$item["cateid"]]["title"];
    $category = array_values($category);
    $result = array( "records" => $item, "category" => $category );
    imessage(error(0, $result), "", "ajax");
    return 1;
}
else
{
    if( $ta == "list" ) 
    {
        $records = kanjia_get_activitylist();
        $result = array( "records" => $records );
        imessage(error(0, $result), "", "ajax");
    }
    else
    {
        if( $ta == "del" ) 
        {
            $id = $_GPC["id"];
            if( empty($id) ) 
            {
                imessage(error(-1, "商品不存在或已被删除"), "", "ajax");
            }

            pdo_delete("tiny_wmall_kanjia", array( "uniacid" => $_W["uniacid"], "sid" => $sid, "id" => $id ));
            imessage(error(0, "删除商品成功"), "", "ajax");
        }

    }

}
?>