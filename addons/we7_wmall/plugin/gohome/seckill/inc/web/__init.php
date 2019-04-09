<?php 
defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
/*淘宝柠檬鱼科技 https://shop486845690.taobao.com*/

function seckill_all_times()
{
    $data = array(  );
    for( $i = 0; $i < 24; $i++ ) 
    {
        $data[] = $i;
    }
    return $data;
}


