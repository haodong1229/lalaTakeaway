<?php 
defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$_W["page"]["title"] = "常见问题";
$helps = pdo_fetchall("select * from " . tablename("tiny_wmall_help") . " where uniacid = :uniacid order by displayorder desc, id asc", array( ":uniacid" => $_W["uniacid"] ));
include(itemplate("home/help"));

