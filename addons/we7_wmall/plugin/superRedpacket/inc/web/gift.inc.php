<?php 
defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$op = (trim($_GPC["op"]) ? trim($_GPC["op"]) : "list");
if( $op == "list" ) 
{
    $_W["page"]["title"] = "天降红包列表";
    $condition = " where uniacid = :uniacid and type = :type";
    $params = array( ":uniacid" => $_W["uniacid"], ":type" => "gift" );
    $keyword = trim($_GPC["keyword"]);
    if( !empty($keyword) ) 
    {
        $condition .= " and name like '%" . $keyword . "%'";
    }

    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("select count(*) FROM " . tablename("tiny_wmall_superredpacket") . $condition, $params);
    $superRedpackets = pdo_fetchall("select * from " . tablename("tiny_wmall_superredpacket") . $condition . " order by id desc limit " . ($pindex - 1) * $psize . "," . $psize, $params);
    foreach( $superRedpackets as &$row ) 
    {
        $row["data"] = json_decode(base64_decode($row["data"]), true);
        $row["starttime"] = date("Y-m-d", $row["starttime"]);
        $row["endtime"] = date("Y-m-d", $row["endtime"]);
        $row["addtime"] = date("Y-m-d H:i", $row["addtime"]);
    }
    $pager = pagination($total, $pindex, $psize);
    include(itemplate("giftList"));
}

if( $op == "post" ) 
{
    $_W["page"]["title"] = "天降红包设置";
    $id = intval($_GPC["id"]);
    if( $_W["ispost"] ) 
    {
        $data = $_GPC["data"];
        $starttime = strtotime($data["params"]["starttime"]);
        $endtime = strtotime($data["params"]["endtime"]);
        if( $endtime <= $starttime ) 
        {
            imessage(error(-1, "活动的开始时间不能大于结束时间"), "", "ajax");
        }

        $insert = array( "uniacid" => $_W["uniacid"], "name" => $data["name"], "type" => "gift", "starttime" => $starttime, "endtime" => $endtime, "data" => base64_encode(json_encode($data)) );
        if( !empty($id) ) 
        {
            pdo_update("tiny_wmall_superredpacket", $insert, array( "id" => $id, "uniacid" => $_W["uniacid"] ));
        }
        else
        {
            $insert["addtime"] = TIMESTAMP;
            $insert["status"] = 1;
            pdo_insert("tiny_wmall_superredpacket", $insert);
            $id = pdo_insertid();
        }

        imessage(error(0, "天降红包设置成功"), iurl("superRedpacket/gift/post", array( "id" => $id )), "ajax");
    }

    if( !empty($id) ) 
    {
        $superRedpacket = pdo_fetch("select * from " . tablename("tiny_wmall_superredpacket") . " where id = :id and uniacid = :uniacid", array( ":id" => $id, ":uniacid" => $_W["uniacid"] ));
        if( !empty($superRedpacket) ) 
        {
            $superRedpacket["data"] = json_decode(base64_decode($superRedpacket["data"]), true);
        }

    }
    else
    {
        $superRedpacket_yes = pdo_fetch("select id from " . tablename("tiny_wmall_superredpacket") . " where uniacid = :uniacid and type = :type and status = 1", array( ":uniacid" => $_W["uniacid"], ":type" => "gift" ));
        if( !empty($superRedpacket_yes) ) 
        {
            imessage("已创建天降红包活动, 如需重新添加天降红包活动，请先撤销其他活动", referer(), "info");
        }

    }

    include(itemplate("giftPost"));
}

if( $op == "del" ) 
{
    $ids = $_GPC["id"];
    if( !is_array($ids) ) 
    {
        $ids = array( $ids );
    }

    foreach( $ids as $id ) 
    {
        pdo_delete("tiny_wmall_superredpacket", array( "uniacid" => $_W["uniacid"], "id" => $id ));
    }
    imessage(error(0, "删除成功"), referer(), "ajax");
}

if( $op == "cancel" ) 
{
    $id = intval($_GPC["id"]);
    pdo_update("tiny_wmall_superredpacket", array( "status" => 2 ), array( "uniacid" => $_W["uniacid"], "id" => $id ));
    imessage(error(0, "撤销成功"), referer(), "ajax");
}


