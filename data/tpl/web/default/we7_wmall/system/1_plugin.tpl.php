<?php defined('IN_IA') or exit('Access Denied');?><?php  include itemplate('public/header', TEMPLATE_INCLUDEPATH);?>
<?php  if($op == 'index') { ?>
<form action="./index.php?" class="form-horizontal form-filter" id="form1">
	<?php  echo tpl_form_filter_hidden('system/plugin/index');?>
	<input type="hidden" name="type" value="<?php  echo $type;?>"/>
	<div class="form-group">
		<label class="col-xs-12 col-sm-3 col-md-2 control-label">类型</label>
		<div class="col-sm-9 col-xs-12">
			<div class="btn-group">
				<a href="<?php  echo ifilter_url('type:');?>" class="btn <?php  if($type == '') { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>">不限</a>
				<?php  if(is_array($types)) { foreach($types as $row) { ?>
					<a href="<?php  echo ifilter_url('type:' . $row['name']);?>" class="btn <?php  if($type == $row['name']) { ?>btn-primary<?php  } else { ?>btn-default<?php  } ?>"><?php  echo $row['title'];?></a>
				<?php  } } ?>
			</div>
		</div>
	</div>
	<div class="form-group form-inline">
		<label class="col-xs-12 col-sm-3 col-md-2 control-label">关键字</label>
		<div class="col-sm-9 col-xs-12">
			<input class="form-control" name="keyword" placeholder="插件名称/插件标识" type="text" value="<?php  echo $_GPC['keyword'];?>">
		</div>
	</div>
	<div class="form-group">
		<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
		<div class="col-sm-9 col-xs-12">
			<button class="btn btn-primary">筛选</button>
		</div>
	</div>
</form>
<form action="" class="form-table form form-validate" method="post">
	<div class="panel panel-table">
		<div class="panel-body table-responsive js-table">
			<?php  if(empty($plugins)) { ?>
				<div class="no-result">
					<p>还没有相关数据</p>
				</div>
			<?php  } else { ?>
			<table class="table table-hover">
				<thead>
				<tr>
					<th>图标</th>
					<th>标识</th>
					<th>开关</th>
					<th>排序</th>
					<th>插件名称</th>
					<th>插件简介</th>
				</tr>
				</thead>
				<?php  if(is_array($plugins)) { foreach($plugins as $plugin) { ?>
				<tr>
					<input type="hidden" name="ids[]" value="<?php  echo $plugin['id'];?>">
					<td>
						<div class="js-selectImg img-container-<?php  echo $plugin['id'];?>" style="width: 50px; height: 50px;" data-input=".img-container-<?php  echo $plugin['id'];?> input" data-element=".img-container-<?php  echo $plugin['id'];?> img" title="点击更换图标">
							<img class="plugin-img-<?php  echo $plugin['id'];?>"  src="<?php  echo tomedia($plugin['thumb']);?>" alt=""  width="50" height="50"/>
							<input type="hidden" name="pluginimgs[]" class="plugin-img-src-<?php  echo $plugin['id'];?>">
						</div>
					</td>
					<td><?php  echo $plugin['name'];?></td>
					<td>
						<div class="checkbox checkbox-inline">
							<input type="checkbox" name="statuss[]" value="<?php  echo $plugin['id'];?>" <?php  if($plugin['status'] == 1) { ?>checked<?php  } ?>>
							<label></label>
						</div>
					</td>
					<td>
						<input type="text" name="displayorders[]" value="<?php  echo $plugin['displayorder'];?>" class="form-control width-100"/>
					</td>
					<td>
						<input type="text" name="titles[]" value="<?php  echo $plugin['title'];?>" class="form-control width-100"/>
					</td>
					<td>
						<input type="text" name="abilitys[]" value="<?php  echo $plugin['ability'];?>" class="form-control"/>
					</td>
				</tr>
				<?php  } } ?>
			</table>
			<div class="btn-region clearfix">
				<div class="pull-left">
					<input name="token" type="hidden" value="<?php  echo $_W['token'];?>" />
					<input type="submit" class="btn btn-primary btn-sm" name="submit" value="提交修改" />
				</div>
			</div>
			<?php  } ?>
		</div>
	</div>
</form>
<?php  } ?>
<?php  include itemplate('public/footer', TEMPLATE_INCLUDEPATH);?>