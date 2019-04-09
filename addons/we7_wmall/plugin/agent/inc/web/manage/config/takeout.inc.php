<?php 
defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$op = (trim($_GPC["op"]) ? trim($_GPC["op"]) : "settle");
$_config = get_agent_system_config();
if( $op == "range" ) 
{
    $_W["page"]["title"] = "服务范围";
    if( $_W["ispost"] ) 
    {
        $range = array( "map" => array( "location_x" => trim($_GPC["map"]["lat"]), "location_y" => trim($_GPC["map"]["lng"]) ), "city" => trim($_GPC["city"]), "serve_radius" => floatval($_GPC["serve_radius"]) );
        set_agent_system_config("takeout.range", $range);
        imessage(error(0, "外卖服务范围设置成功"), referer(), "ajax");
    }

    $range = $_config["takeout"]["range"];
    $range["map"]["lat"] = $range["map"]["location_x"];
    $range["map"]["lng"] = $range["map"]["location_y"];
    include(itemplate("config/takeout-range"));
}

if( $op == "order" ) 
{
    $_W["page"]["title"] = "订单相关";
    if( $_W["ispost"] ) 
    {
        $order = array( "notify_rule_clerk" => array( "notify_delay" => intval($_GPC["clerk"]["notify_delay"]), "notify_frequency" => intval($_GPC["clerk"]["notify_frequency"]), "notify_total" => intval($_GPC["clerk"]["notify_total"]), "notify_phonecall_time" => intval($_GPC["clerk"]["notify_phonecall_time"]) ), "notify_rule_deliveryer" => array( "notify_delay" => intval($_GPC["deliveryer"]["notify_delay"]), "notify_frequency" => intval($_GPC["deliveryer"]["notify_frequency"]), "notify_total" => intval($_GPC["deliveryer"]["notify_total"]), "notify_phonecall_time" => intval($_GPC["deliveryer"]["notify_phonecall_time"]) ), "show_no_pay" => intval($_GPC["show_no_pay"]), "auto_refresh" => intval($_GPC["auto_refresh"]), "deliveryer_collect_notify_clerk" => intval($_GPC["deliveryer_collect_notify_clerk"]), "timeout_limit" => intval($_GPC["timeout_limit"]), "delivery_timeout_limit" => intval($_GPC["delivery_timeout_limit"]), "delivery_before_limit" => intval($_GPC["delivery_before_limit"]), "dispatch_mode" => intval($_GPC["dispatch_mode"]), "can_collect_order" => intval($_GPC["can_collect_order"]), "deliveryer_collect_max" => intval($_GPC["deliveryer_collect_max"]), "over_collect_max_notify" => intval($_GPC["over_collect_max_notify"]), "deliveryer_transfer_status" => intval($_GPC["deliveryer_transfer_status"]), "deliveryer_transfer_max" => intval($_GPC["deliveryer_transfer_max"]), "deliveryer_transfer_reason" => explode("\n", trim($_GPC["deliveryer_transfer_reason"])), "deliveryer_cancel_reason" => explode("\n", trim($_GPC["deliveryer_cancel_reason"])), "dispatch_sort" => trim($_GPC["dispatch_sort"]), "max_dispatching" => intval($_GPC["max_dispatching"]), "show_acceptaddress_when_firstdelivery" => intval($_GPC["show_acceptaddress_when_firstdelivery"]) );
        $order["deliveryer_transfer_reason"] = array_filter($order["deliveryer_transfer_reason"], trim);
        $order["deliveryer_cancel_reason"] = array_filter($order["deliveryer_cancel_reason"], trim);
        set_agent_system_config("takeout.order", $order);
        imessage(error(0, "订单相关设置成功"), referer(), "ajax");
    }

    $order = $_config["takeout"]["order"];
    if( !empty($order["deliveryer_transfer_reason"]) ) 
    {
        $order["deliveryer_transfer_reason"] = implode("\n", $order["deliveryer_transfer_reason"]);
    }

    if( !empty($order["deliveryer_cancel_reason"]) ) 
    {
        $order["deliveryer_cancel_reason"] = implode("\n", $order["deliveryer_cancel_reason"]);
    }

    include(itemplate("config/takeout-order"));
}

if( $op == "deliveryer" ) 
{
    $_W["page"]["title"] = "配送员提成";
    if( $_W["ispost"] ) 
    {
        $order = $_config["takeout"]["order"];
        $order["deliveryer_fee_type"] = (intval($_GPC["deliveryer_fee_type"]) ? intval($_GPC["deliveryer_fee_type"]) : 1);
        if( $order["deliveryer_fee_type"] == 1 ) 
        {
            $order["deliveryer_fee"] = floatval($_GPC["deliveryer_fee_1"]);
        }
        else
        {
            if( $order["deliveryer_fee_type"] == 2 ) 
            {
                $order["deliveryer_fee"] = floatval($_GPC["deliveryer_fee_2"]);
            }
            else
            {
                if( $order["deliveryer_fee_type"] == 3 ) 
                {
                    $order["deliveryer_fee"] = array( "start_fee" => floatval($_GPC["deliveryer_fee_3"]["start_fee"]), "start_km" => floatval($_GPC["deliveryer_fee_3"]["start_km"]), "pre_km" => floatval($_GPC["deliveryer_fee_3"]["pre_km"]), "max_fee" => floatval($_GPC["deliveryer_fee_3"]["max_fee"]) );
                }

            }

        }

        set_agent_system_config("takeout.order", $order);
        imessage(error(0, "配送员提成设置成功"), referer(), "ajax");
    }

    $order = $_config["takeout"]["order"];
    include(itemplate("config/takeout-deliveryer"));
}


