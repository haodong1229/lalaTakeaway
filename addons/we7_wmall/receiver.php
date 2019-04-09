<?php 
defined("IN_IA") or exit( "Access Denied" );
include(IA_ROOT . "/addons/we7_wmall/version.php");
include("defines.php");
include("model.php");

/*淘宝柠檬鱼科技 https://shop486845690.taobao.com*/

class We7_wmallModuleReceiver extends WeModuleReceiver
{
    public function receive()
    {
        global $_W;
    }

}


