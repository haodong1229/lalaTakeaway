<?php  defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
mload()->model("cover");
$op = (trim($_GPC["op"]) ? trim($_GPC["op"]) : "index");
if( $op == "index" ) 
{
	$routers = array( "index" => array( "title" => "下单有礼入口", "url" => ivurl("/package/pages/ordergrant/index", array( ), true), "do" => "ordergrant" ) );
	$router = $routers[$op];
	$_W["page"]["title"] = $router["title"];
	if( $_W["ispost"] ) 
	{
		$keyword = (trim($_GPC["keyword"]) ? trim($_GPC["keyword"]) : imessage(error(-1, "关键词不能为空"), "", "ajax"));
		$cover = array( "keyword" => trim($_GPC["keyword"]), "title" => trim($_GPC["title"]), "thumb" => trim($_GPC["thumb"]), "description" => trim($_GPC["description"]), "do" => $router["do"], "url" => $router["url"], "status" => intval($_GPC["status"]) );
		cover_build($cover);
		imessage(error(0, "设置封面成功"), referer(), "ajax");
	}
	$cover = cover_fetch(array( "do" => $router["do"] ));
	$cover = array_merge($cover, $router);
}
if( $op == "share" ) 
{
	$_W["page"]["title"] = "分享订单入口";
	$urls = array( "share" => ivurl("/package/pages/ordergrant/share", array( ), true) );
}
include(itemplate("cover"));
?>