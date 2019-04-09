<?php 
defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$op = (trim($_GPC["op"]) ? trim($_GPC["op"]) : "list");
mload()->model("deliveryer");
if( $op == "list" ) 
{
    $_W["page"]["title"] = "用户评价";
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $condition = " WHERE a.uniacid = :uniacid";
    $params = array( ":uniacid" => $_W["uniacid"] );
    $deliveryer_id = intval($_GPC["deliveryer_id"]);
    if( 0 < $deliveryer_id ) 
    {
        $condition .= " AND a.deliveryer_id = :deliveryer_id";
        $params[":deliveryer_id"] = $deliveryer_id;
    }

    $sid = intval($_GPC["sid"]);
    if( 0 < $sid ) 
    {
        $condition .= " AND a.sid = :sid";
        $params[":sid"] = $sid;
    }

    $agentid = intval($_GPC["agentid"]);
    if( 0 < $agentid ) 
    {
        $condition .= " and a.agentid = :agentid";
        $params[":agentid"] = $agentid;
    }

    $uid = intval($_GPC["uid"]);
    if( 0 < $uid ) 
    {
        $condition .= " AND a.uid = :uid";
        $params[":uid"] = $uid;
    }

    $status = (isset($_GPC["status"]) ? intval($_GPC["status"]) : -1);
    if( 0 <= $status ) 
    {
        $condition .= " AND a.status = :status";
        $params[":status"] = $status;
    }

    $reply = (isset($_GPC["reply"]) ? intval($_GPC["reply"]) : -1);
    if( $reply == 0 ) 
    {
        $condition .= " AND a.reply = ''";
    }
    else
    {
        if( $reply == 1 ) 
        {
            $condition .= " AND a.reply != ''";
        }

    }

    $note = (isset($_GPC["note"]) ? intval($_GPC["note"]) : -1);
    if( $note == 1 ) 
    {
        $condition .= " AND a.note != ''";
    }

    $goods_quality = (isset($_GPC["goods_quality"]) ? intval($_GPC["goods_quality"]) : -1);
    if( 0 < $goods_quality ) 
    {
        $condition .= " AND a.goods_quality = " . $_GPC["goods_quality"];
    }

    $delivery_service = (isset($_GPC["delivery_service"]) ? intval($_GPC["delivery_service"]) : -1);
    if( 0 < $delivery_service ) 
    {
        $condition .= " AND a.delivery_service = " . $_GPC["delivery_service"];
    }

    $is_share = (isset($_GPC["is_share"]) ? intval($_GPC["is_share"]) : -1);
    if( -1 < $is_share ) 
    {
        $condition .= " AND a.is_share = :is_share";
        $params[":is_share"] = $is_share;
    }

    if( !empty($_GPC["addtime"]) ) 
    {
        $starttime = strtotime($_GPC["addtime"]["start"]);
        $endtime = strtotime($_GPC["addtime"]["end"]);
    }
    else
    {
        $starttime = strtotime("-15 day");
        $endtime = TIMESTAMP;
    }

    $keyword = trim($_GPC["keyword"]);
    if( !empty($keyword) ) 
    {
        $condition .= " AND (a.username LIKE '%" . $keyword . "%' OR a.mobile LIKE '%" . $keyword . "%')";
    }

    $condition .= " AND a.addtime > :start AND a.addtime < :end";
    $params[":start"] = $starttime;
    $params[":end"] = $endtime;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_order_comment") . " AS a " . $condition, $params);
    if( !empty($keyword) ) 
    {
        $condition .= " AND (b.ordersn LIKE '%" . $keyword . "%')";
    }

    $comments = pdo_fetchall("SELECT a.*, b.uid,b.openid,b.addtime FROM " . tablename("tiny_wmall_order_comment") . " AS a LEFT JOIN " . tablename("tiny_wmall_order") . " AS b ON a.oid = b.id " . $condition . " ORDER BY a.addtime DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
    if( !empty($comments) ) 
    {
        foreach( $comments as &$row ) 
        {
            $row["score"] = ($row["delivery_service"] + $row["goods_quality"]) / 2;
            $row["data"] = iunserializer($row["data"]);
            $row["mobile"] = str_replace(substr($row["mobile"], 4, 4), "****", $row["mobile"]);
            $row["thumbs"] = iunserializer($row["thumbs"]);
        }
    }

    $deliveryers = deliveryer_all();
    $pager = pagination($total, $pindex, $psize);
    $stores = pdo_fetchall("select id,title from " . tablename("tiny_wmall_store") . " where uniacid = :uniacid", array( ":uniacid" => $_W["uniacid"] ), "id");
}

if( $op == "status" ) 
{
    $id = intval($_GPC["comment_id"]);
    if( !empty($id) ) 
    {
        $comment = pdo_get("tiny_wmall_order_comment", array( "uniacid" => $_W["uniacid"], "id" => $id ));
        if( empty($comment) ) 
        {
            imessage(error(-1, "评论不存在或已删除"), "", "ajax");
        }

        pdo_update("tiny_wmall_order_comment", array( "status" => intval($_GPC["status"]) ), array( "uniacid" => $_W["uniacid"], "id" => $id ));
        store_comment_stat($comment["sid"]);
    }
    else
    {
        $ids = $_GPC["id"];
        if( !empty($ids) ) 
        {
            foreach( $ids as $value ) 
            {
                $comment = pdo_get("tiny_wmall_order_comment", array( "uniacid" => $_W["uniacid"], "id" => $value ));
                if( empty($comment) ) 
                {
                    imessage(error(-1, "评论不存在或已删除"), "", "ajax");
                }

                pdo_update("tiny_wmall_order_comment", array( "status" => intval($_GPC["status"]) ), array( "uniacid" => $_W["uniacid"], "id" => $value ));
                store_comment_stat($comment["sid"]);
            }
        }

    }

    imessage(error(0, "设置评论状态成功"), referer(), "ajax");
}

if( $op == "reply" ) 
{
    if( !$_W["isajax"] ) 
    {
        return false;
    }

    $id = intval($_GPC["id"]);
    $comment = pdo_get("tiny_wmall_order_comment", array( "uniacid" => $_W["uniacid"], "id" => $id ));
    $order = order_fetch($comment["oid"]);
    if( empty($order) ) 
    {
        imessage(error(-1, "订单不存在或已经删除"), "", "ajax");
    }

    $reply = trim($_GPC["reply"]);
    pdo_update("tiny_wmall_order_comment", array( "reply" => $reply, "replytime" => TIMESTAMP, "status" => 1 ), array( "uniacid" => $_W["uniacid"], "id" => $id ));
    store_comment_stat($order["sid"]);
    imessage(error(0, ""), "", "ajax");
}

if( $op == "share" ) 
{
    $id = intval($_GPC["id"]);
    $comment = pdo_get("tiny_wmall_order_comment", array( "uniacid" => $_W["uniacid"], "id" => $id ));
    if( empty($comment) ) 
    {
        imessage(error(-1, "评论不存在或已删除"), "", "ajax");
    }

    $is_share = intval($_GPC["is_share"]);
    $update = array( "is_share" => $is_share );
    if( 0 < $is_share ) 
    {
        $update["status"] = 1;
    }

    pdo_update("tiny_wmall_order_comment", $update, array( "uniacid" => $_W["uniacid"], "id" => $id ));
    imessage(error(0, "操作成功"), referer(), "ajax");
}

if( $op == "group" ) 
{
    if( $_W["ispost"] ) 
    {
        $reply = trim($_GPC["reply"]);
        if( empty($reply) ) 
        {
            imessage(error(-1, "回复内容不能为空！"), referer(), "ajax");
        }

        $comment_ids = explode(",", $_GPC["comment_ids"]);
        if( !empty($comment_ids) ) 
        {
            foreach( $comment_ids as $id ) 
            {
                $comment = pdo_get("tiny_wmall_order_comment", array( "uniacid" => $_W["uniacid"], "id" => $id ));
                $order = order_fetch($comment["oid"]);
                if( empty($order) ) 
                {
                    imessage(error(-1, "订单不存在或已经删除"), "", "ajax");
                }

                pdo_update("tiny_wmall_order_comment", array( "reply" => $reply, "replytime" => TIMESTAMP, "status" => 1 ), array( "uniacid" => $_W["uniacid"], "id" => $id ));
                store_comment_stat($order["sid"]);
            }
        }

        imessage(error(0, "批量回复成功！"), iurl("service/comment"), "ajax");
    }

    $ids = implode(",", $_GPC["id"]);
    include(itemplate("service/commentOp"));
    exit();
}
else
{
    include(itemplate("service/comment"));
}


