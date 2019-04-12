<?php defined('IN_IA') or exit('Access Denied');?><!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title><?php  if(empty($_W['page']['title'])) { ?><?php  echo $_W['we7_wmall']['config']['mall']['title'];?><?php  } else { ?><?php  echo $_W['page']['title'];?><?php  } ?></title>
		<meta name="viewport" content="initial-scale=1, maximum-scale=1">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<link rel="stylesheet" href="../addons/we7_wmall/static/js/components/light7/light7.min.css" />
		<link rel="stylesheet" href="../addons/we7_wmall/template/mobile/wmall/<?php  echo $_W['mobile_tpl'];?>/static/css/index.css?t=<?php  echo time();?>" />
		<script type='text/javascript' src='../addons/we7_wmall/static/css/iconfont.js' charset='utf-8'></script>
		<link rel="stylesheet" href="../addons/we7_wmall/static/css/iconfont.css"/>
		<script type='text/javascript' src="<?php  echo WE7_WMALL_LOCAL?>static/js/components/jquery/jquery-2.2.1.min.js"></script>
		<script type='text/javascript' src="<?php  echo WE7_WMALL_LOCAL?>static/js/components/jquery/jquery.extend.js"></script>
		<?php  echo iregister_jssdk(false);?>
		<script type='text/javascript' src="<?php  echo WE7_WMALL_LOCAL?>static/js/require.js"></script>
		<script type='text/javascript' src="<?php  echo WE7_WMALL_LOCAL?>static/js/iconfig-app.js"></script>
		<script type='text/javascript'>
			<?php  if(!defined('IS_ROUTER')) { ?>
				$.config = {router: false};
			<?php  } else { ?>
				$.config = {router: true};
			<?php  } ?>
			var we7_wmall = {prefix: "<?php  echo $_W['config']['cookie']['pre'];?>", pluginStaticRoot: "../addons/we7_wmall/plugin/<?php  echo $_W['_plugin']['name'];?>/static/js/"};
			require(['tiny'], function(tiny){tiny.init({siteUrl: "<?php  echo $_W['siteroot'];?>", baseUrl: "<?php  echo imurl('ROUTES')?>", attachUrl: "<?php  echo $_W['attachurl'];?>", uniacid: "<?php  echo $_W['uniacid'];?>"});});
		</script>
		<script type='text/javascript' src='../addons/we7_wmall/static/js/components/light7/light7.min.js?t=1000' charset='utf-8'></script>
		<script type="text/javascript" src="../addons/we7_wmall/static/js/components/light7/light7-swiper.min.js"></script>
		<script type="text/javascript" src="../addons/we7_wmall/static/js/components/light7/i18n/cn.min.js"></script>
		<script type="text/javascript" src="../addons/we7_wmall/static/js/components/light7/common.js?t=20000"></script>
		<style>
			<?php  if(!empty($_config_mall['lazyload_goods'])) { ?>
				img.lazyload{background: url("<?php  echo tomedia($_config_mall['lazyload_goods'])?>") center center no-repeat;}
			<?php  } ?>
			<?php  if(!empty($_config_mall['lazyload_store'])) { ?>
				img.lazyload.lazyload-store{background: url("<?php  echo tomedia($_config_mall['lazyload_store'])?>") center center no-repeat;}
			<?php  } ?>
		</style>
	</head>
	<body>


