<?php defined('IN_IA') or exit('Access Denied');?><?php  include itemplate('public/header', TEMPLATE_INCLUDEPATH);?>
<?php  if($op == 'auth') { ?>
<div class="page clearfix">
	<h2>授权管理</h2>
	<form class="form-horizontal form form-validate" id="form1" action="" method="post" enctype="multipart/form-data">
		<div class="alert alert-info">
			模块更新日志：<a href="http://wiki.duoxunwl.com/hc/kb/section/111955/" target="_blank">http://wiki.duoxunwl.com/hc/kb/section/111955/</a>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">网站URL</label>
			<div class="col-md-6">
				<input type="text" name="url" value="<?php  echo $params['url'];?>" class="form-control" required="true" readonly/>
				<div class="help-block">网站URL</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">IP地址</label>
			<div class="col-md-6">
				<input type="text" name="ip" value="<?php  echo $params['ip'];?>" class="form-control" readonly/>
				<div class="help-block">IP地址</div>
			</div>
		</div>

		<?php  if(!empty($cache['code'])) { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">授权码</label>
				<div class="col-md-6">
					<input type="text" name="code" value="<?php  echo $cache['code'];?>" class="form-control" readonly/>
					<div class="help-block">请联系客服将IP及站点ID提交给客服, 索取授权码，保护好您的授权码，避免泄漏</div>
				</div>
			</div>
		<?php  } ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">授权状态</label>
			<div class="col-md-6">
				<p class="form-control-static">
					<?php  if(is_array($cache) && !empty($cache['code'])) { ?>
						<span class="label label-success">已授权</span>
					<?php  } else { ?>
						<span class="label label-success">已授权</span>
					<?php  } ?>
				</p>
			</div>
		</div>
		
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">当前版本</label>
			<div class="col-md-6">
				<p class="form-control-static">
					<span class="label label-info">
						<?php echo MODULE_VERSION;?>-<?php echo MODULE_RELEASE_DATE;?>
					</span>
				</p>
			</div>
		</div>
		
	</form>
</div>
<?php  } ?>

