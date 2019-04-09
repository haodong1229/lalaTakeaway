<?php defined('IN_IA') or exit('Access Denied');?><div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
	<div class="col-sm-9 col-xs-12">
		<input type="text" name="displayorder" class="form-control" value="<?php  echo $item['displayorder'];?>" />
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品条码</label>
	<div class="col-sm-9 col-xs-12">
		<input type="text" name="tiaoma" class="form-control" value="<?php  echo $item['tiaoma'];?>" />
	</div>
</div>

<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>商品名称</label>
	<div class="col-sm-9 col-xs-12">
		<input type="text" name="title" class="form-control" value="<?php  echo $item['title'];?>" />
	</div>
</div>

<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>商品简称</label>
	<div class="col-sm-9 col-xs-12">
		<input type="text" name="ftitle" class="form-control" value="<?php  echo $item['ftitle'];?>" />
	</div>
</div>

<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品分类</label>
	<div class="col-sm-4 col-xs-12">
		<select name="cateid" class="form-control">
			<option value="0">--请选择分类--</option>
			<?php  if(is_array($gcatlist)) { foreach($gcatlist as $row) { ?>
				<option value="<?php  echo $row['id'];?>" <?php  if($item['cateid'] == $row['id']) { ?>selected="selected"<?php  } ?>><?php  echo $row['catename'];?></option>
			<?php  } } ?>
		</select>
	</div>
</div>

<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">基础销售量</label>
	<div class="col-sm-9 col-xs-12">
		<input type="text" name="basicsales" class="form-control" value="<?php  echo $item['basicsales'];?>" />
	</div>
</div>

<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">点击数</label>
	<div class="col-sm-9 col-xs-12">
		<input type="text" name="views" class="form-control" value="<?php  echo $item['views'];?>" />
	</div>
</div>

<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品小标题1</label>
	<div class="col-sm-9 col-xs-12">
		<input type="text" name="title2" class="form-control" value="<?php  echo $item['title2'];?>" />
	</div>
</div>

<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品小标题2</label>
	<div class="col-sm-9 col-xs-12">
		<input type="text" name="title3" class="form-control" value="<?php  echo $item['title3'];?>" />
		<div class="help-block" style="font-size:16px;"><span style="color:#E43A48;">|商品小标题1|</span><span style="color:#000;font-weight:bold;">商品名称(或商品简称)</span><span style="color:#E43A48;">|商品小标题2|</span></div>
	</div>
</div>

<div class="form-group">
	<label class="col-xs-12 col-sm-2 control-label">商品简短描述</label>
	<div class="col-sm-9 col-xs-12">
		<textarea class="form-control" name="shotdes" style="height:100px;"><?php  echo $item['shotdes'];?></textarea>
	</div>
</div>


<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品缩略图</label>
	<div class="col-sm-9 col-xs-12">
		<?php  echo tpl_form_field_image('thumb', $item['thumb'], '', array('extras' => array('text' => 'readonly')))?>
		<span class="help-block" style="color:red;">推荐尺寸640*640</span>
	</div>
</div>

<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品长图</label>
	<div class="col-sm-9 col-xs-12">
		<?php  echo tpl_form_field_image('changthumb', $item['changthumb'], '', array('extras' => array('text' => 'readonly')))?>
		<span class="help-block" style="color:red;">推荐尺寸750X400，用于社区团购展示</span>
	</div>
</div>

<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品图集</label>
	<div class="col-sm-9 col-xs-12">
		<?php  echo tpl_form_field_multi_image('thumbs',$piclist)?>
		<span class="help-block" style="color:red;">推荐尺寸640*640</span>
	</div>
</div>

<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品价格</label>
	<div class="col-sm-9 col-xs-12">
		<div class="input-group">
			<span class="input-group-addon">售价</span>
			<input type="text" name="normalprice" class="form-control" value="<?php  echo $item['normalprice'];?>" />
			<span class="input-group-addon">元</span>
		</div>

		<div class="input-group">
			<span class="input-group-addon">代理价</span>
			<input type="text" name="dailiprice" class="form-control" value="<?php  echo $item['dailiprice'];?>" />
			<span class="input-group-addon">元</span>
		</div>

		<div class="input-group">
			<span class="input-group-addon">成本价</span>
			<input type="text" name="chengbenprice" class="form-control" value="<?php  echo $item['chengbenprice'];?>" />
			<span class="input-group-addon">元</span>
		</div>
		
		<div class="input-group">
			<span class="input-group-addon">市场价</span>
			<input type="text" name="scprice" class="form-control" value="<?php  echo $item['scprice'];?>" />
			<span class="input-group-addon">元</span>
		</div>
		
		<div class="input-group">
			<span class="input-group-addon">限购</span>
			<input type="text" name="xiangounum" class="form-control" value="<?php  echo $item['xiangounum'];?>" />
			<span class="input-group-addon">件</span>
		</div>
		<span class="help-block" style="color:red;">限购填0表示不限购，限购只对每期活动起作用，不累加总数。</span>
	</div>
</div>

<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">库存</label>
	<div class="col-sm-6 col-xs-12">
		<div class="input-group">
			<input type="text" name="total" id="total" class="form-control" value="<?php  echo $item['total'];?>" />
			<span class="input-group-addon">件</span>
		</div>
		<span class="help-block" style="color:red;">当前商品的库存数量</span>
	</div>
</div>