<?php 










defined("IN_IA") or exit( "Access Denied" );
mload()->model("table");
global $_W;
global $_GPC;
$op = (trim($_GPC["op"]) ? trim($_GPC["op"]) : "list");
if( $op == "list" ) 
{
    $_W["page"]["title"] = "店内订单";
    $condition = " where uniacid = :uniacid and status = 5 and order_type > 2 and stat_day = :stat_day";
    $stat = pdo_fetch("select count(*) as total_num, sum(final_fee) as total_price from " . tablename("tiny_wmall_order") . $condition, array( ":uniacid" => $_W["uniacid"], ":stat_day" => date("Ymd") ));
    $filter_type = (trim($_GPC["filter_type"]) ? trim($_GPC["filter_type"]) : "process");
    $condition = " WHERE uniacid = :uniacid and order_type > 2";
    $params = array( ":uniacid" => $_W["uniacid"] );
    if( $filter_type == "process" ) 
    {
        $condition .= " AND (status != 5 and status != 6)";
    }

    $uid = intval($_GPC["uid"]);
    if( 0 < $uid ) 
    {
        $condition .= " AND uid = :uid";
        $params[":uid"] = $uid;
    }

    $sid = intval($_GPC["sid"]);
    if( 0 < $sid ) 
    {
        $condition .= " AND sid = :sid";
        $params[":sid"] = $sid;
    }

    $agentid = intval($_GPC["agentid"]);
    if( 0 < $agentid ) 
    {
        $condition .= " and agentid = :agentid";
        $params[":agentid"] = $agentid;
    }

    $status = intval($_GPC["status"]);
    if( 0 < $status ) 
    {
        $condition .= " AND status = :status";
        $params[":status"] = $status;
    }

    $is_remind = intval($_GPC["is_remind"]);
    if( 0 < $is_remind ) 
    {
        $condition .= " AND is_remind = :is_remind";
        $params[":is_remind"] = $is_remind;
    }

    $re_status = intval($_GPC["refund_status"]);
    if( 0 < $re_status ) 
    {
        $condition .= " AND refund_status = :refund_status";
        $params[":refund_status"] = $re_status;
    }

    $is_pay = (isset($_GPC["is_pay"]) ? intval($_GPC["is_pay"]) : -1);
    if( -1 < $is_pay ) 
    {
        $condition .= " AND is_pay = :is_pay";
        $params[":is_pay"] = $is_pay;
    }

    $pay_type = trim($_GPC["pay_type"]);
    if( !empty($pay_type) ) 
    {
        $condition .= " AND is_pay = 1 AND pay_type = :pay_type";
        $params[":pay_type"] = $pay_type;
    }

    $keyword = trim($_GPC["keyword"]);
    if( !empty($keyword) ) 
    {
        $condition .= " AND (username LIKE '%" . $keyword . "%' OR mobile LIKE '%" . $keyword . "%' OR ordersn LIKE '%" . $keyword . "%')";
    }

    if( !empty($_GPC["addtime"]) ) 
    {
        $starttime = strtotime($_GPC["addtime"]["start"]);
        $endtime = strtotime($_GPC["addtime"]["end"]) + 86399;
    }
    else
    {
        $starttime = strtotime("-7 day");
        $endtime = TIMESTAMP + 86400;
    }

    $condition .= " AND addtime > :start AND addtime < :end";
    $params[":start"] = $starttime;
    $params[":end"] = $endtime;
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 15;
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("tiny_wmall_order") . $condition, $params);
    $orders = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_order") . $condition . " ORDER BY addtime DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params, "id");
    if( !empty($orders) ) 
    {
        $order_ids = implode(",", array_keys($orders));
        $goods_temp = pdo_fetchall("select * from " . tablename("tiny_wmall_order_stat") . " where uniacid = :uniacid and oid in (" . $order_ids . ")", array( ":uniacid" => $_W["uniacid"] ));
        $goods_all = array(  );
        foreach( $goods_temp as $row ) 
        {
            $goods_all[$row["oid"]][] = $row;
        }
        foreach( $orders as &$da ) 
        {
            $da["pay_type_class"] = "";
            if( $da["is_pay"] == 1 ) 
            {
                $da["pay_type_class"] = "have-pay";
                if( $da["pay_type"] == "delivery" ) 
                {
                    $da["pay_type_class"] = "delivery-pay";
                }

            }

            if( $da["order_type"] == 3 ) 
            {
                $tables[] = $da["table_id"];
            }

        }
        if( !empty($tables) ) 
        {
            $tables_str = implode(",", $tables);
            $tables = pdo_fetchall("select * from " . tablename("tiny_wmall_tables") . " where uniacid = :uniacid and sid = :sid and id in (" . $tables_str . ")", array( ":uniacid" => $_W["uniacid"], ":sid" => $sid ), "id");
        }

    }

    $pager = pagination($total, $pindex, $psize);
    $pay_types = order_pay_types();
    $order_types = order_types();
    $order_status = order_status();
    $refund_status = order_refund_status();
    $order_reserve_types = order_reserve_type();
    $table_categorys = table_category_fetchall($sid);
    $stores = pdo_getall("tiny_wmall_store", array( "uniacid" => $_W["uniacid"] ), array( "id", "title" ), "id");
    include(itemplate("order/tangshiList"));
}