<?php  if($op == 'upgrade') { ?>
<div class="page clearfix">
	<h2>授权管理</h2>
	<form class="form-horizontal form" id="form1" action="" method="post" enctype="multipart/form-data">
		<div class="alert alert-warning">
			平台所有打印机均属于定制打印机， 客户自行购买的打印机会造成打印不兼容的问题。由自行购买打印机造成的问题, 官方不提供打印机部分的售后服务。 如需购买打印机， 请联系模块官方作者。<strong class="text-danger">QQ: 2622178042</strong>
			<br>
			<strong class="text-danger hide">
				更新时请注意备份网站数据和相关数据库文件！官方不强制要求用户跟随官方意愿进行更新尝试！
				各位在更新前， 务必关闭安全狗， 云锁，防火墙等安全防护软件， 否则将更新不成功。因未关闭安全软件造成的更新失败， 官方不会针对每个客户单独处理。
			</strong>
		</div>
		<?php  if(!empty($upgrade) && !empty($upgrade['upgrade'])) { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">版本</label>
				<div class="col-sm-10">
					<p class="form-control-static"><span class="fa fa-square-o"></span> &nbsp; 系统当前版本: <?php  echo $now_family?> <?php  echo MODULE_VERSION;?>-<?php  echo MODULE_RELEASE_DATE?></p>
					<?php  if($upgrade['release'] != MODULE_RELEASE_DATE) { ?>
					 <p class="form-control-static"><span class="fa fa-square-o"></span> &nbsp; 存在的新版本: <?php  echo $now_family?> <?php  echo $upgrade['version'];?>-<?php  echo $upgrade['release'];?></p>
					<?php  } ?>
					<div class="help-block">在一个发布版中可能存在多次补丁, 因此版本可能未更新</div>
				</div>
			</div>
			<?php  if(!empty($upgrade['logs'])) { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">更新内容</label>
					<div class="col-sm-10 col-md-10">
						<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
							<?php  if(is_array($upgrade['logs'])) { foreach($upgrade['logs'] as $log) { ?>
								<?php  $i++;?>
								<div class="panel panel-default panel-update-content">
									<div class="panel-heading" role="tab" id="heading-<?php  echo $log['addtime'];?>" data-toggle="collapse" data-parent="#accordion" href="#<?php  echo $log['addtime'];?>" aria-expanded="true" aria-controls="collapseOne">
										<h4 class="panel-title">
											<a>
												<?php  echo $log['version'];?>-<?php  echo $log['release'];?>
												<span class="pull-right"><?php  echo date('Y-m-d H:i', $log['addtime'])?></span>
											</a>
										</h4>
									</div>
									<div id="<?php  echo $log['addtime'];?>" class="panel-collapse collapse <?php  if($i == 1) { ?>in<?php  } ?>" role="tabpanel" aria-labelledby="heading-<?php  echo $log['addtime'];?>">
										<div class="panel-body">
											<?php  echo $log['message'];?>
										</div>
									</div>
								</div>
							<?php  } } ?>
						</div>
					</div>
				</div>
			<?php  } ?>
			<?php  if(!empty($upgrade['database']) && 0) { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">数据库同步情况</label>
					<div class="col-sm-10">
						<div class="help-block"><strong>注意: 重要: 本次更新涉及到数据库变动, 请做好备份.</strong></div>
						<div class="alert alert-success alert-original" style="line-height:20px;margin-top:20px;">
							<?php  if(is_array($upgrade['database'])) { foreach($upgrade['database'] as $line) { ?>
							<div class="row">
								<div class="col-xs-2 col-lg-1">表名:</div>
								<div class="col-xs-2 col-lg-2"><?php  echo $line['tablename'];?></div>
								<?php  if(!empty($line['new'])) { ?>
									<div class="col-xs-8 col-lg-9">New</div>
								<?php  } else { ?>
									<div class="col-xs-8 col-lg-9"><?php  if(!empty($line['fields'])) { ?>fields: <?php  echo $line['fields'];?>; <?php  } ?><?php  if(!empty($line['indexes'])) { ?>indexes: <?php  echo $line['indexes'];?><?php  } ?></div>
								<?php  } ?>
							</div>
							<?php  } } ?>
						</div>
					</div>
				</div>
			<?php  } ?>
			<?php  if(!empty($upgrade['scripts'])) { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">更新通告</label>
					<div class="col-sm-10 col-md-6">
						<?php  if(is_array($upgrade['scripts'])) { foreach($upgrade['scripts'] as $ver) { ?>
							<p class="form-control-static"><?php  echo $upgrade['family'];?><?php  echo $ver['version'];?><?php  echo $ver['release'];?> Build</p>
						<?php  } } ?>
					</div>
				</div>
			<?php  } ?>
			<?php  if(!empty($upgrade['files'])) { ?>
				<?php  if(!empty($_GPC['gengxin']) || !empty($cache['gengxin'])) { ?>
					<div class="form-group">
						<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">文件同步情况</label>
						<div class="col-sm-10 col-md-10">
							<div class="help-block"><strong>注意: 重要: 本次更新涉及到程序变动, 请做好备份.</strong></div>
							<div class="alert alert-info alert-original" style="line-height:20px;margin-top:20px;">
								<?php  if(is_array($upgrade['files'])) { foreach($upgrade['files'] as $line) { ?>
								<div><span style="display:inline-block; width:30px;"><?php  if(is_file(MODULE_ROOT . $line)) { ?>M<?php  } else { ?>A<?php  } ?></span><?php  echo $line;?></div>
								<?php  } } ?>
							</div>
						</div>
					</div>
				<?php  } else { ?>
					<div class="form-group">
						<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">文件同步情况</label>
						<div class="col-sm-10 col-md-10">
							<p class="form-control-static"><?php  echo count($upgrade['files']);?>个文件变动</p>
						</div>
					</div>
				<?php  } ?>
			<?php  } ?>
			<?php  if(!empty($_GPC['gengxin']) || !empty($cache['gengxin'])) { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">更新协议</label>
					<div class="col-sm-10 col-md-6">
						<div class="checkbox">
							<input type="checkbox" id="agreement_0">
							<label for="agreement_0">我已经做好了相关文件的备份工作</label>
						</div>
						<div class="checkbox">
								<input type="checkbox" id="agreement_1">
								<label for="agreement_1">认同官方的更新行为并自愿承担更新所存在的风险</label>
						</div>
						<div class="checkbox">
							<input type="checkbox" id="agreement_2">
							<label for="agreement_2">理解官方的辛勤劳动并报以感恩的心态点击更新按钮</label>
						</div>
					</div>
				</div>
			<?php  } ?>
			<div class="form-group">
				<div class="col-sm-offset-2 col-md-offset-2 col-lg-offset-1 col-xs-12 col-sm-10 col-md-10 col-lg-11">
					<?php  if(!empty($_GPC['gengxin']) || !empty($cache['gengxin'])) { ?>
						<input type="button" id="forward" value="立即更新" class="btn btn-danger" style="margin-left: 15px" />
					<?php  } else { ?>
						<a class="btn btn-primary" id="forwardwe7" href="javascript:;">去更新</a>
					<?php  } ?>
				</div>
			</div>
		<?php  } else { ?>
			<div class="form-group">
				<div class="col-sm-offset-2 col-md-offset-2 col-lg-offset-1 col-xs-12 col-sm-10 col-md-10 col-lg-11">
					<input name="submit" type="submit" value="进行文件校验" class="btn btn-primary" />
					<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
				</div>
			</div>
		<?php  } ?>
	</form>
</div>
<?php  if(!empty($upgrade) && !empty($upgrade['upgrade'])) { ?>
<script type="text/javascript">
	$('#forward').click(function(){
		var a = $("#agreement_0").is(':checked');
		var b = $("#agreement_1").is(':checked');
		var c = $("#agreement_2").is(':checked');
		if(a && b && c) {
			Notify.confirm('更新将直接覆盖本地文件, 请注意备份文件和数据. \n\n**另注意** 更新过程中不要关闭此浏览器窗口.', function() {
				location.href = "<?php  echo iurl('system/cloud/process')?>";
			});
		} else {
			Notify.error("抱歉，更新前请仔细阅读更新协议！");
			return false;
		}
	});

	$('#forwardwe7').click(function(){
		var url = "<?php  echo url('cloud/process', array('m' => 'we7_wmall', 'is_upgrade' => 1));?>";
		Notify.confirm('更新将直接覆盖本地文件, 请注意备份文件和数据. \n\n**另注意** 更新过程中不要关闭此浏览器窗口.', function() {
			location.href = url;
		});
	});
</script>
<?php  } ?>
<?php  } ?>

