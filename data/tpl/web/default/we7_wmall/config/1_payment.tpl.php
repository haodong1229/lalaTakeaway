<?php defined('IN_IA') or exit('Access Denied');?><?php  include itemplate('public/header', TEMPLATE_INCLUDEPATH);?>
<div class="page clearfix">
	<form class="form-horizontal form form-validate" id="form1" action="" method="post" enctype="multipart/form-data">
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#base" aria-controls="base" role="tab" data-toggle="pill">支付参数</a></li>
			<li role="presentation"><a href="#wechat" aria-controls="wechat" role="tab" data-toggle="pill">微信端</a></li>
			<li role="presentation"><a href="#wap" aria-controls="wap" role="tab" data-toggle="pill">WAP端</a></li>
			<li role="presentation"><a href="#app" aria-controls="app" role="tab" data-toggle="pill">APP端</a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" role="tabpanel" id="base">
				<div class="alert alert-warning">
					注意：微信支付需要在公众平台->微信支付->公众号支付 追加两条支付授权目录:<br>
					目录一：<a href="javascript:;" class="js-clip" data-text="<?php  echo $_W['siteroot'];?>app/"><?php  echo $_W['siteroot'];?>app/</a><br>
					目录二：<a href="javascript:;" class="js-clip" data-text="<?php  echo $_W['siteroot'];?>"><?php  echo $_W['siteroot'];?></a>
				</div>
				<h3>公众号微信支付参数</h3>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">微信支付方式</label>
					<div class="col-sm-9 col-xs-12">
						<div class="radio radio-inline">
							<input type="radio" value="default" name="wechat[type]" id="wechat-type-default" <?php  if($payment['wechat']['type'] == 'default') { ?>checked<?php  } ?>>
							<label for="wechat-type-default">本公众号</label>
						</div>
						<div class="radio radio-inline">
							<input type="radio" value="borrow" name="wechat[type]" id="wechat-type-borrow" <?php  if($payment['wechat']['type'] == 'borrow') { ?>checked<?php  } ?>>
							<label for="wechat-type-borrow">借用</label>
						</div>
						<div class="radio radio-inline">
							<input type="radio" value="partner" name="wechat[type]" id="wechat-type-partner" <?php  if($payment['wechat']['type'] == 'partner') { ?>checked<?php  } ?>>
							<label for="wechat-type-partner">服务商</label>
						</div>
						<div class="radio radio-inline">
							<input type="radio" value="borrow_partner" name="wechat[type]" id="wechat-type-borrow-partner" <?php  if($payment['wechat']['type'] == 'borrow_partner') { ?>checked<?php  } ?>>
							<label for="wechat-type-borrow-partner">借用服务商</label>
						</div>
					</div>
				</div>
				<div id="wechat-core-container" <?php  if($payment['wechat']['type'] == 'partner' || $payment['wechat']['type'] == 'borrow_partner') { ?>class="hide"<?php  } ?>>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">接口类型</label>
						<div class="col-sm-9 col-xs-12">
							<div class="radio radio-inline">
								<input type="radio" value="1" name="wechat[core][version]" id="wechat-version-1" <?php  if($payment['wechat']['core']['version'] == 1) { ?>checked<?php  } ?>>
								<label for="wechat-version-1">旧版</label>
							</div>
							<div class="radio radio-inline">
								<input type="radio" value="2" name="wechat[core][version]" id="wechat-version-2" <?php  if($payment['wechat']['core']['version'] == 2 || empty($payment['wechat']['core']['version'])) { ?>checked<?php  } ?>>
								<label for="wechat-version-2">新版(2014年9月之后申请的)</label>
							</div>
							<span class="help-block">由于微信支付接口调整，需要根据申请时间来区分支付接口</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">公众号(AppId)</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" class="form-control" name="wechat[core][appid]" value="<?php  echo $payment['wechat']['core']['appid'];?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">应用密钥(AppSecret)</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" class="form-control" name="wechat[core][appsecret]" value="<?php  echo $payment['wechat']['core']['appsecret'];?>">
						</div>
					</div>
					<div class="wechat-version-1 <?php  if($payment['wechat']['core']['version'] == '2' || empty($payment['wechat']['core']['version'])) { ?>hide<?php  } ?>">
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label">商户身份(partnerId)</label>
							<div class="col-sm-9 col-xs-12">
								<input type="text" class="form-control" name="wechat[core][partner]" value="<?php  echo $payment['wechat']['core']['partner'];?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label">商户密钥(partnerKey)</label>
							<div class="col-sm-9 col-xs-12">
								<input type="text" class="form-control" name="wechat[core][key]" value="<?php  echo $payment['wechat']['core']['key'];?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label">通信密钥(paySignKey)</label>
							<div class="col-sm-9 col-xs-12">
								<input type="text" class="form-control" name="wechat[core][signkey]" value="<?php  echo $payment['wechat']['core']['signkey'];?>">
							</div>
						</div>
					</div>
					<div class="wechat-version-2 <?php  if($payment['wechat']['core']['version'] == '1') { ?>hide<?php  } ?>">
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label">微信支付商户号(Mch_Id)</label>
							<div class="col-sm-9 col-xs-12">
								<input type="text" class="form-control" name="wechat[core][mchid]" value="<?php  echo $payment['wechat']['core']['mchid'];?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label">微信支付密钥(APIKEY)</label>
							<div class="col-sm-9 col-xs-12">
								<div class="input-group">
									<input type="text" class="form-control" name="wechat[core][apikey]" value="<?php  echo $payment['wechat']['core']['apikey'];?>">
									<span class="input-group-addon js-random">生成新的</span>
								</div>
								<span class="help-block">此值需要手动在腾讯商户后台API密钥保持一致，<a href="http://bbs.we7.cc/thread-5788-1-1.html" target="_blank">查看设置教程</a></span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">CERT证书文件</label>
						<div class="col-sm-9 col-xs-12">
							<textarea name="wechat[core][apiclient_cert]" class="form-control" placeholder="为保证安全性, 不显示证书内容. 若要修改, 请直接输入" cols="30" rows="7"></textarea>
							<span class="help-block">
								<?php  if(!empty($payment['wechat']['core']['apiclient_cert'])) { ?>
									<span class="label label-success">已上传</span>
								<?php  } else { ?>
									<span class="label label-danger">未上传</span>
								<?php  } ?>
								从商户平台上下载支付证书, 解压并取得其中的 <span class="bg-danger">apiclient_cert.pem</span> 用记事本打开并复制文件内容, 填至此处
							</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">KEY证书密钥</label>
						<div class="col-sm-9 col-xs-12">
							<textarea name="wechat[core][apiclient_key]" class="form-control" placeholder="为保证安全性, 不显示证书内容. 若要修改, 请直接输入" cols="30" rows="7"></textarea>
							<span class="help-block">
								<?php  if(!empty($payment['wechat']['core']['apiclient_key'])) { ?>
									<span class="label label-success">已上传</span>
								<?php  } else { ?>
									<span class="label label-danger">未上传</span>
								<?php  } ?>
								从商户平台上下载支付证书, 解压并取得其中的 <span class="bg-danger">apiclient_key.pem</span> 用记事本打开并复制文件内容, 填至此处
							</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">ROOTCA证书</label>
						<div class="col-sm-9 col-xs-12">
							<textarea name="wechat[core][rootca]" class="form-control" placeholder="为保证安全性, 不显示证书内容. 若要修改, 请直接输入" cols="30" rows="7"></textarea>
							<span class="help-block">
								<?php  if(!empty($payment['wechat']['core']['rootca'])) { ?>
									<span class="label label-success">已上传</span>
								<?php  } else { ?>
									<span class="label label-danger">未上传</span>
								<?php  } ?>
								从商户平台上下载支付证书, 解压并取得其中的 <span class="bg-danger"> rootca.pem</span> 用记事本打开并复制文件内容, 填至此处。如果您下载的证书文件夹里没有 rootca.pem 证书，则此项可不填写
							</span>
						</div>
					</div>
				</div>
				<div id="wechat-partner-container" <?php  if($payment['wechat']['type'] != 'partner' && $payment['wechat']['type'] != 'borrow_partner') { ?>class="hide"<?php  } ?>>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">服务商公众号(AppId)</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" class="form-control" name="wechat[partner][appid]" value="<?php  echo $payment['wechat']['partner']['appid'];?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">服务商应用密钥(AppSecret)</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" class="form-control" name="wechat[partner][appsecret]" value="<?php  echo $payment['wechat']['partner']['appsecret'];?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">子商户公众账号(AppId)</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" class="form-control" name="wechat[partner][sub_appid]" value="<?php  echo $payment['wechat']['partner']['sub_appid'];?>">
							<span class="help-block">微信分配的子商户公众账号ID. 注意：此参数可不填写</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">服务商微信支付商户号(Mch_Id)</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" class="form-control" name="wechat[partner][mchid]" value="<?php  echo $payment['wechat']['partner']['mchid'];?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">微信支付商户号(sub_Mch_Id)</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" class="form-control" name="wechat[partner][sub_mch_id]" value="<?php  echo $payment['wechat']['partner']['sub_mch_id'];?>">
							<span class="help-block">这里填写子商户的Mch_Id</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">微信支付密钥(APIKEY)</label>
						<div class="col-sm-9 col-xs-12">
							<div class="input-group">
								<input type="text" class="form-control" name="wechat[partner][apikey]" value="<?php  echo $payment['wechat']['partner']['apikey'];?>">
								<span class="input-group-addon js-random">生成新的</span>
							</div>
							<span class="help-block">注意:这里的密钥是服务商微信支付的密钥,并不是子商户的密钥。此值需要手动在腾讯商户后台API密钥保持一致，<a href="http://bbs.we7.cc/thread-5788-1-1.html" target="_blank">查看设置教程</a></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">CERT证书文件</label>
						<div class="col-sm-9 col-xs-12">
							<textarea name="wechat[partner][apiclient_cert]" class="form-control" placeholder="为保证安全性, 不显示证书内容. 若要修改, 请直接输入" cols="30" rows="7"></textarea>
							<span class="help-block">
								<?php  if(!empty($payment['wechat']['partner']['apiclient_cert'])) { ?>
									<span class="label label-success">已上传</span>
								<?php  } else { ?>
									<span class="label label-danger">未上传</span>
								<?php  } ?>
								从商户平台上下载支付证书, 解压并取得其中的 <span class="bg-danger">apiclient_cert.pem</span> 用记事本打开并复制文件内容, 填至此处
							</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">KEY证书密钥</label>
						<div class="col-sm-9 col-xs-12">
							<textarea name="wechat[partner][apiclient_key]" class="form-control" placeholder="为保证安全性, 不显示证书内容. 若要修改, 请直接输入" cols="30" rows="7"></textarea>
							<span class="help-block">
								<?php  if(!empty($payment['wechat']['partner']['apiclient_key'])) { ?>
									<span class="label label-success">已上传</span>
								<?php  } else { ?>
									<span class="label label-danger">未上传</span>
								<?php  } ?>
								从商户平台上下载支付证书, 解压并取得其中的 <span class="bg-danger">apiclient_key.pem</span> 用记事本打开并复制文件内容, 填至此处
							</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">ROOTCA证书</label>
						<div class="col-sm-9 col-xs-12">
							<textarea name="wechat[partner][rootca]" class="form-control" placeholder="为保证安全性, 不显示证书内容. 若要修改, 请直接输入" cols="30" rows="7"></textarea>
							<span class="help-block">
								<?php  if(!empty($payment['wechat']['partner']['rootca'])) { ?>
									<span class="label label-success">已上传</span>
								<?php  } else { ?>
									<span class="label label-danger">未上传</span>
								<?php  } ?>
								从商户平台上下载支付证书, 解压并取得其中的 <span class="bg-danger"> rootca.pem</span> 用记事本打开并复制文件内容, 填至此处。如果您下载的证书文件夹里没有 rootca.pem 证书，则此项可不填写
							</span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
					<div class="col-sm-9 col-xs-12">
						<a id="del-default" href="<?php  echo iurl('config/trade/del_cert', array('pay_type' => 'wechat', 'wechat_type' => 'default'))?>" class="btn btn-danger btn-sm js-post <?php  if($payment['wechat']['type'] != 'default') { ?>hide<?php  } ?>" data-toggle="tooltip" data-placement="top" data-confirm="删除后需上传新的证书才可正常使用，确定删除吗?">删除本公众号证书</a>
						<a id="del-borrow" href="<?php  echo iurl('config/trade/del_cert', array('pay_type' => 'wechat', 'wechat_type' => 'borrow'))?>" class="btn btn-danger btn-sm js-post <?php  if($payment['wechat']['type'] != 'borrow') { ?>hide<?php  } ?>" data-toggle="tooltip" data-placement="top" data-confirm="删除后需上传新的证书才可正常使用，确定删除吗?">删除借用证书</a>
						<a id="del-partner" href="<?php  echo iurl('config/trade/del_cert', array('pay_type' => 'wechat', 'wechat_type' => 'partner'))?>" class="btn btn-danger btn-sm js-post <?php  if($payment['wechat']['type'] != 'partner') { ?>hide<?php  } ?>" data-toggle="tooltip" data-placement="top" data-confirm="删除后需上传新的证书才可正常使用，确定删除吗?">删除服务商证书</a>
						<a id="del-borrow_partner" href="<?php  echo iurl('config/trade/del_cert', array('pay_type' => 'wechat', 'wechat_type' => 'borrow_partner'))?>" class="btn btn-danger btn-sm js-post <?php  if($payment['wechat']['type'] != 'borrow_partner') { ?>hide<?php  } ?>" data-toggle="tooltip" data-placement="top" data-confirm="删除后需上传新的证书才可正常使用，确定删除吗?">删除借用服务商证书</a>
					</div>
				</div>

				<h3>H5微信支付参数</h3>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">公众号(AppId)</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" class="form-control" name="h5[appid]" value="<?php  echo $payment['h5_wechat']['appid'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">应用密钥(AppSecret)</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" class="form-control" name="h5[appsecret]" value="<?php  echo $payment['h5_wechat']['appsecret'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">微信支付商户号(Mch_Id)</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" class="form-control" name="h5[mchid]" value="<?php  echo $payment['h5_wechat']['mchid'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">微信支付密钥(APIKEY)</label>
					<div class="col-sm-9 col-xs-12">
						<div class="input-group">
							<input type="text" class="form-control" name="h5[apikey]" value="<?php  echo $payment['h5_wechat']['apikey'];?>">
							<span class="input-group-addon js-random">生成新的</span>
						</div>
						<span class="help-block">此值需要手动在腾讯商户后台API密钥保持一致，<a href="http://bbs.we7.cc/thread-5788-1-1.html" target="_blank">查看设置教程</a></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">CERT证书文件</label>
					<div class="col-sm-9 col-xs-12">
						<textarea name="h5[apiclient_cert]" class="form-control" placeholder="为保证安全性, 不显示证书内容. 若要修改, 请直接输入" cols="30" rows="7"></textarea>
						<span class="help-block">
							<?php  if(!empty($payment['h5_wechat']['apiclient_cert'])) { ?>
								<span class="label label-success">已上传</span>
							<?php  } else { ?>
								<span class="label label-danger">未上传</span>
							<?php  } ?>
							从商户平台上下载支付证书, 解压并取得其中的 <span class="bg-danger">apiclient_cert.pem</span> 用记事本打开并复制文件内容, 填至此处
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">KEY证书密钥</label>
					<div class="col-sm-9 col-xs-12">
						<textarea name="h5[apiclient_key]" class="form-control" placeholder="为保证安全性, 不显示证书内容. 若要修改, 请直接输入" cols="30" rows="7"></textarea>
						<span class="help-block">
							<?php  if(!empty($payment['h5_wechat']['apiclient_key'])) { ?>
								<span class="label label-success">已上传</span>
							<?php  } else { ?>
								<span class="label label-danger">未上传</span>
							<?php  } ?>
							从商户平台上下载支付证书, 解压并取得其中的 <span class="bg-danger">apiclient_key.pem</span> 用记事本打开并复制文件内容, 填至此处
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">ROOTCA证书</label>
					<div class="col-sm-9 col-xs-12">
						<textarea name="h5[rootca]" class="form-control" placeholder="为保证安全性, 不显示证书内容. 若要修改, 请直接输入" cols="30" rows="7"></textarea>
						<span class="help-block">
							<?php  if(!empty($payment['h5_wechat']['rootca'])) { ?>
								<span class="label label-success">已上传</span>
							<?php  } else { ?>
								<span class="label label-danger">未上传</span>
							<?php  } ?>
							从商户平台上下载支付证书, 解压并取得其中的 <span class="bg-danger"> rootca.pem</span> 用记事本打开并复制文件内容, 填至此处。如果您下载的证书文件夹里没有 rootca.pem 证书，则此项可不填写
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
					<div class="col-sm-9 col-xs-12">
						<a href="<?php  echo iurl('config/trade/del_cert', array('pay_type' => 'h5_wechat'))?>" class="btn btn-danger btn-sm js-post" data-toggle="tooltip" data-placement="top" data-confirm="删除后需上传新的证书才可正常使用，确定删除吗?">删除已有证书</a>
					</div>
				</div>


				<h3>支付宝支付参数</h3>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">支付宝账号</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" class="form-control" name="alipay[account]" value="<?php  echo $payment['alipay']['account'];?>">
						<span class="help-block">
							 如您没有支付宝帐号，<a href="https://memberprod.alipay.com/account/reg/enterpriseIndex.htm" target="_blank">请点击这里注册</a>
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">合作者身份</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" class="form-control" name="alipay[partner]" value="<?php  echo $payment['alipay']['partner'];?>">
						<span class="help-block">
							支付宝签约用户请在此处填写支付宝分配给您的合作者身份，签约用户的手续费按照您与支付宝官方的签约协议为准。<br>
							如果您还未签约，<a href="https://memberprod.alipay.com/account/reg/enterpriseIndex.htm" target="_blank">请点击这里签约</a>；如果已签约,<a href="https://b.alipay.com/order/pidKey.htm?pid=2088501719138773&product=fastpay" target="_blank">请点击这里获取PID、Key</a>;如果在签约时出现合同模板冲突，请咨询0571-88158090
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">校验密钥</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" class="form-control" name="alipay[secret]" value="<?php  echo $payment['alipay']['secret'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">支付宝应用id(用于退款)</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" class="form-control" name="alipay[appid]" value="<?php  echo $payment['alipay']['appid'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">密钥类型</label>
					<div class="col-sm-9 col-xs-12">
						<div class="radio radio-inline">
							<input type="radio" value="RSA" name="alipay[rsa_type]" id="alipay-rsatype-1" <?php  if($payment['alipay']['rsa_type'] == 'RSA' || empty($payment['alipay']['rsa_type'])) { ?>checked<?php  } ?>>
							<label for="alipay-rsatype-1">RSA</label>
						</div>
						<div class="radio radio-inline">
							<input type="radio" value="RSA2" name="alipay[rsa_type]" id="alipay-rsatype-2" <?php  if($payment['alipay']['rsa_type'] == 'RSA2') { ?>checked<?php  } ?>>
							<label for="alipay-rsatype-2">RSA2</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">应用私钥(private_key,用于退款)</label>
					<div class="col-sm-9 col-xs-12">
						<textarea name="alipay[private_key]" class="form-control" placeholder="为保证安全性, 不显示证书内容. 若要修改, 请直接输入" cols="30" rows="7"></textarea>
						<span class="help-block">
							<?php  if(!empty($payment['alipay']['private_key'])) { ?>
								<span class="label label-success">已上传</span>
							<?php  } else { ?>
								<span class="label label-danger">未上传</span>
							<?php  } ?>
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">支付宝公钥(public_key,用于退款)</label>
					<div class="col-sm-9 col-xs-12">
						<textarea name="alipay[public_key]" class="form-control" placeholder="为保证安全性, 不显示证书内容. 若要修改, 请直接输入" cols="30" rows="7"></textarea>
						<span class="help-block">
							<?php  if(!empty($payment['alipay']['public_key'])) { ?>
								<span class="label label-success">已上传</span>
							<?php  } else { ?>
								<span class="label label-danger">未上传</span>
							<?php  } ?>
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
					<div class="col-sm-9 col-xs-12">
						<a href="<?php  echo iurl('config/trade/del_cert', array('pay_type' => 'alipay'))?>" class="btn btn-danger btn-sm js-post" data-toggle="tooltip" data-placement="top" data-confirm="删除后需上传新的证书才可正常使用，确定删除吗?">删除已有证书</a>
					</div>
				</div>

				<h3>一码付支付参数</h3>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">一码付链接</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" class="form-control" name="yimafu[host]" value="<?php  echo $payment['yimafu']['host'];?>">
						<span class="help-block">
							  请确定链接地址正确
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">商户号</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" class="form-control" name="yimafu[mchid]" value="<?php  echo $payment['yimafu']['mchid'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">一码付密钥</label>
					<div class="col-sm-9 col-xs-12">
							<input type="text" class="form-control" name="yimafu[secret]" value="<?php  echo $payment['yimafu']['secret'];?>">
					</div>
				</div>
			</div>
			<div class="tab-pane" role="tabpanel" id="wechat">
				<div class="panel panel-default panel-payment-switch">
					<div class="panel-body">
						<div class="col-sm-9 col-xs-12">
							<h4>微信支付</h4>
							<span>
								<p>在开启微信支付前，请到 <a href="<?php  echo iurl('config/trade/payment');?>" target="_blank">支付参数</a> 去设置好参数。</p>
							</span>
						</div>
						<div class="col-sm-2 pull-right">
							<input type="checkbox" class="js-switch"  name="weixin[wechat]" value="1" <?php  if(in_array('wechat', $payment['weixin'])) { ?>checked<?php  } ?>/>
						</div>
					</div>
				</div>
				<div class="panel panel-default panel-payment-switch">
					<div class="panel-body">
						<div class="col-sm-9 col-xs-12">
							<h4>支付宝</h4>
							<span>
								<p>在开启支付宝支付前，请到 <a href="<?php  echo iurl('config/trade/payment');?>" target="_blank">支付参数</a> 去设置好参数。</p>
							</span>
						</div>
						<div class="col-sm-2 pull-right">
							<input type="checkbox" name="weixin[alipay]" value="1" class="js-switch" <?php  if(in_array('alipay', $payment['weixin'])) { ?>checked<?php  } ?>/>
						</div>
					</div>
				</div>
				<div class="panel panel-default panel-payment-switch">
					<div class="panel-body">
						<div class="col-sm-9 col-xs-12">
							<h4>余额支付</h4>
							<span>开启后，粉丝可以用账户余额去平台消费.</span>
						</div>
						<div class="col-sm-2 pull-right">
							<input type="checkbox" name="weixin[credit]" value="1" class="js-switch" <?php  if(in_array('credit', $payment['weixin'])) { ?>checked<?php  } ?>/>
						</div>
					</div>
				</div>
				<div class="panel panel-default panel-payment-switch">
					<div class="panel-body">
						<div class="col-sm-9 col-xs-12">
							<h4>货到付款</h4>
							<span>设置是否支持货到付款</span>
						</div>
						<div class="col-sm-2 pull-right">
							<input type="checkbox" name="weixin[delivery]" value="1" class="js-switch" <?php  if(in_array('delivery', $payment['weixin'])) { ?>checked<?php  } ?>/>
						</div>
					</div>
				</div>
				<div class="panel panel-default panel-payment-switch">
					<div class="panel-body">
						<div class="col-sm-9 col-xs-12">
							<h4>一码付</h4>
							<span>
								<p>在开启一码付支付前，请到 <a href="<?php  echo iurl('config/trade/payment');?>" target="_blank">支付参数</a> 去设置好参数。</p>
							</span>
						</div>
						<div class="col-sm-2 pull-right">
							<input type="checkbox" name="weixin[yimafu]" value="1" class="js-switch" <?php  if(in_array('yimafu', $payment['weixin'])) { ?>checked<?php  } ?>/>
						</div>
					</div>
				</div>
				<div class="panel panel-default panel-payment-switch">
					<div class="panel-body" id="peerpay">
						<div class="col-sm-9 col-xs-12">
							<h4>找人代付</h4>
							<span>启用代付功能后，代付发起人（买家）下单后，可将订单分享给小伙伴（朋友圈、微信群、微信好友），请他帮忙付款。</span>
							<p class="text-danger">
								注意：
								找人代付功能只能在微信端使用并且仅支持外卖订单,并且不支持退款操作.除非非常希望使用该功能，官方不建议开启该支付方式.
							</p>
						</div>
						<div class="col-sm-2 pull-right">
							<input type="checkbox" name="weixin[peerpay]" value="1" class="js-switch" <?php  if(in_array('peerpay', $payment['weixin'])) { ?>checked<?php  } ?>/>
						</div>
					</div>
					<div class="panel-body <?php  if(!in_array('peerpay', $payment['weixin'])) { ?>hide<?php  } ?>" id="peerpay-container">
						<div class="tabs-container">
							<ul class="nav nav-tabs">
								<li class="active"><a href="#tab_basic"  data-toggle="pill">发起人的求助</a></li>
								<li><a href="#tab_money" data-toggle="pill">代付人的留言</a></li>
							</ul>
							<div class="tab-content">
								<div class="tab-pane active" id="tab_basic">
									<div class="form-group">
										<div class="col-sm-2 control-label">求助语句</div>
										<div class="col-sm-9">
											<div class="help-items">
												<?php  if(!empty($payment['peerpay']['help_words'])) { ?>
													<?php  if(is_array($payment['peerpay']['help_words'])) { foreach($payment['peerpay']['help_words'] as $help_words) { ?>
														<div class="input-group help-item" style="margin-bottom: 10px;">
															<input class="form-control" type="text" value="<?php  echo $help_words;?>" name="help_words[]">
															<div class="input-group-btn del-item">
																<a class="btn btn-danger" href="javascript:;">
																	<i class="fa fa-remove"></i>
																</a>
															</div>
														</div>
													<?php  } } ?>
												<?php  } ?>
											</div>
											<div style="margin-top: 5px;">
												<a href="javascript:;" class="btn btn-default add-help-words"><i class="fa fa-plus"></i> 增加求助语句</a>
											</div>
											<div class="help-block">随机出现以上一条求助语句 例如 : 感情深不深,看你跟不跟~</div>
										</div>
									</div>
								</div>
								<div class="tab-pane" id="tab_money">
									<div class="form-group">
										<div class="col-sm-2 control-label">代付人的留言</div>
										<div class="col-sm-9">
											<div class="peer-items">
												<?php  if(!empty($payment['peerpay']['notes'])) { ?>
													<?php  if(is_array($payment['peerpay']['notes'])) { foreach($payment['peerpay']['notes'] as $note) { ?>
														<div class="input-group peer-item" style="margin-bottom: 10px;">
															<div class="input-group-addon">留言:</div>
															<input class="form-control" type="text" value="<?php  echo $note;?>" name="notes[]">
															<div class="input-group-btn del-item">
																<a class="btn btn-danger" href="javascript:;">
																	<i class="fa fa-remove"></i>
																</a>
															</div>
														</div>
													<?php  } } ?>
												<?php  } ?>
											</div>
											<div style="margin-top: 5px;">
												<a href="javascript:;" class="btn btn-default add-peerpay"><i class="fa fa-plus"></i> 增加留言</a>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-2 control-label">代付限制</div>
										<div class="col-sm-9">
											<div class="input-group">
												<div class="input-group-addon">最多代付</div>
												<input class="form-control" type="text" value="<?php  echo $payment['peerpay']['peerpay_max_limit'];?>" name="peerpay_max_limit">
												<div class="input-group-addon">元</div>
											</div>
											<div class="help-block">每人 最多代付 多少钱 如果是空 或者 0 则不限制。</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane" role="tabpanel" id="wap">
				<div class="panel panel-default panel-payment-switch">
					<div class="panel-body">
						<div class="col-sm-9 col-xs-12">
							<h4>微信支付</h4>
							<span>
								<p>在开启微信支付前，请到 <a href="<?php  echo iurl('config/trade/payment');?>" target="_blank">支付参数</a> 去设置好参数。<span class="text-danger">Wap中使用微信支付,需要去微信支付商户平台开启“H5支付”产品权限</span></p>
							</span>
						</div>
						<div class="col-sm-2 pull-right">
							<input type="checkbox" class="js-switch"  name="wap[wechat]" value="1" <?php  if(in_array('wechat', $payment['wap'])) { ?>checked<?php  } ?>/>
						</div>
					</div>
				</div>
				<div class="panel panel-default panel-payment-switch">
					<div class="panel-body">
						<div class="col-sm-9 col-xs-12">
							<h4>支付宝</h4>
							<span>
								<p>在开启支付宝支付前，请到 <a href="<?php  echo iurl('config/trade/payment');?>" target="_blank">支付参数</a> 去设置好参数。</p>
							</span>
						</div>
						<div class="col-sm-2 pull-right">
							<input type="checkbox" name="wap[alipay]" value="1" class="js-switch" <?php  if(in_array('alipay', $payment['wap'])) { ?>checked<?php  } ?>/>
						</div>
					</div>
				</div>
				<div class="panel panel-default panel-payment-switch">
					<div class="panel-body">
						<div class="col-sm-9 col-xs-12">
							<h4>余额支付</h4>
							<span>开启后，粉丝可以用账户余额去平台消费.</span>
						</div>
						<div class="col-sm-2 pull-right">
							<input type="checkbox" name="wap[credit]" value="1" class="js-switch" <?php  if(in_array('credit', $payment['wap'])) { ?>checked<?php  } ?>/>
						</div>
					</div>
				</div>
				<div class="panel panel-default panel-payment-switch">
					<div class="panel-body">
						<div class="col-sm-9 col-xs-12">
							<h4>货到付款</h4>
							<span>设置是否支持货到付款</span>
						</div>
						<div class="col-sm-2 pull-right">
							<input type="checkbox" name="wap[delivery]" value="1" class="js-switch" <?php  if(in_array('delivery', $payment['wap'])) { ?>checked<?php  } ?>/>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane" role="tabpanel" id="app">
				<div class="panel panel-default panel-payment-switch">
					<div class="panel-body">
						<div class="col-sm-9 col-xs-12">
							<h4>微信支付</h4>
							<span><p>开启后在APP中可以调起微信进行支付</p></span>
						</div>
						<div class="col-sm-2 pull-right">
							<input type="checkbox" name="app_type[wechat]" value="1" class="js-switch" <?php  if(in_array('wechat', $payment['app'])) { ?>checked<?php  } ?>/>
						</div>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label">微信AppId</label>
							<div class="col-sm-9 col-xs-12">
								<input type="text" class="form-control" name="app[wechat][appid]" value="<?php  echo $payment['app_wechat']['appid'];?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label">微信AppSecret</label>
							<div class="col-sm-9 col-xs-12">
								<input type="text" class="form-control" name="app[wechat][appsecret]" value="<?php  echo $payment['app_wechat']['appsecret'];?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label">微信商户名称</label>
							<div class="col-sm-9 col-xs-12">
								<input type="text" class="form-control" name="app[wechat][merchname]" value="<?php  echo $payment['app_wechat']['merchname'];?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label">微信商户ID</label>
							<div class="col-sm-9 col-xs-12">
								<input type="text" class="form-control" name="app[wechat][mchid]" value="<?php  echo $payment['app_wechat']['mchid'];?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label">微信商户API密钥</label>
							<div class="col-sm-9 col-xs-12">
								<div class="input-group">
									<input type="text" class="form-control" name="app[wechat][apikey]" value="<?php  echo $payment['app_wechat']['apikey'];?>">
									<span class="input-group-addon js-random">生成新的</span>
								</div>
								<span class="help-block">此值需要手动在腾讯商户后台API密钥保持一致，<a href="http://bbs.we7.cc/thread-5788-1-1.html" target="_blank">查看设置教程</a></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label">CERT证书文件</label>
							<div class="col-sm-9 col-xs-12">
								<textarea name="app[wechat][apiclient_cert]" class="form-control" placeholder="为保证安全性, 不显示证书内容. 若要修改, 请直接输入" cols="30" rows="7"></textarea>
							<span class="help-block">
								<?php  if(!empty($payment['app_wechat']['apiclient_cert'])) { ?>
									<span class="label label-success">已上传</span>
								<?php  } else { ?>
									<span class="label label-danger">未上传</span>
								<?php  } ?>
								从商户平台上下载支付证书, 解压并取得其中的 <span class="bg-danger">apiclient_cert.pem</span> 用记事本打开并复制文件内容, 填至此处
							</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label">KEy证书密钥</label>
							<div class="col-sm-9 col-xs-12">
								<textarea name="app[wechat][apiclient_key]" class="form-control" placeholder="为保证安全性, 不显示证书内容. 若要修改, 请直接输入" cols="30" rows="7"></textarea>
							<span class="help-block">
								<?php  if(!empty($payment['app_wechat']['apiclient_key'])) { ?>
									<span class="label label-success">已上传</span>
								<?php  } else { ?>
									<span class="label label-danger">未上传</span>
								<?php  } ?>
								从商户平台上下载支付证书, 解压并取得其中的 <span class="bg-danger">apiclient_key.pem</span> 用记事本打开并复制文件内容, 填至此处
							</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label">ROOTCA证书</label>
							<div class="col-sm-9 col-xs-12">
								<textarea name="app[wechat][rootca]" class="form-control" placeholder="为保证安全性, 不显示证书内容. 若要修改, 请直接输入" cols="30" rows="7"></textarea>
							<span class="help-block">
								<?php  if(!empty($payment['app_wechat']['rootca'])) { ?>
									<span class="label label-success">已上传</span>
								<?php  } else { ?>
									<span class="label label-danger">未上传</span>
								<?php  } ?>
								从商户平台上下载支付证书, 解压并取得其中的 <span class="bg-danger"> rootca.pem</span> 用记事本打开并复制文件内容, 填至此处。如果您下载的证书文件夹里没有 rootca.pem 证书，则此项可不填写
							</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
							<div class="col-sm-9 col-xs-12">
								<a href="<?php  echo iurl('config/trade/del_cert', array('pay_type' => 'app_wechat'))?>" class="btn btn-danger btn-sm js-post" data-toggle="tooltip" data-placement="top" data-confirm="删除后需上传新的证书才可正常使用，确定删除吗?">删除已有证书</a>
							</div>
						</div>
					</div>
				</div>
				<div class="panel panel-default panel-payment-switch">
					<div class="panel-body">
						<div class="col-sm-9 col-xs-12">
							<h4>支付宝支付</h4>
							<span><p>开启后在APP中可以调起支付宝进行支付</p></span>
						</div>
						<div class="col-sm-2 pull-right">
							<input type="checkbox" name="app_type[alipay]" value="1" class="js-switch" <?php  if(in_array('alipay', $payment['app'])) { ?>checked<?php  } ?>/>
						</div>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label">APPID</label>
							<div class="col-sm-9 col-xs-12">
								<input type="text" class="form-control" name="app[alipay][appid]" value="<?php  echo $payment['app_alipay']['appid'];?>">
								<span class="help-block">开放平台应用id</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label">密钥类型</label>
							<div class="col-sm-9 col-xs-12">
								<div class="radio radio-inline">
									<input type="radio" value="RSA" name="app[alipay][rsa_type]" id="app-alipay-rsatype-1" <?php  if($payment['app_alipay']['rsa_type'] == 'RSA' || empty($payment['app_alipay']['rsa_type'])) { ?>checked<?php  } ?>>
									<label for="app-alipay-rsatype-1">RSA</label>
								</div>
								<div class="radio radio-inline">
									<input type="radio" value="RSA2" name="app[alipay][rsa_type]" id="app-alipay-rsatype-2" <?php  if($payment['app_alipay']['rsa_type'] == 'RSA2') { ?>checked<?php  } ?>>
									<label for="app-alipay-rsatype-2">RSA2</label>
								</div>
								<span class="help-block">
									由于支付宝近期更新了新版的接口加密方式，新申请的支付宝应用必须使用新版(RSA2)的加密方式。使用旧版的支付宝接口生成的APP只能使用旧版(RSA1)的支付宝接口，请根据自己的情况选择密钥类型。)
								</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label">应用私钥(private_key)</label>
							<div class="col-sm-9 col-xs-12">
								<textarea name="app[alipay][private_key]" class="form-control" placeholder="为保证安全性, 不显示证书内容. 若要修改, 请直接输入" cols="30" rows="7"></textarea>
								<span class="help-block">
									<?php  if(!empty($payment['app_alipay']['private_key'])) { ?>
										<span class="label label-success">已上传</span>
									<?php  } else { ?>
										<span class="label label-danger">未上传</span>
									<?php  } ?>
								</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label">支付宝公钥(public_key)</label>
							<div class="col-sm-9 col-xs-12">
								<textarea name="app[alipay][public_key]" class="form-control" placeholder="为保证安全性, 不显示证书内容. 若要修改, 请直接输入" cols="30" rows="7"></textarea>
								<span class="help-block">
									<?php  if(!empty($payment['app_alipay']['public_key'])) { ?>
										<span class="label label-success">已上传</span>
									<?php  } else { ?>
										<span class="label label-danger">未上传</span>
									<?php  } ?>
								</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
							<div class="col-sm-9 col-xs-12">
								<a href="<?php  echo iurl('config/trade/del_cert', array('pay_type' => 'app_alipay'))?>" class="btn btn-danger btn-sm js-post" data-toggle="tooltip" data-placement="top" data-confirm="删除后需上传新的证书才可正常使用，确定删除吗?">删除已有证书</a>
							</div>
						</div>
					</div>
				</div>
				<div class="panel panel-default panel-payment-switch">
					<div class="panel-body">
						<div class="col-sm-9 col-xs-12">
							<h4>余额支付</h4>
							<span>开启后，粉丝可以用账户余额去平台消费.</span>
						</div>
						<div class="col-sm-2 pull-right">
							<input type="checkbox" name="app_type[credit]" value="1" class="js-switch" <?php  if(in_array('credit', $payment['app'])) { ?>checked<?php  } ?>/>
						</div>
					</div>
				</div>
				<div class="panel panel-default panel-payment-switch">
					<div class="panel-body">
						<div class="col-sm-9 col-xs-12">
							<h4>货到付款</h4>
							<span>设置是否支持货到付款</span>
						</div>
						<div class="col-sm-2 pull-right">
							<input type="checkbox" name="app_type[delivery]" value="1" class="js-switch" <?php  if(in_array('delivery', $payment['app'])) { ?>checked<?php  } ?>/>
						</div>
					</div>
				</div>
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
<script>
$(function(){
	$('.js-random').click(function(){
		var letters = 'abcdefghijklmnopqrstuvwxyz0123456789';
		var token = '';
		for(var i = 0; i < 32; i++) {
			var j = parseInt(Math.random() * (31 + 1));
			token += letters[j];
		}
		$(this).prev().val(token);
	});

	$('#wechat-type-default, #wechat-type-borrow').click(function(){
		$('#wechat-partner-container').addClass('hide');
		$('#wechat-core-container').removeClass('hide');
	});
	$('#wechat-type-partner, #wechat-type-borrow-partner').click(function(){
		$('#wechat-core-container').addClass('hide');
		$('#wechat-partner-container').removeClass('hide');
	});
	$('#wechat-version-1').click(function(){
		$('.wechat-version-1').removeClass('hide');
		$('.wechat-version-2').addClass('hide');
	});
	$('#wechat-version-2').click(function(){
		$('.wechat-version-1').addClass('hide');
		$('.wechat-version-2').removeClass('hide');
	});

	$('#wechat-type-default').click(function(){
		$('#del-default').removeClass('hide');
		$('#del-borrow').addClass('hide');
		$('#del-partner').addClass('hide');
		$('#del-borrow_partner').addClass('hide');
	});
	$('#wechat-type-borrow').click(function(){
		$('#del-borrow').removeClass('hide');
		$('#del-default').addClass('hide');
		$('#del-partner').addClass('hide');
		$('#del-borrow_partner').addClass('hide');
	});
	$('#wechat-type-partner').click(function(){
		$('#del-partner').removeClass('hide');
		$('#del-borrow').addClass('hide');
		$('#del-default').addClass('hide');
		$('#del-borrow_partner').addClass('hide');
	});
	$('#wechat-type-borrow-partner').click(function(){
		$('#del-borrow_partner').removeClass('hide');
		$('#del-borrow').addClass('hide');
		$('#del-partner').addClass('hide');
		$('#del-default').addClass('hide');
	});

	$(document).on('click', '#peerpay .switchery', function() {
		if($("#peerpay input").prop('checked')) {
			$('#peerpay-container').removeClass('hide');
			return false;
		}
		$('#peerpay-container').addClass('hide');
	});
	$(document).on('click', '.add-help-words', function() {
		var html = '<div class="input-group help-item" style="margin-bottom: 10px;">'+
				'		<input class="form-control" type="text" name="help_words[]">'+
				'		<div class="input-group-btn del-item">'+
				'			<a class="btn btn-danger" href="javascript:;">'+
				'				<i class="fa fa-remove"></i>'+
				'			</a>'+
				'		</div>'+
				'	</div>';
		$('#peerpay-container .help-items').append(html);
	});

	$(document).on('click', '.add-peerpay', function() {
		var html = '<div class="input-group peer-item" style="margin-bottom: 10px;">'+
				'		<div class="input-group-addon">留言:</div>'+
				'		<input class="form-control" type="text" name="notes[]">'+
				'		<div class="input-group-btn del-item">'+
				'			<a class="btn btn-danger" href="javascript:;">'+
				'				<i class="fa fa-remove"></i>'+
				'			</a>'+
				'		</div>'+
				'	</div>';
		$('#peerpay-container .peer-items').append(html);
	});

	$(document).on('click', '.del-item', function() {
		$(this).parents('.input-group').remove();
	});
	$('#form1').submit(function(){
		$('#form1').attr('stop', 1);
		var wechat_type = $(':radio[name="wechat[type]"]:checked').val();
		if(!wechat_type) {
			Notify.info('微信支付方式不能为空');
			return false;
		}
		if(wechat_type == 'default' || wechat_type == 'borrow') {
			var appid = $.trim($(':text[name="wechat[core][appid]"]').val());
			if(!appid) {
				Notify.info('公众号(appid)不能为空');
				return false;
			}
			var appsecret = $.trim($(':text[name="wechat[core][appsecret]"]').val());
			if(!appsecret) {
				Notify.info('应用密钥(AppSecret)不能为空');
				return false;
			}
			var mchid = $.trim($(':text[name="wechat[core][mchid]"]').val());
			if(!mchid) {
				Notify.info('微信支付商户号(Mch_Id)不能为空');
				return false;
			}
			var apikey = $.trim($(':text[name="wechat[core][apikey]"]').val());
			if(!appid) {
				Notify.info('微信支付密钥(APIKEY)不能为空');
				return false;
			}
		} else {
			var appid = $.trim($(':text[name="wechat[partner][appid]"]').val());
			if(!appid) {
				Notify.info('公众号(appid)不能为空');
				return false;
			}
			var appsecret = $.trim($(':text[name="wechat[partner][appsecret]"]').val());
			if(!appsecret) {
				Notify.info('应用密钥(AppSecret)不能为空');
				return false;
			}
			var sub_appid = $.trim($(':text[name="wechat[partner][sub_appid]"]').val());
			if(!appsecret) {
				Notify.info('服务商公众号(AppId)不能为空');
				return false;
			}
			var mchid = $.trim($(':text[name="wechat[partner][mchid]"]').val());
			if(!appsecret) {
				Notify.info('服务商微信支付商户号(Mch_Id)不能为空');
				return false;
			}
			var apikey = $.trim($(':text[name="wechat[partner][apikey]"]').val());
			if(!appid) {
				Notify.info('微信支付密钥(APIKEY)不能为空');
				return false;
			}
		}
		$('#form1').attr('stop', 0);
		return true;
	});
});
</script>
<?php  include itemplate('public/footer', TEMPLATE_INCLUDEPATH);?>