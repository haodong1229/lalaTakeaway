<?php defined('IN_IA') or exit('Access Denied');?><?php  include itemplate('public/header', TEMPLATE_INCLUDEPATH);?>
<style>
	.width-500{width: 500px;}
</style>
<div class="page clearfix">
	<h2>页面设置</h2>
	<form class="form-horizontal form form-validate" id="form1" action="" method="post" enctype="multipart/form-data">
		<div class="alert alert-warning">
			<span class="text-danger" style="font-size: 20px">
				提醒：
				<br>
				页面设置改动后需重新提交微信审核，并且审核通过后才可生效。
				<br>
				小程序导航栏标题颜色仅支持黑black（黑）、white（白）两种颜色。不填写或者为空,将使用系统默认的
			</span>
		</div>
		<?php  if(is_array($extpages)) { foreach($extpages as $page) { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label"><?php  echo $page['title'];?></label>
				<div class="col-sm-9 col-xs-12">
					<div class="input-group width-500">
						<span class="input-group-addon">导航栏标题文字内容</span>
						<input type="text" name="pages[<?php  echo $page['key'];?>][navigationBarTitleText]" class="form-control width-500" value="<?php  echo $config_extpage[$page['key']]['navigationBarTitleText'];?>" placeholder="导航栏标题文字内容">
					</div>
					<br/>
					<div class="input-group width-500">
						<span class="input-group-addon">导航栏背景颜色</span>
						<input class="form-control" type="text" name="pages[<?php  echo $page['key'];?>][navigationBarBackgroundColor]"  placeholder="导航栏背景颜色" value="<?php  echo $config_extpage[$page['key']]['navigationBarBackgroundColor'];?>">
						<span class="input-group-addon" style="width:35px;border-left:none;background-color:<?php  echo $config_extpage[$page['key']]['navigationBarBackgroundColor'];?>"></span>
						<span class="input-group-btn">
							<button class="btn btn-default colorpicker" type="button">选择颜色 <i class="fa fa-caret-down"></i></button>
							<button class="btn btn-default colorclean" type="button"><span><i class="fa fa-remove"></i></span></button>
						</span>
					</div>
					<br/>
					<div class="input-group width-500">
						<span class="input-group-addon">导航栏标题颜色</span>
						<input class="form-control" type="text" name="pages[<?php  echo $page['key'];?>][navigationBarTextStyle]"  placeholder="请输入：black或者white" value="<?php  echo $config_extpage[$page['key']]['navigationBarTextStyle'];?>">
					</div>
				</div>
			</div>
		<?php  } } ?>
		<div class="form-group">
			<div class="col-sm-9 col-xs-9 col-md-9">
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
				<input type="submit" value="提 交" class="btn btn-primary">
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
require(["jquery", "util"], function($, util){
	$(".colorpicker").each(function(){
		var elm = this;
		util.colorpicker(elm, function(color){
			$(elm).parent().prev().prev().val(color.toHexString());
			$(elm).parent().prev().css("background-color", color.toHexString());
		});
	});
	$(".colorclean").click(function(){
		$(this).parent().prev().prev().val("");
		$(this).parent().prev().css("background-color", "#FFF");
	});
});
</script>
<?php  include itemplate('public/footer', TEMPLATE_INCLUDEPATH);?>