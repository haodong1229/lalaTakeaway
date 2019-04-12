<?php defined('IN_IA') or exit('Access Denied');?><div class="second-sidebar-title">
	概况
</div>
<div class="nav slimscroll">
	<?php  if(check_perm('dashboard.index')) { ?>
		<div class="menu-header">运营</div>
		<ul class="menu-item">
			<li <?php  if($_W['_action'] == 'index') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('dashboard/index');?>">运营概况</a>
			</li>
		</ul>
	<?php  } ?>
	<?php  if(check_perm('dashboard.ad') || check_perm('dashboard.slide') || check_perm('dashboard.nav') || check_perm('dashboard.notice' || 'dashboard.cube')) { ?>
		<div class="menu-header">首页</div>
		<ul class="menu-item">
			<?php  if(check_perm('dashboard.ad')) { ?>
				<li <?php  if($_W['_action'] == 'ad') { ?>class="active"<?php  } ?>>
					<a href="<?php  echo iurl('dashboard/ad');?>">全屏引导页</a>
				</li>
			<?php  } ?>
			<?php  if(check_perm('dashboard.slide')) { ?>
				<li <?php  if($_W['_action'] == 'slide') { ?>class="active"<?php  } ?>>
					<a href="<?php  echo iurl('dashboard/slide');?>">幻灯片</a>
				</li>
			<?php  } ?>
			<?php  if(check_perm('dashboard.nav')) { ?>
				<li <?php  if($_W['_action'] == 'nav') { ?>class="active"<?php  } ?>>
					<a href="<?php  echo iurl('dashboard/nav');?>">导航图标</a>
				</li>
			<?php  } ?>
			<?php  if(check_perm('dashboard.notice')) { ?>
				<li <?php  if($_W['_action'] == 'notice') { ?>class="active"<?php  } ?>>
					<a href="<?php  echo iurl('dashboard/notice');?>">公告</a>
				</li>
			<?php  } ?>
			<?php  if(check_perm('dashboard.cube')) { ?>
				<li <?php  if($_W['_action'] == 'cube') { ?>class="active"<?php  } ?>>
					<a href="<?php  echo iurl('dashboard/cube');?>">图片魔方</a>
				</li>
			<?php  } ?>
		</ul>
	<?php  } ?>
</div>
