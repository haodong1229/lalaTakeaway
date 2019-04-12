<?php defined('IN_IA') or exit('Access Denied');?><div class="second-sidebar-title">顾客</div>
<div class="nav slimscroll">
	<div class="menu-header">概况</div>
	<ul class="menu-item">
		<?php  if(check_perm('member.index')) { ?>
			<li <?php  if($_W['_action'] == 'index') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('member/index');?>">顾客概况</a>
			</li>
		<?php  } ?>
		<?php  if(check_perm('member.list')) { ?>
			<li <?php  if($_W['_action'] == 'list') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('member/list/list');?>">顾客列表</a>
			</li>
		<?php  } ?>
		<?php  if(check_perm('member.groups')) { ?>
			<li <?php  if($_W['_action'] == 'groups') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('member/groups')?>">顾客等级</a>
			</li>
		<?php  } ?>

		<!-- <li <?php  if($_W['_action'] == 'sync') { ?>class="active"<?php  } ?>>
			<a href="<?php  echo iurl('member/sync/index');?>">同步顾客数据</a>
		</li> -->

	</ul>
	<div class="menu-header">顾客信息</div>
	<ul class="menu-item">
		<?php  if(check_perm('member.address')) { ?>
			<li <?php  if($_W['_action'] == 'address') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('member/address');?>">顾客地址</a>
			</li>
		<?php  } ?>
		<?php  if(check_perm('member.coupon')) { ?>
			<li <?php  if($_W['_action'] == 'coupon') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('member/coupon');?>">顾客代金券</a>
			</li>
		<?php  } ?>
		<?php  if(check_perm('member.redpacket')) { ?>
			<li <?php  if($_W['_action'] == 'redpacket') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('member/redpacket');?>">顾客红包</a>
			</li>
		<?php  } ?>
	</ul>
	<div class="menu-header">财务</div>
	<ul class="menu-item">
		<?php  if(check_perm('member.recharge')) { ?>
			<li <?php  if($_W['_action'] == 'recharge') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('member/recharge');?>">充值明细</a>
			</li>
		<?php  } ?>
		<?php  if(check_perm('member.credit')) { ?>
			<li <?php  if($_W['_action'] == 'credit' && $_GPC['credit'] == 'credit1') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('member/credit', array('credit' => 'credit1'));?>">积分明细</a>
			</li>
			<li <?php  if($_W['_action'] == 'credit' && $_GPC['credit'] == 'credit2') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('member/credit', array('credit' => 'credit2'));?>">余额明细</a>
			</li>
		<?php  } ?>
	</ul>
</div>