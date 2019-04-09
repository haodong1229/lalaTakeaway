<?php 
defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$op = trim($_GPC["op"]);
if( $op == "list" ) 
{
    $key = trim($_GPC["key"]);
    $data = pdo_fetchall("select * from " . tablename("account_wechats") . " where name like :key order by uniacid desc limit 50", array( ":key" => "%" . $key . "%" ), "uniacid");
    if( !empty($data) ) 
    {
        $account = array_values($data);
    }

    message(array( "errno" => 0, "message" => $account, "data" => $data ), "", "ajax");
}


