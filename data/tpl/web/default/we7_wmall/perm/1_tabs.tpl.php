<?php defined('IN_IA') or exit('Access Denied');?><div class="second-sidebar-title">
	权限系统
</div>
<div class="nav slimscroll">
	<div class="menu-header">角色</div>
	<ul class="menu-item">
		<li <?php  if($_W['_action'] == 'role') { ?>class="active"<?php  } ?>>
			<a href="<?php  echo iurl('perm/role/list');?>">角色管理</a>
		</li>
	</ul>
	<div class="menu-header">操作员</div>
	<ul class="menu-item">
		<li <?php  if($_W['_action'] == 'user') { ?>class="active"<?php  } ?>>
			<a href="<?php  echo iurl('perm/user/list');?>">操作员管理</a>
		</li>
	</ul>
	<div class="menu-header">操作日志</div>
	<ul class="menu-item">
		<li <?php  if($_W['_action'] == 'operatelog') { ?>class="active"<?php  } ?>>
			<a href="<?php  echo iurl('perm/operatelog/list');?>">操作日志</a>
		</li>
	</ul>
</div>
