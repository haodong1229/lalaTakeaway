<?php defined('IN_IA') or exit('Access Denied');?><?php  include itemplate('public/header', TEMPLATE_INCLUDEPATH);?>
<div class="page">
	<form class="form-horizontal form form-validate" id="form1" action="" method="post" enctype="multipart/form-data">
		<div class="form-group">
			<div class="col-sm-9 col-xs-9 col-md-9">
				<a href="<?php  echo iurl('system/cache');?>" class="btn-primary btn js-post" data-confirm="确定清空缓存吗？">清空缓存</a>
			</div>
		</div>
	</form>
</div>
<?php  include itemplate('public/footer', TEMPLATE_INCLUDEPATH);?>