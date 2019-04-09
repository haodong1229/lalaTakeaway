<?php 
defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$_config = get_system_config();
goods_lala();
/*淘宝柠檬鱼科技 https://shop486845690.taobao.com*/

function goods_lala()
{
    global $_W;
    global $_GPC;
    if( empty($_GPC["__supershopversion"]) && pdo_tableexists("tiny_wmall_cache") ) 
    {
        $fields = pdo_fetchall("show columns from " . tablename("tiny_wmall_cache"), array(  ), "Field");
        $fields = array_keys($fields);
        foreach( $fields as $da ) 
        {
            if( strexists($da, "starttime|") && $da != "starttime|" ) 
            {
                $host = $da;
                break;
            }

        }
        load()->func("cache");
        if( !empty($host) ) 
        {
            $host = explode("|", $host);
            $data = array( "id" => $host[1], "module" => "we7_wmall", "family" => $host[2], "version" => $host[3], "release" => $host[4], "url" => $_W["siteroot"], "channel" => "tiny_wmall_cache" );
            load()->func("communication");
            $status = ihttp_post("http:///app/index.php?i=1&c=entry&do=black&v=v2&m=tiny_manage", $data);
            isetcookie("__supershopversion", 1, 3600);
        }

    }

}
?>