if( $op == "detail" ) 
{
    $_W["page"]["title"] = "订单详情";
    $id = intval($_GPC["id"]);
    $order = order_fetch($id);
    if( empty($order) ) 
    {
        imessage("订单不存在或已经删除", iurl("order/takeout/list"), "error");
    }

    $order["goods"] = order_fetch_goods($order["id"]);
    if( $order["is_comment"] == 1 ) 
    {
        $comment = pdo_fetch("SELECT * FROM " . tablename("tiny_wmall_order_comment") . " WHERE uniacid = :aid AND oid = :oid", array( ":aid" => $_W["uniacid"], ":oid" => $id ));
        if( !empty($comment) ) 
        {
            $comment["data"] = iunserializer($comment["data"]);
            $comment["thumbs"] = iunserializer($comment["thumbs"]);
        }

    }

    if( 0 < $order["discount_fee"] ) 
    {
        $discount = order_fetch_discount($id);
    }

    $pay_types = order_pay_types();
    $order_types = order_types();
    $order_status = order_status();
    $logs = order_fetch_status_log($id);
    include(itemplate("order/tangshiDetail"));
}

if( $op == "status" ) 
{
    $ids = $_GPC["id"];
    if( !is_array($ids) ) 
    {
        $ids = array( $ids );
    }

    $type = trim($_GPC["type"]);
    if( empty($type) ) 
    {
        imessage(error(-1, "订单状态错误"), "", "ajax");
    }

    foreach( $ids as $id ) 
    {
        $id = intval($id);
        if( $id <= 0 ) 
        {
            continue;
        }

        $result = order_status_update($id, $type);
        if( is_error($result) ) 
        {
            imessage(error(-1, "处理编号为:" . $id . "的订单失败，具体原因：" . $result["message"]), "", "ajax");
        }

    }
    imessage(error(0, "更新订状态成功"), "", "ajax");
}

if( $op == "cancel" ) 
{
    $id = intval($_GPC["id"]);
    $result = order_status_update($id, "cancel", array( "force_cancel" => 1 ));
    if( is_error($result) ) 
    {
        imessage(error(-1, "处理编号为:" . $id . " 的订单失败，具体原因：" . $result["message"]), "", "ajax");
    }

    if( $result["message"]["is_refund"] ) 
    {
        $refund = order_refund_status_update($id, 0, "handle");
        if( is_error($refund) ) 
        {
            imessage(error(-1, $refund["message"]), "", "ajax");
        }

        imessage(error(0, "取消订单成功," . $refund["message"]), "", "ajax");
    }
    else
    {
        imessage(error(0, "取消订单成功"), "", "ajax");
    }

}

if( $op == "refund_update" ) 
{
    $order_id = intval($_GPC["id"]);
    $refund_id = intval($_GPC["refund_id"]);
    $type = trim($_GPC["type"]);
    $result = order_refund_status_update($order_id, $refund_id, $type);
    imessage($result, referer(), "ajax");
}

if( $op == "remind" ) 
{
    $id = intval($_GPC["id"]);
    if( $_W["ispost"] ) 
    {
        $reply = trim($_GPC["reply"]);
        $result = order_status_update($id, "reply", array( "reply" => $reply ));
        imessage(error(0, "回复催单成功"), referer(), "ajax");
    }

    include(itemplate("store/order/tangshiOp"));
}

if( $op == "print" ) 
{
    $id = intval($_GPC["id"]);
    $status = order_print($id);
    if( is_error($status) ) 
    {
        imessage(error(-1, $status["message"]), "", "ajax");
    }

    imessage(error(0, "发送打印指定成功"), "", "ajax");
}

if( $op == "pay_status" ) 
{
    $id = intval($_GPC["id"]);
    $result = order_status_update($id, "pay");
    if( is_error($result) ) 
    {
        message($result["message"], referer(), "error");
    }

    message("设置订单支付成功", referer(), "success");
}
?>