<?php defined('IN_IA') or exit('Access Denied');?><?php  include itemplate('public/header', TEMPLATE_INCLUDEPATH);?>
	<div class="page clearfix">
		<div class="form-horizontal">
			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active"><a href="#takeout" aria-controls="takeout" role="tab" data-toggle="pill">外卖</a></li>
				<?php  if(check_plugin_perm('errander')) { ?>
					<li role="presentation"><a href="#errander" aria-controls="errander" role="tab" data-toggle="pill">跑腿</a></li>
				<?php  } ?>
				<?php  if(check_plugin_perm('diypage')) { ?>
					<li role="presentation"><a href="#diyPages" aria-controls="diyPages" role="tab" data-toggle="pill">自定义页面</a></li>
				<?php  } ?>
				<?php  if(check_plugin_perm('creditshop') || check_plugin_perm('spread') || check_plugin_perm('ordergrant')) { ?>
					<li role="presentation"><a href="#other" aria-controls="other" role="tab" data-toggle="pill">其他链接</a></li>
				<?php  } ?>
			</ul>
			<div class="tab-content">
				<?php  if(is_array($urls)) { foreach($urls as $key => $item) { ?>
					<?php  if($key == 'diyPages') { ?>
						<div class="tab-pane <?php  if($key == 'takeout') { ?>active<?php  } ?>" role="tabpanel" id="<?php  echo $key;?>">
							<h3>自定义页面</h3>
							<?php  if(is_array($item)) { foreach($item as $item1) { ?>
								<div class="form-group">
									<label class="col-xs-12 col-sm-3 col-md-2 control-label"><?php  echo $item1['name'];?></label>
									<div class="col-sm-9 col-xs-12">
										<p class="form-control-static js-clip" data-href="pages/diy/index?id=<?php  echo $item1['id'];?>"><a href="javascript:;" title="点击复制链接">pages/diy/index?id=<?php  echo $item1['id'];?></a></p>
									</div>
								</div>
							<?php  } } ?>
						</div>
					<?php  } else { ?>
						<div class="tab-pane <?php  if($key == 'takeout') { ?>active<?php  } ?>" role="tabpanel" id="<?php  echo $key;?>">
							<?php  if(is_array($item)) { foreach($item as $item1) { ?>
								<h3><?php  echo $item1['title'];?></h3>
								<?php  if(is_array($item1['items'])) { foreach($item1['items'] as $item2) { ?>
									<div class="form-group">
										<label class="col-xs-12 col-sm-3 col-md-2 control-label"><?php  echo $item2['title'];?></label>
										<div class="col-sm-9 col-xs-12">
											<p class="form-control-static js-clip" data-href="<?php  echo $item2['url'];?>"><a href="javascript:;" title="点击复制链接"><?php  echo $item2['url'];?></a></p>
										</div>
									</div>
								<?php  } } ?>
							<?php  } } ?>
						</div>
					<?php  } ?>
				<?php  } } ?>
			</div>
		</div>
	</div>
<?php  include itemplate('public/footer', TEMPLATE_INCLUDEPATH);?>