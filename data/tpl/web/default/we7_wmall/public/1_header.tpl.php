<?php defined('IN_IA') or exit('Access Denied');?><?php  include itemplate('public/header-base', TEMPLATE_INCLUDEPATH);?>
<?php  if(defined('IN_AGENT') || defined('IN_MERCHANT')) { ?>
<style>
	/*隐藏上传图片弹出层删除分组的按钮*/
	#image .category .category-menu .ng-scope .edit .color-red{
		display: none!important;
	}
	/*隐藏移动按钮*/
	.material-head .btn-group button{
		display: none;
	}
</style>
<?php  } ?>
<body>
	<?php  if(defined('IN_MANAGE')) { ?>
	<div id="app-sidebar">
		<div id="app-first-sidebar">
			<a class="team-logo-wrap" href="">
				<div class="team-logo">
					<img width="32px" height="32px" src="<?php  echo tomedia($_W['we7_wmall']['config']['mall']['logo']);?>" alt="">
				</div>
				<span class="plateform-name"><?php  echo $_W['we7_wmall']['config']['mall']['title'];?></span>
			</a>
			<div class="nav slimscroll">
				<ul class="clearfix">
					<?php  if(check_perm('dashboard')) { ?>
						<li class="<?php  if($_W['_controller'] == 'dashboard') { ?>active<?php  } ?>">
							<a href="<?php  echo iurl('dashboard/index');?>">
								<i class="icon icon-scan"></i>
								概况
							</a>
						</li>
					<?php  } ?>
					<?php  if(check_perm('order')) { ?>
						<li class="<?php  if($_W['_controller'] == 'order') { ?>active<?php  } ?>">
							<a href="<?php  echo iurl('order/takeout/list');?>">
								<i class="icon icon-order"></i>
								订单
							</a>
						</li>
					<?php  } ?>
					<?php  if(check_perm('paycenter')) { ?>
						<li class="<?php  if($_W['_controller'] == 'paycenter') { ?>active<?php  } ?>">
							<a href="<?php  echo iurl('paycenter/paybill');?>">
								<i class="icon icon-order"></i>
								当面付
							</a>
						</li>
					<?php  } ?>
					<?php  if(check_perm('statcenter')) { ?>
						<li class="<?php  if($_W['_controller'] == 'statcenter') { ?>active<?php  } ?>">
							<a href="<?php  echo iurl('statcenter/takeout');?>">
								<i class="icon icon-data"></i>
								数据
							</a>
						</li>
					<?php  } ?>
					<?php  if(check_perm('merchant')) { ?>
						<li class="<?php  if($_W['_controller'] == 'merchant') { ?>active<?php  } ?>">
							<a href="<?php  echo iurl('merchant/store/list');?>">
								<i class="icon icon-shop"></i>
								商户
							</a>
						</li>
					<?php  } ?>
					<?php  if(check_perm('service')) { ?>
						<li class="<?php  if($_W['_controller'] == 'service') { ?>active<?php  } ?>">
							<a href="<?php  echo iurl('service/comment');?>">
								<i class="icon icon-refund"></i>
								售后
							</a>
						</li>
					<?php  } ?>
					<?php  if(check_perm('deliveryer')) { ?>
						<li class="<?php  if($_W['_controller'] == 'deliveryer') { ?>active<?php  } ?>">
							<a href="<?php  echo iurl('deliveryer/plateform/list');?>">
								<i class="icon icon-people"></i>
								配送员
							</a>
						</li>
					<?php  } ?>
					<?php  if(check_perm('clerk')) { ?>
						<li class="<?php  if($_W['_controller'] == 'clerk') { ?>active<?php  } ?>">
							<a href="<?php  echo iurl('clerk/account/list');?>">
								<i class="icon icon-person2"></i>
								店员
							</a>
						</li>
					<?php  } ?>
					<?php  if(check_perm('member')) { ?>
						<li class="<?php  if($_W['_controller'] == 'member') { ?>active<?php  } ?>">
							<a href="<?php  echo iurl('member/index');?>">
								<i class="icon icon-peoplelist"></i>
								顾客
							</a>
						</li>
					<?php  } ?>
					<?php  if(check_perm('iwxapp')) { ?>
					<li class="<?php  if($_W['_controller'] == 'wxapp') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('iwxapp/index');?>" style="color:red;">
							<i class="icon icon-weixin" ></i>
							小程序
						</a>
					</li>
					<?php  } ?>
					<li class="<?php  if((defined('IN_PLUGIN') && $_W['_controller'] != 'wxapp') || $_W['_controller'] == 'plugin') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('plugin/index');?>">
							<i class="icon icon-app"></i>
							应用
						</a>
					</li>
					<?php  if(check_perm('config')) { ?>
						<li class="<?php  if($_W['_controller'] == 'config') { ?>active<?php  } ?>">
							<a href="<?php  echo iurl('config/mall');?>">
								<i class="icon icon-settings"></i>
								设置
							</a>
						</li>
					<?php  } ?>
					<?php  if($_W['perms'] == 'all') { ?>
						<li class="<?php  if($_W['_controller'] == 'perm') { ?>active<?php  } ?>">
							<a href="<?php  echo iurl('perm/role');?>">
								<i class="icon icon-lock"></i>
								权限
							</a>
						</li>
					<?php  } ?>
					<?php  if(!empty($_W['isfounder'])) { ?>
						<li class="<?php  if($_W['_controller'] == 'system') { ?>active<?php  } ?>">
							<a href="<?php  echo iurl('system/plugin/index');?>">
								<i class="icon icon-switch"></i>
								系统
							</a>
						</li>
					<?php  } ?>
				</ul>
			</div>
		</div>
		<div id="app-second-sidebar">
			<?php  build_menu();?>
		</div>
	</div>
	<div id="app-container">
		<div class="sys-message hide"></div>
		<div id="app-third-sidebar" class="clearfix">
			<ul class="nav-list">
				<li><a href="javascritp:;"><?php  echo $_W['page']['title'];?></a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
					<a href="javascript:;" class="dropdown-toggle account-info" data-toggle="dropdown">
						<span class="pic">
							<img src="<?php  echo tomedia($_W['we7_wmall']['config']['mall']['logo']);?>" alt=""/>
						</span>
						<?php  echo $_W['account']['name'];?> <span class="caret"></span>
					</a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="<?php  echo url('account/display');?>" target="_blank"><i class="icon icon-similar"></i> 切换公众号</a></li>
						<li><a href="<?php  echo url('account/post', array('uniacid' => $_W['uniacid'], 'acid' => $_W['uniacid']));?>" target="_blank"><i class="icon icon-weixin"></i> 编辑公众号</a></li>
						<li><a href="<?php  echo url('utility/emulator');?>" target="_blank"><i class="icon icon-machinery"></i> 模拟测试</a></li>
						<?php  if(!empty($_W['isfounder'])) { ?>
							<li class="divider"></li>
							<li><a href="<?php  echo url('user/profile');?>" target="_blank"><i class="icon icon-lock"></i> 修改密码</a></li>
						<?php  } ?>
						<li><a href="<?php  echo url('account/display');?>" target="_blank"><i class="icon icon-back"></i> 返回系统</a></li>
					</ul>
				</li>
			</ul>
		</div>
		<div id="app-inner">
	<?php  } else if(defined('IN_AGENT')) { ?>
	<div id="app-sidebar">
		<div id="app-first-sidebar">
			<a class="team-logo-wrap" href="">
				<div class="team-logo">
					<img width="32px" height="32px" src="<?php  echo tomedia($_W['we7_wmall']['config']['mall']['logo']);?>" alt="">
				</div>
				<span class="plateform-name"><?php  echo $_W['agent']['area'];?></span>
			</a>
			<div class="nav slimscroll">
				<ul class="clearfix">
					<li class="<?php  if($_W['_controller'] == 'dashboard') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('dashboard/index');?>">
							<i class="icon icon-scan"></i>
							概况
						</a>
					</li>
					<li class="<?php  if($_W['_controller'] == 'order') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('order/takeout/list');?>">
							<i class="icon icon-order"></i>
							订单
						</a>
					</li>
					<li class="<?php  if($_W['_controller'] == 'paycenter') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('paycenter/paybill');?>">
							<i class="icon icon-order"></i>
							当面付
						</a>
					</li>
					<li class="<?php  if($_W['_controller'] == 'statcenter') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('statcenter/index');?>">
							<i class="icon icon-data"></i>
							数据
						</a>
					</li>
					<li class="<?php  if($_W['_controller'] == 'merchant') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('merchant/store/list');?>">
							<i class="icon icon-shop"></i>
							商户
						</a>
					</li>
					<li class="<?php  if($_W['_controller'] == 'service') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('service/comment');?>">
							<i class="icon icon-refund"></i>
							售后
						</a>
					</li>
					<li class="<?php  if($_W['_controller'] == 'finance') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('finance/order');?>">
							<i class="icon icon-refund"></i>
							财务
						</a>
					</li>
					<li class="<?php  if($_W['_controller'] == 'deliveryer') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('deliveryer/plateform/list');?>">
							<i class="icon icon-people"></i>
							配送员
						</a>
					</li>
					<li class="<?php  if($_W['_controller'] == 'clerk') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('clerk/account/list');?>">
							<i class="icon icon-person2"></i>
							店员
						</a>
					</li>
					<li class="<?php  if($_W['_controller'] == 'plugin') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('plugin/index');?>">
							<i class="icon icon-app"></i>
							应用
						</a>
					</li>
					<li class="<?php  if($_W['_controller'] == 'config') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('config/mall');?>">
							<i class="icon icon-settings"></i>
							设置
						</a>
					</li>
					<li class="<?php  if($_W['_controller'] == 'agent') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('agent/setting');?>">
							<i class="icon icon-settings"></i>
							代理
						</a>
					</li>
				</ul>
			</div>
		</div>
		<div id="app-second-sidebar">
			<?php  build_menu();?>
		</div>
	</div>
	<div id="app-container">
		<div class="sys-message hide"></div>
		<div id="app-third-sidebar" class="clearfix">
			<ul class="nav-list">
				<li><a href="javascritp:;"><?php  echo $_W['page']['title'];?></a></li>
			</ul>
			<div class="pull-right user-section">
				<?php  if(!empty($_W['agent'])) { ?>
				<div class="pull-left wm-user-name">
					<a href="javascript:;" class="text">
						<span class="names"><?php  echo $_W['agent']['title'];?>-<?php  echo $_W['agent']['realname'];?></span>
						<span><i class="fa fa-trans fa-angle-down"></i></span>
					</a>
					<ul class="drop-menu">
						<li class="page-jump"><a href="<?php  echo iurl('agent/setting')?>">修改密码</a></li>
						<li class="page-jump"><a href="<?php  echo iurl('agent/loginout')?>">退出</a></li>
					</ul>
				</div>
				<?php  } ?>
			</div>
		</div>
		<div id="app-inner">
	<?php  } else if(defined('IN_MERCHANT')) { ?>
	<div id="app-sidebar">
		<div id="app-first-sidebar">
			<a class="team-logo-wrap" href="">
				<div class="team-logo">
					<img width="32px" height="32px" src="<?php  echo tomedia($store['logo'])?>" alt="">
				</div>
				<span class="plateform-name"><?php  echo $store['title'];?></span>
			</a>
			<div class="nav slimscroll">
				<ul class="clearfix">
					<li class="<?php  if($_W['_controller'] == 'dashboard') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('store/dashboard/index');?>">
							<i class="icon icon-scan"></i>
							概况
						</a>
					</li>
					<li class="<?php  if($_W['_action'] == 'order') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('store/order/takeout');?>">
							<i class="icon icon-waimai"></i>
							外卖
						</a>
					</li>
					<li class="<?php  if($_W['_action'] == 'tangshi') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('store/tangshi/order');?>">
							<i class="icon icon-store"></i>
							店内
						</a>
					</li>
					<li class="<?php  if($_W['_action'] == 'paycenter') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('store/paycenter/paybill');?>">
							<i class="icon icon-order"></i>
							当面付
						</a>
					</li>
					<li class="<?php  if($_W['_action'] == 'goods') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('store/goods/index');?>">
							<i class="icon icon-goods"></i>
							商品
						</a>
					</li>
					<li class="<?php  if($_W['_action'] == 'service') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('store/service/comment');?>">
							<i class="icon icon-refund"></i>
							售后
						</a>
					</li>
					<li class="<?php  if($_W['_action'] == 'finance') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('store/finance/order');?>">
							<i class="icon icon-recharge"></i>
							财务
						</a>
					</li>
					<li class="<?php  if($_W['_action'] == 'statistic') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('store/statistic/order');?>">
							<i class="icon icon-data"></i>
							分析
						</a>
					</li>
					<li class="<?php  if($_W['_action'] == 'activity') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('store/activity/shopIndex');?>">
							<i class="icon icon-activity"></i>
							营销
						</a>
					</li>
					<li class="hide <?php  if($_W['_action'] == 'member') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('store/member/index');?>">
							<i class="icon icon-peoplelist"></i>
							顾客
						</a>
					</li>
					<li class="<?php  if($_W['_action'] == 'shop') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('store/shop/setting');?>">
							<i class="icon icon-settings"></i>
							门店
						</a>
					</li>
					<li class="<?php  if($_W['_action'] == 'wxapp') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('store/wxapp/index/index');?>">
							<i class="icon icon-settings"></i>
							小程序
						</a>
					</li>
					<li class="<?php  if($_W['_action'] == 'decoration') { ?>active<?php  } ?>">
						<a href="<?php  echo iurl('store/decoration/template/index');?>">
							<i class="icon icon-settings"></i>
							装修
						</a>
					</li>
					<?php  if(check_plugin_perm('gohome')) { ?>
						<li class="<?php  if($_W['_action'] == 'gohome') { ?>active<?php  } ?>">
							<a href="<?php  echo iurl('store/gohome/order');?>">
								<i class="icon icon-settings"></i>
								生活圈
							</a>
						</li>
					<?php  } ?>
				</ul>
			</div>
		</div>
		<div id="app-second-sidebar">
			<?php  build_menu();?>
		</div>
	</div>
	<div id="app-container">
		<div class="sys-message hide"></div>
		<div id="app-third-sidebar" class="clearfix">
			<ul class="nav-list">
				<li><a href="javascritp:;"><?php  echo $_W['page']['title'];?></a></li>
			</ul>
			<div class="pull-right user-section">
				<?php  if(!empty($_W['clerk'])) { ?>
				<div class="pull-left wm-user-name">
					<a href="javascript:;" class="text">
						<span class="names"><?php  echo $_W['clerk']['title'];?></span>
						<span><i class="fa fa-trans fa-angle-down"></i></span>
					</a>
					<ul class="drop-menu">
						<li class="page-jump"><a href="<?php  echo iurl('store/shop/account')?>">修改密码</a></li>
						<li class="page-jump"><a href="<?php  echo iurl('store/oauth/loginout')?>">退出</a></li>
					</ul>
				</div>
				<?php  } ?>
				<div class="pull-left wm-notice">
					<a class="page-jump notice-jump" href="<?php  echo iurl('store/shop/notice/list')?>"><i class="fa fa-bell"></i><span class="badge hide" id="notice-total">0</span></a>
				</div>
			</div>
		</div>
		<div id="app-inner">
	<?php  } ?>

