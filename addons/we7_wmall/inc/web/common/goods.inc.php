<?php 
defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$op = trim($_GPC["op"]);
if( $op == "list" ) 
{
    $condition = " where uniacid = :uniacid";
    $params = array( ":uniacid" => $_W["uniacid"] );
    $sid = intval($_GPC["store_id"]);
    if( 0 < $sid ) 
    {
        $condition .= " and sid = :sid";
        $params[":sid"] = $sid;
    }

    $is_options = intval($_GPC["is_options"]);
    $condition .= " and is_options = :is_options";
    $params[":is_options"] = $is_options;
    $key = trim($_GPC["key"]);
    if( !empty($key) ) 
    {
        $condition .= " and title like :key";
        $params[":key"] = "%" . $key . "%";
    }

    $data = pdo_fetchall("select id, sid, title, thumb, price, old_price, sailed, comment_good, comment_total,total from " . tablename("tiny_wmall_goods") . $condition, $params, "id");
    if( !empty($data) ) 
    {
        foreach( $data as &$row ) 
        {
            $row["thumb"] = tomedia($row["thumb"]);
            $row["store"] = pdo_fetch("select id, title, logo, send_price, delivery_price, delivery_time from " . tablename("tiny_wmall_store") . " where id = :id ", array( ":id" => $row["sid"] ));
            if( $row["store"] ) 
            {
                $row["store"]["price"] = store_order_condition($row["store"]["id"]);
                $row["store"]["delivery_price"] = $row["store"]["price"]["delivery_price"];
                $row["store_title"] = $row["store"]["title"];
            }

            $row["old_price"] = $row["old_price"];
            if( $row["old_price"] != 0 ) 
            {
                $row["discount"] = round($row["price"] / $row["old_price"] * 10, 1);
            }
            else
            {
                $row["discount"] = 0;
            }

            if( $row["comment_total"] != 0 ) 
            {
                $row["comment_good_percent"] = round($row["comment_good"] / $row["comment_total"] * 100, 2) . "%";
            }
            else
            {
                $row["comment_good_percent"] = "0%";
            }

            if( $row["total"] == -1 ) 
            {
                $row["total"] = "无限";
            }

        }
        $goods = array_values($data);
    }

    message(array( "errno" => 0, "message" => $goods, "data" => $data ), "", "ajax");
}