<?php  if($op == 'process') { ?>
<div class="page clearfix">
	<h2>更新进度</h2>
	<div class="alert alert-warning">
		<strong class="text-danger">
			各位在更新前， 务必关闭安全狗， 云锁，防火墙等安全防护软件， 否则将更新不成功。因未关闭安全软件造成的更新失败， 官方不会针对每个客户单独处理。
		</strong>
	</div>
	<form class="form-horizontal form" id="form1" action="" method="post" enctype="multipart/form-data">
	<?php  if($step == 'files') { ?>
		<?php  if(empty($packet['files'])) { ?>
			<script type="text/javascript">
				location.href = "<?php  echo iurl('system/cloud/process', array( 'step' => 'schemas'));?>";
			</script>
		<?php  } ?>
		<div class="alert alert-warning">
			正在更新系统文件, 请不要关闭窗口.
		</div>
		<div class="alert alert-warning">
			如果下载文件失败，可能造成的原因：写入失败，请仔细检查写入权限是否正确。<strong>如果出现更新失败的文件， 请刷新页面重新更新。如果出现空白页面 ，请等待1分钟左右在更新， 是服务器缓存的原因</strong>
		</div>
		<div class="alert alert-info alert-original form-horizontal ng-cloak" ng-controller="processor">
			<dl class="dl-horizontal">
				<dt>整体进度</dt>
				<dd>{{pragress}}</dd>
				<dt>正在下载文件</dt>
				<dd>{{file}}</dd>
			</dl>
			<dl class="dl-horizontal" ng-show="fails.length > 0">
				<dt>下载失败的文件</dt>
				<dd>
					<p class="text-danger" ng-repeat="file in fails" style="margin:0;">{{file}}</p>
				</dd>
			</dl>
		</div>
		<script>
			require(['angular'], function(angular){
				angular.module('app', []).controller('processor', function($scope, $http){
					$scope.files = <?php  echo json_encode($packet['files']);?>;
					$scope.fails = [];
					var total = $scope.files.length;
					var i = 1;
					var proc = function() {
						var path = $scope.files.pop();
						if(!path) {
							if($scope.fails.length == 0) {
								setTimeout(function(){
									location.href = "<?php  echo iurl('system/cloud/process', array( 'step' => 'schemas'));?>";
								}, 2000);
							} else {
								setTimeout(function(){
									location.href = "<?php  echo iurl('system/cloud/process');?>";
								}, 2000);
							}
							return;
						}
						$scope.file = path;
						$scope.pragress = i + '/' + total;
						var params = {path: path};
						$http.post(location.href, params).success(function(dat){
							i++;
							if(dat != 'success') {
								$scope.fails.push(path);
							}
							proc();
						}).error(function(){
							i++;
							$scope.fails.push(path);
							proc();
						});
					}
					proc();
				});
				angular.bootstrap(document, ['app']);
			});
		</script>
	<?php  } ?>
	<?php  if($step == 'schemas') { ?>
		<?php  if(empty($packet['schemas'])) { ?>
			<script>
				location.href = "<?php  echo iurl('system/cloud/process', array( 'step' => 'scripts'));?>";
			</script>
		<?php  } ?>
		<div class="alert alert-warning">
			正在更新数据库, 请不要关闭窗口.
		</div>
		<div class="alert alert-info alert-original form-horizontal ng-cloak" ng-controller="processor">
			<dl class="dl-horizontal">
				<dt>整体进度</dt>
				<dd>{{pragress}}</dd>
			</dl>
			<dl class="dl-horizontal" ng-show="fails.length > 0">
				<dt>处理失败的数据表</dt>
				<dd>
					<p class="text-danger" ng-repeat="schema in fails" style="margin:0;" class="hide">{{schema}}</p>
				</dd>
			</dl>
		</div>
		<script>
			require(['angular', 'util'], function(angular, u){
				angular.module('app', []).controller('processor', function($scope, $http){
					$scope.schemas = <?php  echo json_encode($schemas);?>;
					$scope.fails = [];
					var total = $scope.schemas.length;
					var i = 1;
					var error = function() {
						require(['util'], function(u){
							util.message('未能成功执行处理数据库, 请刷新页面重新进行更新. ');
						});
					}
					var proc = function() {
						var schema = $scope.schemas.pop();
						if(!schema) {
							if($scope.fails.length > 0) {
								error();
								return;
							} else {
								setTimeout(function(){
									location.href = "<?php  echo iurl('system/cloud/process', array( 'step' => 'scripts'));?>";
								}, 2000);
								return;
							}
						}
						$scope.schema = schema;
						$scope.pragress = i + '/' + total;
						var params = {table: schema};
						$http.post(location.href, params).success(function(dat){
							i++;
							if(dat != 'success') {
								$scope.fails.push(schema)
							}
							proc();
						}).error(function(){
							i++;
							$scope.fails.push(schema);
							proc();
						});
					}
					proc();
				});
				angular.bootstrap(document, ['app']);
			});
		</script>
	<?php  } ?>
	<?php  if($step == 'scripts') { ?>
		<?php  if(empty($packet['scripts'])) { ?>
			<script>
				util.message('已经成功执行升级操作!', "<?php  echo iurl('system/cloud/upgrade');?>", 'success');
			</script>
		<?php  } ?>
		<div class="alert alert-warning">
			正在数据迁移及清理操作, 请不要关闭窗口.
		</div>
		<div class="alert alert-info alert-original form-horizontal ng-cloak" ng-controller="processor">
			<dl class="dl-horizontal">
				<dt>整体进度</dt>
				<dd>{{pragress}}</dd>
				<dt>正在处理</dt>
				<dd>{{script}}</dd>
			</dl>
			<dl class="dl-horizontal" ng-show="fails.length > 0">
				<dt>处理失败的操作</dt>
				<dd>
					<p class="text-danger" ng-repeat="script in fails" style="margin:0;">{{script}}</p>
				</dd>
			</dl>
		</div>
		<script>
			require(['angular'], function(angular){
				angular.module('app', []).controller('processor', function($scope, $http){
					$scope.scripts = <?php  echo json_encode($scripts);?>;
					$scope.fails = [];
					var total = $scope.scripts.length;
					var i = 1;
					var error = function() {
						require(['util'], function(u){
							util.message('未能成功执行清理升级操作, 请刷新页面重试. ');
						});
					}
					var proc = function() {
						var script = $scope.scripts.shift();
						if(!script) {
							if($scope.fails.length > 0) {
								error();
							} else {
								util.message('已经成功执行升级操作!', "<?php  echo iurl('system/cloud/upgrade');?>");
								return;
							}
						}
						$scope.script = script.fname;
						$scope.message = script.message;
						$scope.pragress = i + '/' + total;
						var params = {fname: script.fname};
						$http.post(location.href, params).success(function(dat){
							i++;
							if(dat != 'success') {
								console.dir(dat)
								$scope.fails.push(script.fname)
								error();
								return;
							}
							proc();
						}).error(function(){
							i++;
							$scope.fails.push(script.fname);
							error();
						});
					}
					proc();
				});
				angular.bootstrap(document, ['app']);
			});
		</script>
	<?php  } ?>
	</form>
</div>
<?php  } ?>
<?php  include itemplate('public/footer', TEMPLATE_INCLUDEPATH);?>