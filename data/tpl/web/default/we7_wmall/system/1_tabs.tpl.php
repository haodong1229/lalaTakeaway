<?php defined('IN_IA') or exit('Access Denied');?>﻿<div class="second-sidebar-title">系统</div>
<div class="nav slimscroll">
	<div class="menu-header">网站</div>
	<ul class="menu-item">
		<li <?php  if($_W['_router'] == 'system/copyright/index') { ?>class="active"<?php  } ?>>
			<a href="<?php  echo iurl('system/copyright/index')?>">版权</a>
		</li>
	</ul>
	<div class="menu-header">应用管理</div>
	<ul class="menu-item">
		<li <?php  if($_W['_action'] == 'plugin') { ?>class="active"<?php  } ?>>
			<a href="<?php  echo iurl('system/plugin/index');?>">应用信息</a>
		</li>
		<?php  if(check_plugin_exist('plugincenter')) { ?>
		<li <?php  if($_W['_action'] == 'plugincenter_slide') { ?>class="active"<?php  } ?>>
			<a href="<?php  echo iurl('system/plugincenter_slide/list');?>">幻灯片管理</a>
		</li>
		<li <?php  if($_W['_action'] == 'plugincenter_plugin') { ?>class="active"<?php  } ?>>
			<a href="<?php  echo iurl('system/plugincenter_plugin/list');?>">授权应用管理</a>
		</li>
		<li <?php  if($_W['_action'] == 'plugincenter_package') { ?>class="active"<?php  } ?>>
			<a href="<?php  echo iurl('system/plugincenter_package/list');?>">应用套餐管理</a>
		</li>
		<li <?php  if($_W['_action'] == 'plugincenter_order') { ?>class="active"<?php  } ?>>
			<a href="<?php  echo iurl('system/plugincenter_order/list');?>">应用订单列表</a>
		</li>
		<li <?php  if($_W['_action'] == 'plugincenter_setting') { ?>class="active"<?php  } ?>>
			<a href="<?php  echo iurl('system/plugincenter_setting/setting');?>">应用授权设置</a>
		</li>
		<?php  } ?>
	</ul>
	<div class="menu-header">权限</div>
	<ul class="menu-item">
		<li <?php  if($_W['_action'] == 'account') { ?>class="active"<?php  } ?>>
			<a href="<?php  echo iurl('system/account/list');?>">公众号权限</a>
		</li>
		<li <?php  if($_W['_action'] == 'wxapp') { ?>class="active"<?php  } ?>>
			<a href="<?php  echo iurl('system/wxapp/list');?>">小程序权限</a>
		</li>
	</ul>
	<?php  if(!empty($_W['isfounder'])) { ?>
		<div class="menu-header">更多应用</div>
		<ul class="menu-item">
			<li>
				<a href="<?php  echo cloud_store_url();?>" target="_blank" style="color: #ff2d4b">应用中心</a>
			</li>
		</ul>
	<?php  } ?>
	<div class="menu-header">系统设置</div>
	<ul class="menu-item">
		<li <?php  if($_W['_router'] == 'system/task/') { ?>class="active"<?php  } ?>>
			<a href="<?php  echo iurl('system/task');?>">计划任务</a>
		</li>
		<li <?php  if($_W['_router'] == 'system/cache/') { ?>class="active"<?php  } ?>>
			<a href="<?php  echo iurl('system/cache');?>">更新缓存</a>
		</li>
	</ul>
	<?php  if(!empty($_GPC['f']) || in_array($_GPC['ac'], array('slog', 'development', 'runsql'))) { ?>
		<div class="menu-header">调试</div>
		<ul class="menu-item">
			<li <?php  if($_W['_router'] == 'system/slog/index') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('system/slog/index');?>">错误日志</a>
			</li>
			<li <?php  if($_W['_router'] == 'system/development/index') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('system/development/index');?>">调试模式</a>
			</li>
			<li <?php  if($_W['_router'] == 'system/runsql/index') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('system/runsql/index');?>">运行SQL</a>
			</li>
		</ul>
	<?php  } ?>
	<div class="menu-header">授权</div>
	<ul class="menu-item">
		<li <?php  if($_W['_router'] == 'system/cloud/auth') { ?>class="active"<?php  } ?>>
			<a href="<?php  echo iurl('system/cloud/auth');?>">授权管理</a>
		</li>
		<li <?php  if($_W['_router'] == 'system/cloud/upgrade') { ?>class="active"<?php  } ?>>
			<a href='https://denghuo.kf5.com/hc/kb/section/111955'>系统更新</a>
		</li>
		<li class="hide" <?php  if($_W['_router'] == 'system/cloud/log') { ?>class="active"<?php  } ?>>
			<a href="<?php  echo iurl('system/cloud/log');?>">更新日志</a>
		</li>
	</ul>
</div>