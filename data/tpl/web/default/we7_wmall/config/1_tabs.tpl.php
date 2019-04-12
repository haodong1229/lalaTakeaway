<?php defined('IN_IA') or exit('Access Denied');?><div class="second-sidebar-title">
	设置
</div>
<div class="nav slimscroll">
	<?php  if(check_perm('config.mall')) { ?>
		<div class="menu-header">平台</div>
		<ul class="menu-item">
			<li <?php  if($_W['_router'] == 'config/mall/basic') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('config/mall/basic');?>">基础设置</a>
			</li>
			<li <?php  if($_W['_router'] == 'config/mall/follow') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('config/mall/follow');?>">分享及关注</a>
			</li>
			<li <?php  if($_W['_router'] == 'config/mall/close') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('config/mall/close');?>">平台状态</a>
			</li>
			<li <?php  if($_W['_router'] == 'config/mall/oauth') { ?>class="active"<?php  } ?>>
			<a href="<?php  echo iurl('config/mall/oauth');?>">oAuth设置</a>
			</li>
		</ul>
	<?php  } ?>
	<?php  if(check_perm('config.trade')) { ?>
		<div class="menu-header">交易</div>
		<ul class="menu-item">
			<li <?php  if($_W['_router'] == 'config/trade/payment') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('config/trade/payment');?>">支付方式</a>
			</li>
			<li <?php  if($_W['_router'] == 'config/trade/paycallback') { ?>class="active"<?php  } ?>>
			<a href="<?php  echo iurl('config/trade/paycallback');?>">支付回调</a>
			</li>
			<li <?php  if($_W['_router'] == 'config/trade/recharge') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('config/trade/recharge');?>">充值</a>
			</li>
			<li <?php  if($_W['_router'] == 'config/trade/getcash') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('config/trade/getcash');?>">提现方式</a>
			</li>
		</ul>
	<?php  } ?>
	<?php  if(check_perm('config.notice')) { ?>
		<div class="menu-header">消息推送</div>
		<ul class="menu-item">
			<li <?php  if($_W['_router'] == 'config/notice/wxtemplate') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('config/notice/wxtemplate');?>">模板消息</a>
			</li>
			<li <?php  if($_W['_router'] == 'config/notice/sms') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('config/notice/sms');?>">短信消息</a>
			</li>
		</ul>
	<?php  } ?>
	<?php  if(check_perm('config.sms')) { ?>
		<div class="menu-header">短信平台</div>
		<ul class="menu-item">
			<li <?php  if($_W['_router'] == 'config/sms/set') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('config/sms/set');?>">短信接入</a>
			</li>
			<li <?php  if($_W['_router'] == 'config/sms/template') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('config/sms/template');?>">短信模板</a>
			</li>
			<li <?php  if($_W['_router'] == 'config/sms/verify') { ?>class="active"<?php  } ?>>
			<a href="<?php  echo iurl('config/sms/verify');?>">短信验证</a>
			</li>
		</ul>
	<?php  } ?>
	<?php  if(check_perm('config.takeout')) { ?>
	<div class="menu-header">外卖</div>
	<ul class="menu-item">
		<li <?php  if($_W['_router'] == 'config/takeout/range') { ?>class="active"<?php  } ?>>
		<a href="<?php  echo iurl('config/takeout/range');?>">服务范围</a>
		</li>
		<li <?php  if($_W['_router'] == 'config/takeout/order') { ?>class="active"<?php  } ?>>
			<a href="<?php  echo iurl('config/takeout/order');?>" style="color:red">订单相关</a>
		</li>
		<!--<li <?php  if($_W['_router'] == 'config/takeout/deliveryer') { ?>class="active"<?php  } ?>>
			<a href="<?php  echo iurl('config/takeout/deliveryer');?>">配送员提成</a>
		</li>-->
	</ul>
	<?php  } ?>
	<?php  if(check_perm('config.store')) { ?>
		<div class="menu-header">商户</div>
		<ul class="menu-item">
			<li <?php  if($_W['_router'] == 'config/store/recommend') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('config/store/recommend');?>">为您优选</a>
			</li>
			<li <?php  if($_W['_router'] == 'config/store/delivery') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('config/store/delivery');?>">配送模式</a>
			</li>
			<li <?php  if($_W['_router'] == 'config/store/serve_fee') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('config/store/serve_fee');?>">服务费率</a>
			</li>
			<li <?php  if($_W['_router'] == 'config/store/settle') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('config/store/settle');?>">商户入驻</a>
			</li>
			<li <?php  if($_W['_router'] == 'config/store/activity') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('config/store/activity');?>">商户活动</a>
			</li>
			<li <?php  if($_W['_router'] == 'config/store/extra') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('config/store/extra');?>">其他批量操作</a>
			</li>
		</ul>
	<?php  } ?>
	<?php  if(check_perm('config.deliveryer')) { ?>
	<div class="menu-header">配送员</div>
	<ul class="menu-item">
		<li <?php  if($_W['_router'] == 'config/deliveryer/settle') { ?>class="active"<?php  } ?>>
		<a href="<?php  echo iurl('config/deliveryer/settle');?>">配送员申请</a>
		</li>
		<li <?php  if($_W['_router'] == 'config/deliveryer/cash') { ?>class="active"<?php  } ?>>
		<a href="<?php  echo iurl('config/deliveryer/cash');?>">提成及提现</a>
		</li>
		<li <?php  if($_W['_router'] == 'config/deliveryer/extra') { ?>class="active"<?php  } ?>>
		<a href="<?php  echo iurl('config/deliveryer/extra');?>">其他设置</a>
		</li>
	</ul>
	<?php  } ?>
	<?php  if(check_perm('config.activity')) { ?>
		<div class="menu-header">优惠活动相关</div>
		<ul class="menu-item">
			<li <?php  if($_W['_router'] == 'config/activity/newmember') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('config/activity/newmember');?>">新用户</a>
			</li>
			<li <?php  if($_W['_router'] == 'config/activity/notice') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('config/activity/notice');?>">红包到期通知</a>
			</li>
			<li <?php  if($_W['_router'] == 'config/activity/activityother') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('config/activity/activityother');?>">其他 / 代金券</a>
			</li>
		</ul>		
	<?php  } ?>
	<?php  if(check_perm('config.sensitive')) { ?>
		<div class="menu-header">敏感词</div>
		<ul class="menu-item">
			<li <?php  if($_W['_router'] == 'config/sensitive') { ?>class="active"<?php  } ?>>
			<a href="<?php  echo iurl('config/sensitive');?>">敏感词过滤</a>
			</li>
		</ul>
	<?php  } ?>
	<?php  if(check_perm('config.member')) { ?>
		<div class="menu-header">顾客设置</div>
		<ul class="menu-item">
			<li <?php  if($_W['_router'] == 'config/member/group') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('config/member');?>">顾客设置</a>
			</li>
			<li <?php  if($_W['_router'] == 'config/member/address') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('config/member/address');?>">收货地址设置</a>
			</li>
		</ul>
	<?php  } ?>
	<?php  if(0 && (check_plugin_exist('managerApp') || check_plugin_exist('customerApp') || check_plugin_exist('deliveryerApp'))) { ?>
		<div class="menu-header">APP设置</div>
		<ul class="menu-item">
			<?php  if(check_plugin_exist('deliveryerApp')) { ?>
				<li <?php  if($_W['_router'] == 'config/app/deliveryer') { ?>class="active"<?php  } ?>>
					<a href="<?php  echo iurl('config/app/deliveryer');?>">配送员APP</a>
				</li>
			<?php  } ?>
			<?php  if(check_plugin_exist('managerApp')) { ?>
				<li <?php  if($_W['_router'] == 'config/app/manager') { ?>class="active"<?php  } ?>>
					<a href="<?php  echo iurl('config/app/manager');?>">商家APP</a>
				</li>
			<?php  } ?>
			<?php  if(check_plugin_exist('customerApp')) { ?>
				<li <?php  if($_W['_router'] == 'config/app/customer') { ?>class="active"<?php  } ?>>
					<a href="<?php  echo iurl('config/app/customer');?>">顾客APP</a>
				</li>
			<?php  } ?>
		</ul>
	<?php  } ?>
	<?php  if(check_perm('config.label') || check_perm('config.report') || check_perm('config.help')) { ?>
		<div class="menu-header">其他</div>
		<ul class="menu-item">
			<?php  if(check_perm('config.label')) { ?>
				<li <?php  if($_W['_router'] == 'config/label/TY_store_label') { ?>class="active"<?php  } ?>>
					<a href="<?php  echo iurl('config/label/TY_store_label');?>">商户标签</a>
				</li>
				<li <?php  if($_W['_router'] == 'config/label/TY_delivery_label') { ?>class="active"<?php  } ?>>
					<a href="<?php  echo iurl('config/label/TY_delivery_label')?>">配送标签</a>
				</li>
			<?php  } ?>
			<?php  if(check_perm('config.report')) { ?>
				<li <?php  if($_W['_router'] == 'config/report/list') { ?>class="active"<?php  } ?>>
					<a href="<?php  echo iurl('config/report/list');?>">商户举报类型</a>
				</li>
			<?php  } ?>
			<?php  if(check_perm('config.help')) { ?>
				<li <?php  if($_W['_action'] == 'help') { ?>class="active"<?php  } ?>>
					<a href="<?php  echo iurl('config/help/list');?>">常见问题</a>
				</li>
			<?php  } ?>
		</ul>
	<?php  } ?>
	<?php  if(check_perm('config.cover')) { ?>
		<div class="menu-header">入口</div>
		<ul class="menu-item">
			<li <?php  if($_W['_router'] == 'config/cover/index') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('config/cover/index');?>">平台入口</a>
			</li>
			<li <?php  if($_W['_router'] == 'config/cover/manage') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('config/cover/manage');?>">商家管理入口</a>
			</li>
			<li <?php  if($_W['_router'] == 'config/cover/settle') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('config/cover/settle');?>">商家入驻入口</a>
			</li>
			<li <?php  if($_W['_router'] == 'config/cover/delivery') { ?>class="active"<?php  } ?>>
				<a href="<?php  echo iurl('config/cover/delivery');?>">配送员入口</a>
			</li>
		</ul>
	<?php  } ?>
</div>
