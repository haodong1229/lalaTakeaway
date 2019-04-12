<?php  defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
mload()->model("plugin");
pload()->model("yinsihao");
p("号码加密接口测试");
$result = yinsihao_bind(794, "member");
p($result);
?>