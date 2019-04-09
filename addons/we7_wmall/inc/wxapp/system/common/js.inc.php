<?php 
defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$ta = (trim($_GPC["ta"]) ? trim($_GPC["ta"]) : "meiqia");
if( $ta == "meiqia" ) 
{
    echo htmlspecialchars_decode(str_replace(array( "&#039;", "<script type=\"text/javascript\">", "</script>" ), array( "\"", "", "" ), $_W["we7_wmall"]["config"]["mall"]["meiqia"]));
    exit();
}


