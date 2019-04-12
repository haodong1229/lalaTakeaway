<?php  defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$op = (trim($_GPC["op"]) ? trim($_GPC["op"]) : "list");
$_W["page"]["title"] = "图标魔方";
if( $op == "list" ) 
{
	if( $_W["ispost"] ) 
	{
		if( !empty($_GPC["ids"]) ) 
		{
			foreach( $_GPC["ids"] as $key => $id ) 
			{
				if( intval($id) ) 
				{
					$row = array( "title" => trim($_GPC["titles"][$key]), "tips" => trim($_GPC["tips"][$key]), "link" => trim($_GPC["links"][$key]), "wxapp_link" => trim($_GPC["wxapp_links"][$key]), "thumb" => trim($_GPC["thumbs"][$key]), "displayorder" => intval($_GPC["displayorder"][$key]) );
					pdo_update("tiny_wmall_cube", $row, array( "uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id ));
				}
			}
		}
		imessage(error(0, "图片魔方设置成功"), iurl("dashboard/cube/list"), "ajax");
	}
	$condition = " where uniacid = :uniacid and agentid = :agentid";
	$params = array( ":uniacid" => $_W["uniacid"], ":agentid" => $_W["agentid"] );
	$cubes = pdo_fetchall("select * from " . tablename("tiny_wmall_cube") . $condition . " order by displayorder desc", $params);
	include(itemplate("dashboard/cube"));
}
if( $op == "post" ) 
{
	$id = intval($_GPC["id"]);
	if( $_W["ispost"] ) 
	{
		$updata = array( "uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "title" => trim($_GPC["title"]), "tips" => trim($_GPC["tips"]), "link" => trim($_GPC["link"]), "wxapp_link" => trim($_GPC["wxapp_link"]), "thumb" => trim($_GPC["thumb"]), "displayorder" => intval($_GPC["displayorder"]) );
		if( $id ) 
		{
			pdo_update("tiny_wmall_cube", $updata, array( "uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id ));
		}
		else 
		{
			pdo_insert("tiny_wmall_cube", $updata);
		}
		imessage(error(0, "图片魔方设置成功"), iurl("dashboard/cube/list"), "ajax");
	}
	if( !empty($id) ) 
	{
		$cube = pdo_get("tiny_wmall_cube", array( "uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => $id ));
	}
	include(itemplate("dashboard/cube"));
}
if( $op == "del" && !empty($_GPC["id"]) ) 
{
	pdo_delete("tiny_wmall_cube", array( "uniacid" => $_W["uniacid"], "agentid" => $_W["agentid"], "id" => intval($_GPC["id"]) ));
	imessage(error(0, "删除成功"), iurl("dashboard/cube/list"), "ajax");
}
?>