<?php defined('IN_IA') or exit('Access Denied');?><?php  include itemplate('public/header', TEMPLATE_INCLUDEPATH);?>
<div class="page clearfix">
	<h2>基础设置</h2>
	<form class="form-horizontal form form-validate" id="form1" action="" method="post" enctype="multipart/form-data">
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">平台管理员微信信息</label>
			<div class="col-sm-9 col-xs-12">
				<?php  echo tpl_form_field_fans('manager', $config['manager'], 'notify', false);?>
				<div class="help-block">当有商户商户申请, 商户提现等申请时,系统会微信通知平台管理员</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">平台名称</label>
			<div class="col-sm-9 col-xs-12">
				<input type="text" name="title" value="<?php  echo $config['title'];?>" class="form-control" required>
				<div class="help-block"></div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">平台LOGO</label>
			<div class="col-sm-9 col-xs-12">
				<?php  echo tpl_form_field_image('logo', $config['logo']);?>
				<div class="help-block">正方形图片</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">平台客服电话</label>
			<div class="col-sm-9 col-xs-12">
				<input type="text" name="mobile" value="<?php  echo $config['mobile'];?>" class="form-control" required>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">平台模式</label>
			<div class="col-sm-9 col-xs-12">
				<div class="radio radio-inline">
					<input type="radio" value="2" name="version" id="version-1" <?php  if($config['version'] == 2) { ?>checked<?php  } ?> required>
					<label for="version-1">单店模式</label>
				</div>
				<div class="radio radio-inline">
					<input type="radio" value="1" name="version" id="version-2" <?php  if($config['version'] == 1) { ?>checked<?php  } ?> required>
					<label for="version-2">平台模式</label>
				</div>
				<div class="help-block text-danger">如果是单店模式，用户进入时，不会显示门店列表，直接跳转到该门店的菜单页</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否跳转到离顾客最近的门店</label>
			<div class="col-sm-9 col-xs-12">
				<div class="radio radio-inline">
					<input type="radio" value="1" name="is_to_nearest_store" id="is-to-nearest-store-1" <?php  if($config['is_to_nearest_store'] == 1) { ?>checked<?php  } ?>>
					<label for="is-to-nearest-store-1">是</label>
				</div>
				<div class="radio radio-inline">
					<input type="radio" value="0" name="is_to_nearest_store" id="is-to-nearest-store-0" <?php  if($config['is_to_nearest_store'] == 0) { ?>checked<?php  } ?>>
					<label for="is-to-nearest-store-0">否</label>
				</div>
				<div class="help-block text-danger">如果开启，用户进入时，会自动跳转到离顾客最近的门店</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否启用商户二级分类</label>
			<div class="col-sm-9 col-xs-12">
				<div class="radio radio-inline">
					<input type="radio" value="1" name="store_use_child_category" id="store_use_child_category-1" <?php  if($config['store_use_child_category'] == 1) { ?>checked<?php  } ?>>
					<label for="store_use_child_category-1">是</label>
				</div>
				<div class="radio radio-inline">
					<input type="radio" value="0" name="store_use_child_category" id="store_use_child_category-0" <?php  if($config['store_use_child_category'] == 0 || !$config['store_use_child_category']) { ?>checked<?php  } ?>>
					<label for="store_use_child_category-0">否</label>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">默认门店</label>
			<div class="col-sm-9 col-xs-12">
				<select class="form-control" name="default_sid">
					<option value="">==请选择默认门店==</option>
					<?php  if(is_array($stores)) { foreach($stores as $store) { ?>
					<option value="<?php  echo $store['id'];?>" <?php  if($config['default_sid'] == $store['id']) { ?>selected<?php  } ?>><?php  echo $store['title'];?></option>
					<?php  } } ?>
				</select>
				<div class="help-block">设置为单店模式时,默认跳转的门店</div>
			</div>
		</div>
		<div class="form-group hide">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">模板选择</label>
			<div class="col-sm-9 col-xs-12">
				<select class="form-control" name="template">
					<option value="">选择模板</option>
					<?php  if(is_array($templates)) { foreach($templates as $template) { ?>
						<option value="<?php  echo $template;?>" <?php  if($config['template_mobile'] == $template) { ?>selected<?php  } ?>><?php  echo $template;?></option>
					<?php  } } ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">首页商家排序方式</label>
			<div class="col-sm-9 col-xs-12">
				<div class="radio radio-inline">
					<input type="radio" value="displayorder" name="store_orderby_type" id="store-orderby-type-displayorder" <?php  if($config['store_orderby_type'] == 'displayorder') { ?>checked<?php  } ?>>
					<label for="store-orderby-type-displayorder">按排序大小排序</label>
				</div>
				<div class="radio radio-inline">
					<input type="radio" value="click" name="store_orderby_type" id="store-orderby-type-click" <?php  if($config['store_orderby_type'] == 'click') { ?>checked<?php  } ?>>
					<label for="store-orderby-type-click">按热度排序</label>
				</div>
				<div class="radio radio-inline">
					<input type="radio" value="distance" name="store_orderby_type" id="store-orderby-type-distance" <?php  if($config['store_orderby_type'] == 'distance' || !$config['store_orderby_type']) { ?>checked<?php  } ?>>
					<label for="store-orderby-type-distance">离我最近</label>
				</div>
				<div class="radio radio-inline">
					<input type="radio" value="sailed" name="store_orderby_type" id="store-orderby-type-sailed" <?php  if($config['store_orderby_type'] == 'sailed') { ?>checked<?php  } ?>>
					<label for="store-orderby-type-sailed">销量最高</label>
				</div>
				<div class="radio radio-inline">
					<input type="radio" value="score" name="store_orderby_type" id="store-orderby-type-score" <?php  if($config['store_orderby_type'] == 'score') { ?>checked<?php  } ?>>
					<label for="store-orderby-type-score">评分最高</label>
				</div>
				<div class="radio radio-inline">
					<input type="radio" value="displayorderAndDistance" name="store_orderby_type" id="store-orderby-type-displayorderAndDistance" <?php  if($config['store_orderby_type'] == 'displayorderAndDistance') { ?>checked<?php  } ?>>
					<label for="store-orderby-type-displayorderAndDistance">先按照排序大小,再按照距离排序</label>
				</div>
				<div class="help-block">
					<span class="text-danger">热度: 体现在粉丝在点击“搜索”菜单时，系统会按照热度有大到小列出5个商家。</span>
					<br>
					<span class="text-danger">推荐: 体现在粉丝搜索门店或菜品时，在搜索结果下面会列出平台推荐的商家。</span>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">超出商家配送范围是否显示</label>
			<div class="col-sm-9 col-xs-12">
				<div class="radio radio-inline">
					<input type="radio" value="1" name="store_overradius_display" id="store-overradius-display-1" <?php  if($config['store_overradius_display'] == '1' || !$config['store_overradius_display']) { ?>checked<?php  } ?>>
					<label for="store-overradius-display-1">显示</label>
				</div>
				<div class="radio radio-inline">
					<input type="radio" value="2" name="store_overradius_display" id="store-overradius-display-2" <?php  if($config['store_overradius_display'] == '2') { ?>checked<?php  } ?>>
					<label for="store-overradius-display-2">不显示</label>
				</div>
				<div class="help-block hide">
					<span>注意:设置为不显示时，还要根据商家的配送相关设置进行判断。</span>
					<br>
					<span class="text-danger">商家还会在首页显示情况说明：<br>一、固定或者按距离收取配送费，1允许在配送半径之外下单，2不允许在配送半径之外下单，但是顾客定位在商家配送半径之内（直线距离）；<br> 二、按区域收取配送费并且顾客定位在划分的区域。</span>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">配送方名称</label>
			<div class="col-sm-9 col-xs-12">
				<input type="text" name="delivery_title" value="<?php  echo $config['delivery_title'];?>" class="form-control">
				<div class="help-block">设置后此内容将替换前台页面中的配送方名称"平台配送"</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">懒加载商家图片</label>
			<div class="col-sm-9 col-xs-12">
				<?php  echo tpl_form_field_image('lazyload_store', $config['lazyload_store']);?>
				<div class="help-block">推荐图片大小：70*70</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">懒加载商品图片</label>
			<div class="col-sm-9 col-xs-12">
				<?php  echo tpl_form_field_image('lazyload_goods', $config['lazyload_goods']);?>
				<div class="help-block">推荐图片大小：70*70</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">app端版权设置</label>
			<div class="col-sm-9 col-xs-12">
				<?php  echo tpl_ueditor('copyright', $config['copyright']);?>
			</div>
		</div>
		<div class="form-group hide">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">心知天气统计代码</label>
			<div class="col-sm-9 col-xs-12">
				<textarea style="height:150px;" class="form-control" cols="70" name="seniverse" autocomplete="off"><?php  echo $config['seniverse'];?></textarea>
				<div class="help-block">如不填写,系统默认自动获取顾客的位置显示当地天气概括（这种情况下极少部分客户可能出现地区不匹配的情况）。如果你仅需要显示您外卖运营区域的天气概括，请申请<a href="https://www.seniverse.com" target="_blank">心知天气</a>重新获得天气统计代码替换系统默认代码</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label">美洽客服插件代码</label>
			<div class="col-sm-9 col-xs-12">
				<textarea style="height:150px;" class="form-control" cols="70" name="meiqia" autocomplete="off"><?php  echo $config['meiqia'];?></textarea>
				<div class="help-block">系统对接了"美洽客服"，请申请<a href="https://app.meiqia.com/setting/web-widget" target="_blank">美洽客服插件代码</a></div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-9 col-xs-9 col-md-9">
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
				<input type="submit" value="提交" class="btn btn-primary">
			</div>
		</div>
	</form>
</div>
<?php  include itemplate('public/footer', TEMPLATE_INCLUDEPATH);?>