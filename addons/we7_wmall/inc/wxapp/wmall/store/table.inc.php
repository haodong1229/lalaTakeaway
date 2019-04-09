<?php 











defined("IN_IA") or exit( "Access Denied" );
define("ORDER_TYPE", "tangshi");
mload()->model("goods");
mload()->model("table");
global $_W;
global $_GPC;
icheckauth();
$_W["page"]["title"] = "商品列表";
$sid = intval($_GPC["sid"]);
$store = store_fetch($sid);
if( empty($store) ) 
{
    imessage(error(-1, "门店不存在或已经删除"), "", "ajax");
}

if( $store["is_meal"] != 1 ) 
{
    imessage(error(-1000, "店内点餐暂未开启"), "", "ajax");
}

$_share = array( "title" => $store["title"], "desc" => $store["content"], "imgUrl" => tomedia($store["logo"]) );
$ta = (trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "index");
if( $ta == "index" ) 
{
    $table_id = intval($_GPC["table_id"]);
    $table = table_fetch($table_id);
    if( empty($table) ) 
    {
        imessage(error(-1000, "桌号不存在"), "", "ajax");
    }

    $store["activity"] = store_fetch_activity($sid);
    $store["is_favorite"] = is_favorite_store($sid, $_W["member"]["uid"]);
    mload()->model("coupon");
    $coupons = coupon_collect_member_available($sid);
    $template = ($store["data"]["wxapp"]["template"] ? $store["data"]["wxapp"]["template"] : 1);
    $categorys = array_values(store_fetchall_goods_category($sid, 1, false, "all", "available"));
    $store["table_id"] = $table["id"];
    $result = array( "store" => $store, "coupon" => $coupons, "category" => $categorys, "cart" => cart_data_init($sid), "template" => $template, "share" => $_share, "table" => $table );
    $cid = (intval($_GPC["cid"]) ? intval($_GPC["cid"]) : $categorys[0]["id"]);
    $child_id = 0;
    if( !empty($categorys[0]["child"]) ) 
    {
        $child_id = $categorys[0]["child"][0]["id"];
    }

    $result["goods"] = goods_filter($sid, array( "cid" => $cid, "child_id" => $child_id ));
    $result["cid"] = $cid;
    $result["child_id"] = $child_id;
    imessage(error(0, $result), "", "ajax");
}
else
{
    if( $ta == "index_vue" ) 
    {
        $table_id = intval($_GPC["table_id"]);
        $table = table_fetch($table_id);
        if( empty($table) ) 
        {
            imessage(error(-1000, "桌号不存在"), "", "ajax");
        }

        $store["activity"] = store_fetch_activity($sid);
        $store["is_favorite"] = is_favorite_store($sid, $_W["member"]["uid"]);
        mload()->model("coupon");
        $coupons = coupon_collect_member_available($sid);
        $template = ($store["data"]["wxapp"]["template"] ? $store["data"]["wxapp"]["template"] : 1);
        $template_page = ($store["data"]["wxapp"]["template_page"]["vue"] ? $store["data"]["wxapp"]["template_page"]["vue"] : 0);
        $store["table_id"] = $table["id"];
        $result = array( "store" => $store, "coupon" => $coupons, "cart" => cart_data_init($sid), "template" => $template, "table" => $table, "template_page" => $template_page );
        if( $template_page == 1 ) 
        {
            $categorys = array_values(store_fetchall_goods_category($sid, 1, false, "all", "available"));
            $result["category"] = $categorys;
            $cid = (intval($_GPC["cid"]) ? intval($_GPC["cid"]) : $categorys[0]["id"]);
            $child_id = 0;
            if( !empty($categorys[0]["child"]) ) 
            {
                $child_id = $categorys[0]["child"][0]["id"];
            }

            $result["goods"] = goods_filter($sid, array( "cid" => $cid, "child_id" => $child_id ));
            $result["cid"] = $cid;
            $result["child_id"] = $child_id;
        }
        else
        {
            $all = goods_avaliable_fetchall($sid);
            $result["cate_has_goods"] = $all["cate_has_goods"];
        }

        $_W["_share"] = $_share;
        imessage(error(0, $result), "", "ajax");
    }
    else
    {
        if( $ta == "truncate" ) 
        {
            $data = pdo_delete("tiny_wmall_order_cart", array( "uniacid" => $_W["uniacid"], "sid" => $sid, "uid" => $_W["member"]["uid"] ));
            imessage(error(0, ""), "", "ajax");
        }
        else
        {
            if( $ta == "cart" ) 
            {
                $goods_id = intval($_GPC["goods_id"]);
                $option_id = trim($_GPC["option_id"]);
                $sign = trim($_GPC["sign"]);
                $order_id = intval($_GPC["order_id"]);
                $ignore_bargain = (0 < $order_id ? true : false);
                $cart = cart_data_init($sid, $goods_id, $option_id, $sign, $ignore_bargain);
                imessage($cart, "", "ajax");
            }
            else
            {
                if( $ta == "goods" ) 
                {
                    $goods = goods_filter($sid);
                    $result = array( "goods" => $goods );
                    imessage(error(0, $result), "", "ajax");
                }
                else
                {
                    if( $ta == "detail" ) 
                    {
                        $sid = intval($_GPC["sid"]);
                        $id = intval($_GPC["id"]);
                        $goods = goods_fetch($id);
                        if( is_error($goods) ) 
                        {
                            imessage(error(-1, "商品不存在或已删除"), "", "ajax");
                        }

                        $table_id = intval($_GPC["table_id"]);
                        $table = table_fetch($table_id);
                        if( empty($table) ) 
                        {
                            imessage(error(-1000, "桌号不存在"), "", "ajax");
                        }

                        $bargain_goods = pdo_fetch("select a.discount_price,a.max_buy_limit,b.status as bargain_status from " . tablename("tiny_wmall_activity_bargain_goods") . " as a left join " . tablename("tiny_wmall_activity_bargain") . " as b on a.bargain_id = b.id where a.uniacid = :uniacid and a.sid = :sid and a.goods_id = :goods_id and a.status = 1 and b.status = 1", array( ":uniacid" => $_W["uniacid"], ":sid" => $sid, ":goods_id" => $id ));
                        if( !empty($bargain_goods["bargain_status"]) ) 
                        {
                            $goods = array_merge($goods, $bargain_goods);
                        }

                        $cart = order_fetch_member_cart($sid);
                        $goods["old_price"] = $goods["ts_price"];
                        $goods["price"] = $goods["old_price"];
                        $goods = goods_format($goods);
                        if( !empty($cart["data"][$id]) ) 
                        {
                            foreach( $cart["data"][$id] as $key => $cart_option ) 
                            {
                                $goods["options_data"][$key]["num"] = $cart_option["num"];
                                $goods["totalnum"] += $cart_option["num"];
                            }
                        }

                        $result = array( "goodsDetail" => $goods, "cart" => cart_data_init($sid), "store" => $store, "table" => $table );
                        message(error(0, $result), "", "ajax");
                        return 1;
                    }
                    else
                    {
                        if( $ta == "create" ) 
                        {
                            $cart = order_fetch_member_cart($sid, true);
                            if( empty($cart) ) 
                            {
                                imessage(error(1000, "购物车数据错误"), "", "ajax");
                            }

                            $pay_types = order_pay_types();
                            $condition = json_decode(htmlspecialchars_decode($_GPC["extra"]), true);
                            $coupon_id = intval($condition["coupon_id"]);
                            $redpacket_id = intval($condition["redpacket_id"]);
                            $message = error(0, "");
                            $activityed = order_count_activity($sid, $cart, $coupon_id, $redpacket_id, 0, 0, 3);
                            $serve_fee = 0;
                            if( 0 < $store["serve_fee"]["type"] && 0 < $store["serve_fee"]["fee"] ) 
                            {
                                $serve_fee = $store["serve_fee"]["fee"];
                                if( $store["serve_fee"]["type"] == 2 ) 
                                {
                                    $serve_fee = round(($store["serve_fee"]["fee"] * $cart["price"]) / 100, 2);
                                }

                            }

                            mload()->model("coupon");
                            mload()->model("redPacket");
                            $order = array( "order_type" => 3, "price" => $cart["price"], "total_fee" => $cart["price"] + $serve_fee, "serve_fee" => $serve_fee, "discount_fee" => $activityed["total"], "activityed" => $activityed, "note" => trim($condition["note"]), "person_num" => trim($condition["person_num"]), "invoiceId" => intval($condition["invoiceId"]), "coupon" => coupon_available_check($sid, $coupon_id, $cart["price"]), "redpacket" => redpacket_available_check($redpacket_id, $cart["price"], explode("|", $store["cid"]), array( "scene" => "waimai", "sid" => $sid )) );
                            $order["final_fee"] = $order["total_fee"] - $activityed["total"];
                            $result = array( "store" => $store, "cart" => $cart, "coupons" => coupon_available($sid, $cart["price"]), "redPackets" => redPacket_available($cart["price"], explode("|", $store["cid"]), array( "scene" => "waimai", "sid" => $sid )), "order" => $order, "message" => $message, "islegal" => 1 );
                            imessage(error(0, $result), "", "ajax");
                        }
                        else
                        {
                            if( $ta == "submit" ) 
                            {
                                $cart = order_check_member_cart($sid);
                                if( is_error($cart) ) 
                                {
                                    imessage($cart, "", "ajax");
                                }

                                if( !$store["is_in_business_hours"] ) 
                                {
                                    imessage(error(-1, "商户休息中"), "", "ajax");
                                }

                                $condition = json_decode(htmlspecialchars_decode($_GPC["extra"]), true);
                                if( empty($condition) ) 
                                {
                                    imessage(error(-1, "参数错误"), "", "ajax");
                                }

                                $coupon_id = intval($condition["coupon_id"]);
                                $redpacket_id = intval($condition["redpacket_id"]);
                                $activityed = order_count_activity($sid, $cart, $coupon_id, $redpacket_id, 0, 0, 3);
                                $serve_fee = 0;
                                if( 0 < $store["serve_fee"]["type"] && 0 < $store["serve_fee"]["fee"] ) 
                                {
                                    $serve_fee = $store["serve_fee"]["fee"];
                                    if( $store["serve_fee"]["type"] == 2 ) 
                                    {
                                        $serve_fee = round(($store["serve_fee"]["fee"] * $cart["price"]) / 100, 2);
                                    }

                                }

                                $invoice_id = intval($condition["invoice_id"]);
                                if( 0 < $invoice_id ) 
                                {
                                    $invoice = member_invoice($invoice_id);
                                    $invoice = iserializer(array( "title" => $invoice["title"], "recognition" => $invoice["recognition"] ));
                                }

                                $table_id = intval($_GPC["table_id"]);
                                $order = array( "uniacid" => $_W["uniacid"], "acid" => $_W["acid"], "sid" => $sid, "uid" => $_W["member"]["uid"], "ordersn" => date("YmdHis") . random(6, true), "serial_sn" => store_order_serial_sn($sid), "code" => random(4, true), "order_type" => 3, "openid" => $_W["openid"], "mobile" => "", "username" => trim($condition["username"]), "person_num" => intval($condition["person_num"]), "table_id" => $table_id, "sex" => "", "address" => "", "location_x" => "", "location_y" => "", "delivery_day" => "", "delivery_time" => "", "delivery_fee" => 0, "pack_fee" => 0, "pay_type" => "", "num" => $cart["num"], "price" => $cart["price"], "serve_fee" => $serve_fee, "total_fee" => $cart["price"] + $serve_fee, "discount_fee" => $activityed["total"], "store_discount_fee" => $activityed["store_discount_fee"], "plateform_discount_fee" => $activityed["plateform_discount_fee"], "final_fee" => ($cart["price"] + $serve_fee) - $activityed["total"], "status" => 1, "is_comment" => 0, "invoice" => $invoice, "addtime" => TIMESTAMP, "data" => iserializer($cart["data"]), "note" => trim($condition["note"]) );
                                if( $order["final_fee"] < 0 ) 
                                {
                                    $order["final_fee"] = 0;
                                }

                                pdo_insert("tiny_wmall_order", $order);
                                $order_id = pdo_insertid();
                                $order_id = intval($order_id);
                                if( empty($order_id) ) 
                                {
                                    imessage(error(-1, "订单信息有误，请重新下单"), "", "ajax");
                                }

                                order_update_bill($order_id);
                                table_order_update($table_id, $order_id, 3);
                                order_insert_discount($order_id, $sid, $activityed["list"]);
                                order_insert_status_log($order_id, "place_order");
                                order_update_goods_info($order_id, $sid);
                                order_del_member_cart($sid);
                                order_clerk_notice($order_id, "store_order_place");
                                imessage(error(0, $order_id), "", "ajax");
                            }
                            else
                            {
                                if( $ta == "jiacai" ) 
                                {
                                    $order_id = intval($_GPC["order_id"]);
                                    $result = table_order_jiacai($sid, $order_id);
                                    if( !is_error($result) ) 
                                    {
                                        order_del_member_cart($sid);
                                    }

                                    imessage($result, "", "ajax");
                                }
                                else
                                {
                                    if( $ta == "note" ) 
                                    {
                                        if( $store["invoice_status"] == 1 ) 
                                        {
                                            $invoices = member_invoices();
                                        }

                                        $result = array( "invoices" => $invoices, "store" => $store );
                                        imessage(error(0, $result), "", "ajax");
                                    }

                                }

                            }

                        }

                    }

                }

            }

        }

    }

}
?>