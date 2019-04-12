<?php defined('IN_IA') or exit('Access Denied');?><!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php  if(isset($title)) $_W['page']['title'] = $title?><?php  if(!empty($_W['page']['title'])) { ?><?php  echo $_W['page']['title'];?> - <?php  } ?><?php  echo $_W['page']['copyright']['sitename'];?></title>
	<meta name="keywords" content="<?php  echo $_W['page']['copyright']['keywords'];?>" />
	<meta name="description" content="<?php  echo $_W['page']['copyright']['description'];?>" />
	<link rel="shortcut icon" href="<?php  echo $_W['siteroot'];?><?php  echo $_W['config']['upload']['attachdir'];?>/<?php  if(!empty($_W['setting']['copyright']['icon'])) { ?><?php  echo $_W['setting']['copyright']['icon'];?><?php  } else { ?>images/global/wechat.jpg<?php  } ?>" />
	<link rel="stylesheet" href="<?php  echo WE7_WMALL_LOCAL?>static/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php  echo WE7_WMALL_LOCAL?>static/css/font-awesome.min.css">
	<link rel="stylesheet"  href="<?php  echo WE7_WMALL_LOCAL?>static/css/animate.css">
	<link rel="stylesheet" href="<?php  echo WE7_WMALL_LOCAL?>static/css/new.css">
	<link rel="stylesheet" href="<?php  echo WE7_WMALL_LOCAL?>static/css/common.css?t=<?php  echo time();?>">
	<link rel="stylesheet" href="<?php  echo WE7_WMALL_LOCAL?>static/css/iconfont.css">
	<script>var require = {urlArgs: 'v=<?php  echo date('YmdH');?>' };</script>
	<script src="<?php  echo $_W['siteroot'];?>web/resource/js/lib/jquery-1.11.1.min.js"></script>
	<script src="<?php  echo WE7_WMALL_LOCAL?>static/js/components/jquery/jquery.extend.js"></script>
	<script src="<?php  echo WE7_WMALL_LOCAL?>static/js/components/jquery/pace.min.js"></script>
	<script type="text/javascript">
		if(navigator.appName == 'Microsoft Internet Explorer'){
			if(navigator.userAgent.indexOf("MSIE 5.0")>0 || navigator.userAgent.indexOf("MSIE 6.0")>0 || navigator.userAgent.indexOf("MSIE 7.0")>0) {
				alert('您使用的 IE 浏览器版本过低, 推荐使用 Chrome 浏览器或 IE8 及以上版本浏览器.');
			}
		}
		window.sysinfo = {
			<?php  if(!empty($_W['uniacid'])) { ?>
				'uniacid': '<?php  echo $_W['uniacid'];?>',
			<?php  } ?>
			<?php  if(!empty($_W['acid'])) { ?>
				'acid': '<?php  echo $_W['acid'];?>',
			<?php  } ?>
			<?php  if(!empty($_W['openid'])) { ?>
				'openid': '<?php  echo $_W['openid'];?>',
			<?php  } ?>
			<?php  if(!empty($_W['uid'])) { ?>
				'uid': '<?php  echo $_W['uid'];?>',
			<?php  } ?>
			'isfounder': <?php  if(!empty($_W['isfounder'])) { ?>1<?php  } else { ?>0<?php  } ?>,
			'siteroot': '<?php  echo $_W['siteroot'];?>',
			'siteurl': '<?php  echo $_W['siteurl'];?>',
			'attachurl': '<?php  echo $_W['attachurl'];?>',
			'attachurl_local': '<?php  echo $_W['attachurl_local'];?>',
			'attachurl_remote': '<?php  echo $_W['attachurl_remote'];?>',
			<?php  if(defined('MODULE_URL')) { ?>
			'MODULE_URL': '<?php echo MODULE_URL;?>',
			<?php  } ?>
			'module' : {'url' : '<?php  if(defined('MODULE_URL')) { ?><?php echo MODULE_URL;?><?php  } ?>', 'name' : '<?php  if(defined('IN_MODULE')) { ?><?php echo IN_MODULE;?><?php  } ?>'},
			'cookie' : {'pre': '<?php  echo $_W['config']['cookie']['pre'];?>'},
			'account' : <?php  echo json_encode($_W['account'])?>,
			'merchant' : <?php echo defined('IN_MERCHANT') ? 1 : 0?>,
			'agent' : <?php echo defined('IN_AGENT') ? 1 : 0?>
		};
	</script>
	<script src="<?php  echo $_W['siteroot'];?>web/resource/js/app/util.js"></script>
	<script src="<?php  echo $_W['siteroot'];?>web/resource/js/app/common.min.js"></script>
	<script src="<?php  echo $_W['siteroot'];?>web/resource/js/require.js"></script>
	<script src="<?php  echo $_W['siteroot'];?>web/resource/js/app/config.js?t=<?php  echo time();?>"></script>
	<script src="<?php  echo WE7_WMALL_LOCAL?>static/js/iconfig-web.js?t=<?php  echo time();?>"></script>
	<style>
		<?php  if($_W['role'] == 'merchanter') { ?>
			.material-content .material-body .item:hover .del{display: none}
		<?php  } ?>
	</style>
</head>
<body>
