<?php  defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$op = (trim($_GPC["op"]) ? trim($_GPC["op"]) : "list");
if( $op == "post" ) 
{
	$_W["page"]["title"] = "编辑幻灯片";
	$id = intval($_GPC["id"]);
	if( 0 < $id ) 
	{
		$slide = pdo_get("tiny_wmall_slide", array( "uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id ));
		if( empty($slide) ) 
		{
			imessage("幻灯片不存在或已删除", referer(), "error");
		}
	}
	if( $_W["ispost"] ) 
	{
		$title = (trim($_GPC["title"]) ? trim($_GPC["title"]) : imessage(error(-1, "标题不能为空"), "", "ajax"));
		$data = array( "uniacid" => $_W["uniacid"], "title" => $title, "agentid" => $_W["agentid"], "thumb" => trim($_GPC["thumb"]), "link" => trim($_GPC["link"]), "displayorder" => intval($_GPC["displayorder"]), "type" => trim($_GPC["type"]), "status" => intval($_GPC["status"]), "wxapp_link" => trim($_GPC["wxapp_link"]) );
		if( !empty($slide) ) 
		{
			pdo_update("tiny_wmall_slide", $data, array( "uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id ));
		}
		else 
		{
			pdo_insert("tiny_wmall_slide", $data);
		}
		imessage(error(0, "编辑幻灯片成功"), iurl("dashboard/slide/list"), "ajax");
	}
}
if( $op == "list" ) 
{
	$_W["page"]["title"] = "幻灯片列表";
	$condition = " where uniacid = :uniacid and agentid = :agentid";
	$params = array( ":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"] );
	$type = (isset($_GPC["type"]) ? trim($_GPC["type"]) : "");
	if( !empty($type) ) 
	{
		$condition .= " and type = :type";
		$params[":type"] = $type;
	}
	else 
	{
		$condition .= " and type != :type";
		$params[":type"] = "startpage";
	}
	$slides = pdo_fetchall("select * from" . tablename("tiny_wmall_slide") . $condition . " order by displayorder desc", $params);
}
if( $op == "del" ) 
{
	$id = intval($_GPC["id"]);
	pdo_delete("tiny_wmall_slide", array( "uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id ));
	imessage(error(0, "删除引导页成功"), "", "ajax");
}
if( $op == "status" ) 
{
	$id = intval($_GPC["id"]);
	$status = intval($_GPC["status"]);
	pdo_update("tiny_wmall_slide", array( "status" => $status ), array( "uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id ));
	imessage(error(0, ""), "", "ajax");
}
if( $op == "displayorder" ) 
{
	$id = intval($_GPC["id"]);
	$displayorder = intval($_GPC["displayorder"]);
	pdo_update("tiny_wmall_slide", array( "displayorder" => $displayorder ), array( "uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id ));
	imessage(error(0, ""), "", "ajax");
}
include(itemplate("dashboard/slide"));
?>