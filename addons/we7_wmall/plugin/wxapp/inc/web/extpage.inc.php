<?php  defined("IN_IA") or exit( "Access Denied" );
global $_W;
global $_GPC;
$op = (trim($_GPC["op"]) ? trim($_GPC["op"]) : "basic");
if( $op == "basic" ) 
{
	$_W["page"]["title"] = "页面配置";
	$titles = array( "navigationBarTitleText" => "导航栏标题文字内容", "navigationBarBackgroundColor" => "导航栏背景颜色", "navigationBarTextStyle" => "导航栏标题颜色" );
	$extpages = array( "pages/home/index" => array( "title" => "平台首页", "key" => "pages/home/index" ), "pages/home/category" => array( "title" => "商家分类页", "key" => "pages/home/category" ), "pages/member/mine" => array( "title" => "会员中心", "key" => "pages/member/mine" ), "pages/order/index" => array( "title" => "我的订单", "key" => "pages/order/index" ), "pages/member/coupon/index" => array( "title" => "我的代金券", "key" => "pages/member/coupon/index" ), "pages/member/address" => array( "title" => "我的收货地址", "key" => "pages/member/address" ), "pages/member/favorite" => array( "title" => "我的收藏", "key" => "pages/member/favorite" ), "pages/deliveryCard/index" => array( "title" => "配送会员卡", "key" => "pages/deliveryCard/index" ), "pages/channel/coupon" => array( "title" => "领券中心", "key" => "pages/channel/coupon" ), "pages/member/recharge" => array( "title" => "余额充值", "key" => "pages/member/recharge" ), "pages/channel/brand" => array( "title" => "优选商家", "key" => "pages/channel/brand" ) );
	if( $_W["ispost"] ) 
	{
		set_plugin_config("wxapp.extPages", $_GPC["pages"]);
		imessage(error(0, "页面配置成功"), "refresh", "ajax");
	}
	$config_extpage = get_plugin_config("wxapp.extPages");
	include(itemplate("extpage"));
}
?>