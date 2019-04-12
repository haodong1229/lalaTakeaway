<?php  defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$op = (trim($_GPC["op"]) ? trim($_GPC["op"]) : "list");
if( $op == "list" ) 
{
	$_W["page"]["title"] = "分类列表";
	if( $_W["ispost"] && !empty($_GPC["ids"]) ) 
	{
		foreach( $_GPC["ids"] as $k => $v ) 
		{
			$data = array( "title" => trim($_GPC["title"][$k]), "displayorder" => intval($_GPC["displayorder"][$k]) );
			pdo_update("tiny_wmall_errander_category", $data, array( "uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => intval($v) ));
		}
		imessage(error(0, "修改成功"), iurl("errander/category/list"), "ajax");
	}
	$condition = " where uniacid = :uniacid and agentid = :agentid";
	$params = array( ":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"] );
	$categorys = pdo_fetchall("SELECT * FROM " . tablename("tiny_wmall_errander_category") . $condition . " ORDER BY displayorder DESC,id ASC", $params);
}
if( $op == "post" ) 
{
	$_W["page"]["title"] = "编辑跑腿分类";
	$id = intval($_GPC["id"]);
	if( 0 < $id ) 
	{
		$item = pdo_get("tiny_wmall_errander_category", array( "uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id ));
		if( empty($item) ) 
		{
			imessage(error(-1, "跑腿类型不存在或已删除"), referer(), "ajax");
		}
		$item["label"] = iunserializer($item["label"]);
		$item["delivery_times"] = iunserializer($item["delivery_times"]);
		$item["weight_fee"] = iunserializer($item["weight_fee"]);
	}
	if( $_W["ispost"] ) 
	{
		$title = (trim($_GPC["title"]) ? trim($_GPC["title"]) : imessage(error(-1, "标题不能为空"), "", "ajax"));
		$data = array( "uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "title" => $title, "type" => trim($_GPC["type"]), "thumb" => trim($_GPC["thumb"]), "goods_thumbs_status" => intval($_GPC["goods_thumbs_status"]), "delivery_within_days" => intval($_GPC["delivery_within_days"]), "start_fee" => trim($_GPC["start_fee"]), "start_km" => trim($_GPC["start_km"]), "pre_km" => trim($_GPC["pre_km"]), "pre_km_fee" => trim($_GPC["pre_km_fee"]), "weight_fee_status" => intval($_GPC["weight_fee_status"]), "tip_min" => (trim($_GPC["tip_min"]) < 0 ? 0 : trim($_GPC["tip_min"])), "tip_max" => (trim($_GPC["tip_max"]) < 0 ? 200 : trim($_GPC["tip_max"])), "displayorder" => intval($_GPC["displayorder"]), "rule" => htmlspecialchars_decode($_GPC["rule"]) );
		$data["label"] = array( );
		if( !empty($_GPC["label"]) ) 
		{
			foreach( $_GPC["label"] as $label ) 
			{
				if( empty($label) ) 
				{
					continue;
				}
				$data["label"][] = $label;
			}
		}
		$data["label"] = iserializer($data["label"]);
		$times = array( );
		if( !empty($_GPC["times"]["start"]) ) 
		{
			foreach( $_GPC["times"]["start"] as $key => $val ) 
			{
				$start = trim($val);
				$end = trim($_GPC["times"]["end"][$key]);
				if( empty($start) || empty($end) ) 
				{
					continue;
				}
				$times[] = array( "start" => $start, "end" => $end, "status" => intval($_GPC["times"]["status"][$key]), "fee" => intval($_GPC["times"]["fee"][$key]) );
			}
		}
		$data["delivery_times"] = iserializer($times);
		if( $data["weight_fee_status"] == 1 ) 
		{
			$weight_fee = array( "start_weight" => intval($_GPC["start_weight"]) );
			$weight = array( );
			if( !empty($_GPC["pre_kgs"]) ) 
			{
				foreach( $_GPC["pre_kgs"] as $key => $val ) 
				{
					if( empty($val) || empty($_GPC["pre_kg_fees"][$key]) ) 
					{
						continue;
					}
					$weight[$val] = $_GPC["pre_kg_fees"][$key];
				}
				ksort($weight);
				$weight_fee["weight"] = $weight;
			}
			$data["weight_fee"] = iserializer($weight_fee);
		}
		if( empty($item["id"]) ) 
		{
			pdo_insert("tiny_wmall_errander_category", $data);
		}
		else 
		{
			pdo_update("tiny_wmall_errander_category", $data, array( "uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id ));
		}
		imessage(error(0, "编辑成功"), iurl("errander/category/list"), "ajax");
	}
}
if( $op == "del" ) 
{
	$id = intval($_GPC["id"]);
	pdo_delete("tiny_wmall_errander_category", array( "uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id ));
	imessage(error(0, "删除跑腿分类成功"), "", "ajax");
}
if( $op == "status" ) 
{
	$id = intval($_GPC["id"]);
	$status = intval($_GPC["status"]);
	pdo_update("tiny_wmall_errander_category", array( "status" => $status ), array( "uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id ));
	imessage(error(0, "设置分类状态成功"), "", "ajax");
}
include(itemplate("category"));
?>