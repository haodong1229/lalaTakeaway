<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li <?php  if($operation == 'display' && $isxq == 0) { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('huodong',array('op' =>'display'))?>">活动管理</a></li>
	<li <?php  if($operation == 'display' && $isxq == 1) { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('huodong',array('op' =>'display','isxq'=>1))?>">社区活动管理</a></li>
	<li<?php  if(empty($huodong['id']) && $operation == 'post' && $isxq == 0) { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('huodong',array('op' =>'post'))?>">添加活动</a></li>
	<li<?php  if(empty($huodong['id']) && $operation == 'post' && $isxq == 1) { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('huodong',array('op' =>'post','isxq'=>1))?>">添加社区活动</a></li>
	<?php  if(!empty($huodong['id']) &&  $operation == 'post') { ?><li  class="active"><a href="<?php  echo $this->createWebUrl('huodong',array('op' =>'post','id'=>$huodong['id']))?>">编辑活动</a></li><?php  } ?>
	<?php  if($operation == 'canyu') { ?><li class="active"><a href="###">参与列表</a></li><?php  } ?>
	<?php  if($operation == 'canyuedit') { ?><li class="active"><a href="###">团长活动编辑</a></li><?php  } ?>
	<?php  if($operation == 'teamjiang') { ?><li class="active"><a href="###">配置团队奖</a></li><?php  } ?>
	<?php  if($operation == 'xiajia') { ?><li class="active"><a href="###">活动商品管理</a></li><?php  } ?>
</ul>

<?php  if($operation == 'display') { ?>
<div class="main panel panel-default">
	<div class="panel-body table-responsive">
		<table class="table table-hover">
			<thead class="navbar-inner">
				<tr>
					<th>标题</th>
					<th>时间</th>
					<th style="text-align:right;">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($list)) { foreach($list as $huodong) { ?>
				<tr>
					<td>
						<?php  if($huodong['isxq'] == 1 && $huodong['isdqhd'] == 1) { ?><span class="label label-warning">正在抢购</span><?php  } ?>
						<?php  if($huodong['isxq'] == 1 && $huodong['isdqhd'] == 2) { ?><span class="label label-info">下期预告</span><?php  } ?>
						<span class="label label-success"><?php  echo $huodong['title'];?></span>
					</td>
					<td>
						<div><?php  echo date("Y-m-d H:i:s",$huodong['starttime'])?></div>
						<div style="margin-top:5px;"><?php  echo date("Y-m-d H:i:s",$huodong['endtime'])?></div>
					</td>
					<td style="text-align:right;">
						<div>
						<?php  if($huodong['endtime'] > TIMESTAMP && $huodong['tqjs'] == 0) { ?>
						<a href="<?php  echo $this->createWebUrl('huodong', array('op' => 'tingzhi', 'id' => $huodong['id']))?>" onclick="return confirm('提前结束活动将无法继续开启，确认吗？');return false;" class="btn btn-danger btn-sm">提前结束</a>
						<?php  } ?>
						<?php  if($_W["account"]["type_name"] == "公众号") { ?>
						<a href="<?php  echo $this->createWebUrl('huodong', array('op' => 'tongzhi', 'id' => $huodong['id']))?>" onclick="return confirm('群发模板消息有风险，确认操作吗？');return false;" class="btn btn-warning btn-sm">通知团长</a>
						<?php  } ?>
						<?php  if($huodong['endtime'] > TIMESTAMP) { ?>
						<a href="<?php  echo $this->createWebUrl('huodong', array('op' => 'teamjiang', 'id' => $huodong['id']))?>" class="btn btn-info btn-sm">配置团队奖</a>
						<?php  } ?>
						<a href="<?php  echo $this->createWebUrl('order', array('hdid' => $huodong['id']))?>" class="btn btn-success btn-sm">查看订单</a>
						</div>
						<div style="margin-top:5px;">
							<a href="<?php  echo $this->createWebUrl('huodong', array('op' => 'post', 'id' => $huodong['id']))?>" class="btn btn-default btn-sm" title="修改"><i class="fa fa-edit"></i></a>
							<a href="<?php  echo $this->createWebUrl('huodong', array('op' => 'canyu', 'id' => $huodong['id']))?>" class="btn btn-default btn-sm"><?php  echo $huodong['canyunum'];?>个参与</a>
							<a href="<?php  echo $this->createWebUrl('huodong', array('op' => 'peihuo', 'id' => $huodong['id']))?>" onclick="return confirm('确认要导出配货单吗？');return false;" class="btn btn-default btn-sm">导出配货单</a>
							<a href="<?php  echo $this->createWebUrl('huodong', array('op' => 'xiajia', 'id' => $huodong['id']))?>" class="btn btn-default btn-sm">活动商品管理</a>
							<a href="<?php  echo $this->createWebUrl('huodong', array('op' => 'delete', 'id' => $huodong['id']))?>" onclick="return confirm('此操作不可恢复，确认吗？');return false;" class="btn btn-default btn-sm" title="删除"><i class="fa fa-times"></i></a>
						</div>
					</td>
				</tr>
				<?php  } } ?>
			</tbody>
		</table>
	</div>
</div>
<?php  echo $pager;?>

<?php  } else if($operation == 'post') { ?>
<div class="main">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" onsubmit='return formcheck()'>
		<input type="hidden" name="id" value="<?php  echo $huodong['id'];?>" />
		<input type="hidden" name="isxq" value="<?php  echo $isxq;?>" />
		<div class="panel panel-default">
			<div class="panel-heading">活动设置</div>
			<div class="panel-body">
				<?php  if($isxq == 1) { ?>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">社区属性</label>
						<div class="col-sm-9 col-xs-12">
							<label for="isdqhd1" class="radio-inline"><input name="isdqhd" value="0" id="isdqhd1" <?php  if($huodong['isdqhd'] == 0) { ?>checked="true"<?php  } ?> type="radio"> 无</label>
							&nbsp;&nbsp;&nbsp;
							<label for="isdqhd2" class="radio-inline"><input name="isdqhd" value="1" id="isdqhd2" <?php  if($huodong['isdqhd'] == 1) { ?>checked="true"<?php  } ?> type="radio"> 正在抢购</label>
							&nbsp;&nbsp;&nbsp;
							<label for="isdqhd3" class="radio-inline"><input name="isdqhd" value="2" id="isdqhd3" <?php  if($huodong['isdqhd'] == 2) { ?>checked="true"<?php  } ?> type="radio"> 下期预告</label>
						</div>
					</div> 
				<?php  } ?>
				
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">标题</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="title" class="form-control" value="<?php  echo $huodong['title'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">分享标题</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="sharetitle" class="form-control" value="<?php  echo $huodong['sharetitle'];?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">分享图标</label>
					<div class="col-sm-9 col-xs-12">
						<?php  echo tpl_form_field_image('sharethumb', $huodong['sharethumb'], '', array('extras' => array('text' => 'readonly')))?>
						<span class="help-block" style="color:red;">推荐尺寸200*200</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">分享描述</label>
					<div class="col-sm-7 col-xs-12">
						<textarea class="form-control" name="sharedes"><?php  echo $huodong['sharedes'];?></textarea>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">开始时间</label>
					<div class="col-sm-9 col-xs-12">
						<?php  echo tpl_form_field_date('starttime',date('Y-m-d H:i:s',$huodong['starttime']),true);?>
					</div>
				</div>		
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">结束时间</label>
					<div class="col-sm-9 col-xs-12">
						<?php  echo tpl_form_field_date('endtime',date('Y-m-d H:i:s',$huodong['endtime']),true);?>
					</div>
				</div>
				
				<!--<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">活动商品</label>
					<div class="col-sm-9 col-xs-12">
						<?php  if(is_array($goodslist)) { foreach($goodslist as $goods) { ?>
						<label for="goods<?php  echo $goods['id'];?>" class="checkbox-inline"><input name="goodsid[]" value="<?php  echo $goods['id'];?>" id="goods<?php  echo $goods['id'];?>" <?php  if($goods['has']) { ?>checked="checked"<?php  } ?> type="checkbox"> <?php  echo $goods['title'];?></label>
						&nbsp;&nbsp;&nbsp;
						<?php  } } ?>
					</div>
				</div>-->
				
				<?php  if($isxq == 0) { ?>
				<input type="hidden" name="isdqhd" value="0" />
				<input type="hidden" name="isxqhd" value="0" />
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">团长自主定价</label>
					<div class="col-sm-9 col-xs-12">
						<label for="candj1" class="radio-inline"><input name="candj" value="1" id="candj1" <?php  if($huodong['candj'] == 1) { ?>checked="true"<?php  } ?> type="radio"> 支持</label>
						&nbsp;&nbsp;&nbsp;
						<label for="candj2" class="radio-inline"><input name="candj" value="0" id="candj2" <?php  if($huodong['candj'] == 0) { ?>checked="true"<?php  } ?> type="radio"> 不支持</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">货到付款</label>
					<div class="col-sm-9 col-xs-12">
						<label for="candmfk1" class="radio-inline"><input name="candmfk" value="1" id="candmfk1" <?php  if($huodong['candmfk'] == 1) { ?>checked="true"<?php  } ?> type="radio"> 支持</label>
						&nbsp;&nbsp;&nbsp;
						<label for="candmfk2" class="radio-inline"><input name="candmfk" value="0" id="candmfk2" <?php  if($huodong['candmfk'] == 0) { ?>checked="true"<?php  } ?> type="radio"> 不支持</label>
					</div>
				</div>
				<?php  } else { ?>
				<input type="hidden" name="candj" value="0" />
				<input type="hidden" name="candmfk" value="0" />
				<?php  } ?>
				
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">配送方式</label>
					<div class="col-sm-9 col-xs-12">
						<label for="pstype1" class="radio-inline"><input name="pstype" value="0" id="pstype1" <?php  if($huodong['pstype'] == 0) { ?>checked="true"<?php  } ?> type="radio"> 平台配送</label>
						&nbsp;&nbsp;&nbsp;
						<label for="pstype2" class="radio-inline"><input name="pstype" value="1" id="pstype2" <?php  if($huodong['pstype'] == 1) { ?>checked="true"<?php  } ?> type="radio"> 团长配送</label>
						<span class="help-block" style="color:red;">平台配送是指平台直接发货给用户，团长配送是指平台发货给团长，团长自己决定用户上门自提还是团长自己送货</span>
					</div>
				</div>
				
				<div id="sss" <?php  if($huodong['pstype'] == 1) { ?>style="display:none;"<?php  } ?>>					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">免运费</label>
						<div class="col-sm-3 col-xs-12">
							<div class="input-group">
								<span class="input-group-addon">满</span>
								<input class="form-control" name="manjian" value="<?php  echo $huodong['manjian'];?>" type="text">
								<span class="input-group-addon">元免运费</span>
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">运费模板</label>
						<div class="col-sm-9 col-xs-12">
							<?php  if(is_array($yunfeilist)) { foreach($yunfeilist as $yunfei) { ?>
							<label for="yunfei<?php  echo $yunfei['id'];?>" class="radio-inline"><input name="yfid" value="<?php  echo $yunfei['id'];?>" id="yunfei<?php  echo $yunfei['id'];?>" <?php  if($huodong['yfid'] == $yunfei['id']) { ?>checked="checked"<?php  } ?> type="radio"> <?php  echo $yunfei['title'];?></label>
							&nbsp;&nbsp;&nbsp;
							<?php  } } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group col-sm-12">
			<input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" />
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
	</form>
</div>
<script>
$(function(){
	$("input[name='pstype']").click(function(){
		if($(this).val() == 0){
			$("#sss").show();
		}else{
			$("#sss").hide();
		}
	});
})
</script>
<?php  } else if($operation == 'canyu') { ?>
<div class="panel panel-default">
	<div class="panel-body table-responsive">
		<table class="table table-hover">
			<thead class="navbar-inner">
				<tr>
					<th style="width:5%;">ID</th>
					<th style="width:20%;">商家名称</th>
					<th style="width:15%;">参与时间</th>
					<th style="text-align:right;">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($merchanthdlist)) { foreach($merchanthdlist as $item) { ?>
				<tr>
					<td><?php  echo $item['id'];?></td>
					<td><?php  echo $item['merchant']['name'];?></td>
					<td>
						<span class="label label-success"><?php  echo date("Y-m-d H:i:s",$item['time'])?></span>
					</td>
					<td style="text-align:right;">
						<a href="<?php  echo $this->createWebUrl('order', array('hdid' => $item['hdid'],'merchant_id'=>$item['merchant_id']))?>" class="btn btn-info btn-sm"><?php  echo $item['ordernum'];?>个订单</a>
						<a href="<?php  echo $this->createWebUrl('huodong', array('id' => $item['id'], 'op' => 'canyuedit'))?>" class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="top" title="编辑"><i class="fa fa-pencil"></i></a>
						<a href="<?php  echo $this->createWebUrl('huodong', array('op' => 'deletecanyu', 'id' => $item['id'], 'hdid' => $item['hdid']))?>" onclick="return confirm('此操作不可恢复，确认吗？');return false;" class="btn btn-default btn-sm" title="删除"><i class="fa fa-times"></i></a>
					</td>
				</tr>
				<?php  } } ?>
			</tbody>
		</table>
	</div>
</div>
<?php  echo $pager;?>
<?php  } ?>

<?php  if($operation == 'xiajia') { ?>
<div class="panel panel-default">
	<div class="panel-heading">添加商品</div>
	<div class="panel-body table-responsive" style="max-height:400px;overflow-y:auto;">
		<form action="" method="post">
		<table class="table table-hover">
			<thead class="navbar-inner">
				<tr>
					<th style="width:50%;">商品名称</th>
					<th style="width:10%;">库存</th>
					<th style="width:10%;">售价</th>
					<th style="width:10%;">代理价</th>
					<th style="text-align:right;">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($addgoodslist)) { foreach($addgoodslist as $item) { ?>
					<tr>
						<td><?php  echo $item['title'];?></td>
						<td><?php  echo $item['total'];?></td>
						<td>
							<span class="label label-success"><?php  echo $item['normalprice'];?></span>
						</td>
						<td>
							<span class="label label-info"><?php  echo $item['dailiprice'];?></span>
						</td>
						<td style="text-align:right;">
							<button class="btn btn-success btn-sm btn-add" data-id="<?php  echo $item['id'];?>">添加该商品</button>
						</td>
					</tr>
				<?php  } } ?>	
			</tbody>
		</table>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-body table-responsive">
		<form action="" method="post">
		<table class="table table-hover">
			<thead class="navbar-inner">
				<tr>
					<th style="width:8%;">排序</th>
					<th style="width:50%;">商品名称</th>
					<th style="width:10%;">库存</th>
					<th style="width:10%;">售价</th>
					<th style="width:10%;">代理价</th>
					<th style="text-align:right;">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($goodslist)) { foreach($goodslist as $item) { ?>
					<tr>
						<td><input class="form-control" name="displayorder[<?php  echo $item['id'];?>]" value="<?php  echo $item['displayorder'];?>" type="text"></td>
						<td><?php  echo $item['goodsres']['title'];?></td>
						<td><?php  echo $item['goodsres']['total'];?></td>
						<td>
							<span class="label label-success"><?php  echo $item['goodsres']['normalprice'];?></span>
						</td>
						<td>
							<span class="label label-info"><?php  echo $item['goodsres']['dailiprice'];?></span>
						</td>
						<td style="text-align:right;">
							<a href="<?php  echo $this->createWebUrl('huodong', array('op' => 'doxiajia', 'hdid' => $item['hdid'], 'goodsid' => $item['goodsid']))?>" onclick="return confirm('此操作不可恢复，确认吗？');return false;" class="btn btn-danger btn-sm">下架</a>
						</td>
					</tr>
				<?php  } } ?>
				<tr>
					<td colspan="6">
						<input name="submit" class="btn btn-primary" value="提交排序" type="submit">
						<input name="token" value="<?php  echo $_W['token'];?>" type="hidden">
					</td>
				</tr>
			</tbody>
		</table>
		</form>
	</div>
	</div>
</div>

<script>
$(function(){
	$(".btn-add").click(function(){
		var gid = $(this).attr('data-id');
		var hdid = <?php  echo $hdid;?>;
		$.ajax({   
			url:"<?php  echo $this->createWebUrl('huodong',array('op'=>'addgoods'))?>",   
			type:'post', 
			data:{
				gid:gid,
				hdid:hdid,
			},
			dataType:'json',
			success:function(data){   
				history.go(0);
			}
		});
	});
})
</script>
<?php  } ?>

<?php  if($operation == 'canyuedit') { ?>
<div class="main">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" onsubmit='return formcheck()'>
		<input type="hidden" name="id" value="<?php  echo $merchanthd['id'];?>" />
		<div class="panel panel-default">
			<div class="panel-heading">团长活动编辑</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">分享标题</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="sharetitle" class="form-control" value="<?php  echo $merchanthd['sharetitle'];?>" />
					</div>
				</div>

				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">分享二维码</label>
					<div class="col-sm-9 col-xs-12">
						<?php  echo tpl_form_field_image('sharethumb', $merchanthd['sharethumb'], '', array('extras' => array('text' => 'readonly')))?>
						<span class="help-block" style="color:red;">推荐尺寸200*200</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">分享描述</label>
					<div class="col-sm-7 col-xs-12">
						<textarea class="form-control" name="sharedes"><?php  echo $merchanthd['sharedes'];?></textarea>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">运费</label>
					<div class="col-sm-9 col-xs-12">
						<div class="input-group">
							<input class="form-control" name="yunfei" value="<?php  echo $merchanthd['yunfei'];?>" type="text">
							<span class="input-group-addon">元</span>
						</div>
					</div>
				</div>
			
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">免运费</label>
					<div class="col-sm-9 col-xs-12">
						<div class="input-group">
							<span class="input-group-addon">满</span>
							<input class="form-control" name="manjian" value="<?php  echo $merchanthd['manjian'];?>" type="text">
							<span class="input-group-addon">元免运费</span>
						</div>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">支持自提</label>
					<div class="col-sm-9 col-xs-12">
						<label for="canziti1" class="radio-inline"><input name="canziti" value="1" id="canziti1" <?php  if($merchanthd['canziti'] == 1) { ?>checked="true"<?php  } ?> type="radio"> 是</label>
						&nbsp;&nbsp;&nbsp;
						<label for="canziti2" class="radio-inline"><input name="canziti" value="0" id="canziti2" <?php  if($merchanthd['canziti'] == 0) { ?>checked="true"<?php  } ?> type="radio"> 否</label>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">支持送货</label>
					<div class="col-sm-9 col-xs-12">
						<label for="cansonghuo1" class="radio-inline"><input name="cansonghuo" value="1" id="cansonghuo1" <?php  if($merchanthd['cansonghuo'] == 1) { ?>checked="true"<?php  } ?> type="radio"> 是</label>
						&nbsp;&nbsp;&nbsp;
						<label for="cansonghuo2" class="radio-inline"><input name="cansonghuo" value="0" id="cansonghuo2" <?php  if($merchanthd['cansonghuo'] == 0) { ?>checked="true"<?php  } ?> type="radio"> 否</label>
					</div>
				</div>
								
				<!--<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">选择商品</label>
					<div class="col-sm-9 col-xs-12">
						<?php  if(is_array($goodsarr)) { foreach($goodsarr as $goods) { ?>
						<label class="checkbox-inline"><input name="goodsid[]" value="<?php  echo $goods['goods']['id'];?>-<?php  echo $goods['optionid'];?>" <?php  if($goods['has']) { ?>checked="checked"<?php  } ?> type="checkbox"> <?php  echo $goods['goods']['title'];?><?php  if($goods['optionid'] > 0) { ?><span style="color:red;">[<?php  echo $goods['optionname'];?>]</span><?php  } ?></label>
						&nbsp;&nbsp;&nbsp;
						<?php  } } ?>
					</div>
				</div>-->
			</div>
		</div>
		<div class="form-group col-sm-12">
			<input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" />
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
	</form>
</div>
<?php  } else if($operation == 'teamjiang') { ?>
<div class="main">
	<div class="alert alert-danger" role="alert">
		<div>1、团队奖只能在活动结束前配置</div>
		<div>2、团队奖是阶梯制模式，例如100-1000元奖励团队销售额的5%、例如1001-2000元奖励团队销售额的8%。以此类推</div>
		<div>3、团队奖只能在活动结束后在<a href="<?php  echo $this->createWebUrl('teamjiang',array('hdid'=>$id))?>" style="color:green;">团队奖管理</a>里发放</div>
	</div>
	<form action="" method="post" class="form-horizontal form">
		<input type="hidden" name="id" value="<?php  echo $id;?>" />
		<div class="panel panel-default">
			<div class="panel-heading">配置团队奖</div>
			<div class="panel-body">
				<table class="table table-hover">
					<thead>
					<tr>
						<th>起始价格</th>
						<th>截止价格</th>
						<th>奖励比例</th>
						<th width="10%">操作</th>
					</tr>
					</thead>
					<tbody id="option-items">
						<?php  if(is_array($teamjiang)) { foreach($teamjiang as $p) { ?>
						<tr>
							<td>
								<div class="input-group">
									<input name="startmoney[]" type="text" class="form-control" value="<?php  echo $p['startmoney'];?>"/>
									<span class="input-group-addon">元</span>
								</div>
							</td>
							<td>
								<div class="input-group">
									<input name="endmoney[]" type="text" class="form-control" value="<?php  echo $p['endmoney'];?>"/>
									<span class="input-group-addon">元</span>
								</div>
							</td>
							<td>								
								<div class="input-group">
									<span class="input-group-addon">奖励比例</span>
									<input name="jiangli[]" type="text" class="form-control" value="<?php  echo $p['jiangli'];?>"/>
									<span class="input-group-addon">%</span>
								</div>
							</td>
							<td>
								<a href="javascript:;" onclick="deleteoption(this)" style="margin-top:10px;" title="删除"><i class="fa fa-remove"></i></a>
							</td>
						</tr>
						<?php  } } ?>
					</tbody>
					<tbody>
						<tr>
							<td colspan="4">
								<a href="javascript:;" id='add-option' onclick="addoption()" style="margin-top:10px;"  title="添加"><i class='fa fa-plus'></i>添加</a>
							</td>
						</tr>
					</tbody>
				</table>

			</div>
		</div>
		<div class="form-group col-sm-12">
			<input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" />
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
	</form>
</div>
<script language="javascript">
	function addoption() {
		var html = '<tr>'
						+'<td>'
							+'<div class="input-group">'
								+'<input name="startmoney[]" type="text" class="form-control" />'
								+'<span class="input-group-addon">元</span>'
							+'</div>'
						+'</td>'
						+'<td>'
							+'<div class="input-group">'
								+'<input name="endmoney[]" type="text" class="form-control" />'
								+'<span class="input-group-addon">元</span>'
							+'</div>'
						+'</td>'
						+'<td>'
							+'<div class="input-group">'
								+'<span class="input-group-addon">奖励比例</span>'
								+'<input name="jiangli[]" type="text" class="form-control" />'
								+'<span class="input-group-addon">%</span>'
							+'</div>'
						+'</td>'
						+'<td>'
							+'<a href="javascript:;" onclick="deleteoption(this)" style="margin-top:10px;" title="删除"><i class="fa fa-remove"></i></a>'
						+'</td>'
					+'</tr>';
		$('#option-items').append(html);
	}
	function deleteoption(o) {
		$(o).parent().parent().remove();
	}
</script>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>