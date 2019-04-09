<?php 
defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
collect_wxapp_formid();
if( !empty($_GPC["filter"]) ) 
{
    $_GPC["filter"] = json_decode(htmlspecialchars_decode($_GPC["filter"]), true);
    foreach( $_GPC["filter"] as $key => $val ) 
    {
        $_GPC[$key] = $val;
    }
}

$_W["ITIMESTAMP"] = cache_read("itime");
$_W["ITIMESTAMP"] = intval($_W["ITIMESTAMP"]);
?>