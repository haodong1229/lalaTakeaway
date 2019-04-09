<?php
global $_GPC, $_W;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {	
	$keyword = trim($_GPC['keyword']);
	$status = intval($_GPC['status']);
	$condition = "weid = {$_W['uniacid']} AND status = {$status} ";
	if(!empty($keyword)){
		$condition .= "AND (name like '%{$keyword}%' OR realname like '%{$keyword}%' OR telphone like '%{$keyword}%') ";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$total=pdo_fetchcolumn("SELECT count(id) FROM ".tablename(BEST_MERCHANT)." WHERE ".$condition);
	$merchant = pdo_fetchall("SELECT * FROM " . tablename(BEST_MERCHANT) . " WHERE ".$condition." ORDER BY addtime DESC LIMIT ".($pindex - 1) * $psize.",{$psize}");
	foreach($merchant as $k=>$v){
		$merchant[$k]['moneylist'] = pdo_fetchall("SELECT * FROM ".tablename(BEST_MERCHANTACCOUNT)." WHERE merchant_id = {$v['id']} ORDER BY time DESC");
		if($v['sqxqid'] > 0){
			$merchant[$k]['xiaoqu'] = pdo_fetch("SELECT name FROM ".tablename(BEST_XIAOQU)." WHERE id = {$v['sqxqid']}");
		}
		$merchant[$k]['zitidianlist'] = pdo_fetchall("SELECT * FROM ".tablename(BEST_ZITIDIAN)." WHERE weid = {$_W['uniacid']} AND openid = '{$v['openid']}' AND ztdtype = 1");
	}
	$pager = pagination($total, $pindex, $psize);
	
	//团长关系
	$tzgxmer = pdo_fetchall("SELECT * FROM " . tablename(BEST_MERCHANT) . " WHERE weid = {$_W['uniacid']} AND tzfopenid != '' AND fopenid = ''");
	foreach($tzgxmer as $k=>$v){
		pdo_update(BEST_MERCHANT,array('fopenid'=>$v['tzfopenid']),array('id'=>$v['id']));
	}
	
	
	$ssmerchant = pdo_fetchall("SELECT * FROM " . tablename(BEST_MERCHANT) . " WHERE weid = {$_W['uniacid']} AND fopenid != ''");
	foreach($ssmerchant as $kk=>$vv){
		$fmer = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} AND openid = '{$vv['fopenid']}'");
		if(!empty($fmer)){
			$data2['istz'] = 1;
			if($fmer['tztime'] == 0){
				$data2['tztime'] = TIMESTAMP;
			}
			pdo_update(BEST_MERCHANT,$data2,array('id'=>$fmer['id']));
		}
	}
	include $this->template('web/merchant');
} elseif ($operation == 'post') {
	$id = intval($_GPC['id']);
	$merchant = pdo_fetch("SELECT * FROM " . tablename(BEST_MERCHANT) . " WHERE id = {$id} AND status = 0");
	if (empty($merchant)) {
		message('抱歉，该团长不存在！', $this->createWebUrl('merchant', array('op' => 'display')), 'error');
	}
	$data = array(
		'status' => 1,
	);
	if($merchant['sqxqid'] > 0){
		$data['xqz'] = 1;
		$dataxq['zzopenid'] = $merchant['openid'];
		pdo_update(BEST_XIAOQU, $dataxq, array('id' => $merchant['sqxqid']));
	}
	pdo_update(BEST_MERCHANT, $data, array('id' => $id, 'weid' => $_W['uniacid']));
	
	if($_W["account"]["type_name"] == "公众号"){
		if($this->module['config']['istplon'] == 1){
			$or_paysuccess_redirect = $_W["siteroot"].'app/'.str_replace("./","",$this->createMobileUrl("merchant"));
			$postdata = array(
				'first' => array(
					'value' => "你的团长资格审核已经处理",
					'color' => '#ff510'
				),
				'keyword1' => array(
					'value' => "团长注册",
					'color' => '#ff510'
				),
				'keyword2' => array(
					'value' => "通过",
					'color' => '#ff510'
				),
				'keyword3' => array(
					'value' => date("Y年m月d日H:i",TIMESTAMP),
					'color' => '#ff510'
				),
				'remark' => array(
					'value' => '点击查看详情',
					'color' => '#ff510'
				),			
			);
			$account_api = WeAccount::create();
			$account_api->sendTplNotice($merchant['openid'],$this->module['config']['agent_tz'],$postdata,$or_paysuccess_redirect,'#980000');
		}
	}else{
		if($this->module['config']['istplon'] == 1){
			$temvalue = array(
				"keyword1"=>array(
					"value"=>"您提交的团长申请已经通过审核",
					"color"=>"#4a4a4a"
				),
				"keyword2"=>array(
					"value"=>date("Y-m-d H:i:s",TIMESTAMP),
					"color"=>"#9b9b9b"
				)
			);
			$account_api = WeAccount::create();
			$access_token = $account_api->getAccessToken();
			$url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token;
			$dd = array();
			$dd['touser'] = $merchant['openid'];
			$dd['template_id'] = $this->module['config']['agent_tz'];
			$dd['page'] = 'cy163_salesjl/pages/merchantcenter/merchantcenter'; //点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,该字段不填则模板无跳转。
			$dd['form_id'] = $merchant['formid'];
			$dd['data'] = $temvalue;                        //模板内容，不填则下发空模板
			$dd['color'] = '';                        //模板内容字体的颜色，不填默认黑色
			$dd['emphasis_keyword'] = '';    //模板需要放大的关键词，不填则默认无放大
			$result = $this->https_curl_json($url,$dd,'json');
		}
	}
	message('审核团长成功！', $this->createWebUrl('merchant', array('op' => 'display')), 'success');
	include $this->template('web/merchant');
}elseif ($operation == 'tuandui') {
	$keyword = trim($_GPC['keyword']);
	$condition = "weid = {$_W['uniacid']} AND istz = 1 ";
	if(!empty($keyword)){
		$condition .= "AND (name like '%{$keyword}%' OR realname like '%{$keyword}%' OR telphone like '%{$keyword}%') ";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$total=pdo_fetchcolumn("SELECT count(id) FROM ".tablename(BEST_MERCHANT)." WHERE ".$condition);
	$merchant = pdo_fetchall("SELECT * FROM " . tablename(BEST_MERCHANT) . " WHERE ".$condition." ORDER BY tztime DESC LIMIT ".($pindex - 1) * $psize.",{$psize}");
	foreach($merchant as $k=>$v){
		$xiaji = pdo_fetchall("SELECT * FROM " . tablename(BEST_MERCHANT) . " WHERE weid = {$_W['uniacid']} AND fopenid = '{$v['openid']}' ORDER BY addtime DESC");
		foreach($xiaji as $kk=>$vv){
			$xiaji[$kk]['member'] = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$vv['openid']}'");
		}
		$merchant[$k]['xiaji'] = $xiaji;
		$merchant[$k]['xiajinum'] = count($xiaji);
	}
	$pager = pagination($total, $pindex, $psize);
	include $this->template('web/tuandui');
}elseif ($operation == 'delete') {
	$id = intval($_GPC['id']);
	$merchant = pdo_fetch("SELECT id,openid FROM " . tablename(BEST_MERCHANT) . " WHERE id = {$id} AND status = 1");
	if (empty($merchant)) {
		message('抱歉，该团长不存在！', $this->createWebUrl('merchant', array('op' => 'display')), 'error');
	}
	pdo_update(BEST_MERCHANT,array('status'=>0), array('id' => $id));
	message('禁用团长成功！', $this->createWebUrl('merchant', array('op' => 'display')), 'success');
}elseif ($operation == 'yichuxqz') {
	$id = intval($_GPC['id']);
	$merchant = pdo_fetch("SELECT id,openid FROM " . tablename(BEST_MERCHANT) . " WHERE id = {$id}");
	if (empty($merchant)) {
		message('抱歉，该团长不存在！', $this->createWebUrl('merchant', array('op' => 'display')), 'error');
	}
	pdo_update(BEST_MERCHANT,array('xqz'=>0), array('id' => $id));
	pdo_update(BEST_XIAOQU,array('zzopenid'=>''), array('zzopenid' => $merchant['openid']));
	message('移除小区团长身份成功！', $this->createWebUrl('merchant', array('op' => 'display')), 'success');
}elseif ($operation == 'edit') {
	$id = intval($_GPC['id']);
	$merchant = pdo_fetch("SELECT * FROM " . tablename(BEST_MERCHANT) . " WHERE id = {$id}");
	if (empty($merchant)) {
		message('抱歉，该商户不存在！', $this->createWebUrl('merchant', array('op' => 'display')), 'error');
	}
	$dododo = intval($_GPC['dododo']);
	if($dododo == 1){
		$data['name'] = trim($_GPC['name']);
		$data['telphone'] = trim($_GPC['telphone']);
		$data['address'] = trim($_GPC['address']);
		$data['openid'] = trim($_GPC['openid']);
		$data['fopenid'] = trim($_GPC['fopenid']);
		$data['avatar'] = trim($_GPC['avatar']);
		$data['realname'] = trim($_GPC['realname']);
		$data['idcard'] = trim($_GPC['idcard']);
		$data['status'] = intval($_GPC['status']);
		$data['usetx'] = intval($_GPC['usetx']);
		$data['istz'] = intval($_GPC['istz']);
		$data['txdisaccount'] = $_GPC['txdisaccount'];
		if($merchant['tztime'] <= 0){
			$data['tztime'] = TIMESTAMP;
		}
		pdo_update(BEST_MERCHANT,$data, array('id' => $id));
		message('编辑团长资料成功！', $this->createWebUrl('merchant', array('op' => 'display')), 'success');
	}else{
		include $this->template('web/editmerchant');
	}
}elseif($operation == 'deletedu') {
	$id = intval($_GPC['id']);
	$money = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANTACCOUNT)." WHERE id = {$id}");
	if (empty($money)) {
		$resarr['error'] = 1;
		$resarr['msg'] = '不存在该记录！';
		echo json_encode($resarr);
		exit();
	}
	pdo_delete(BEST_MERCHANTACCOUNT,array('id'=>$id));
	$resarr['error'] = 0;
	$resarr['msg'] = '删除成功！';
	echo json_encode($resarr);
	exit();
}elseif($operation == 'deletetd') {
	$id = intval($_GPC['id']);
	$tuanyuan = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE id = {$id}");
	if (empty($tuanyuan)) {
		$resarr['error'] = 1;
		$resarr['msg'] = '不存在该记录！';
		echo json_encode($resarr);
		exit();
	}
	pdo_update(BEST_MERCHANT,array('fopenid'=>''),array('id'=>$id));
	$resarr['error'] = 0;
	$resarr['msg'] = '解除关系成功！';
	echo json_encode($resarr);
	exit();
}elseif($operation == 'account') {
	$id = intval($_GPC['id']);
	$merchant = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE id = {$id} AND weid = {$_W['uniacid']}");
	$allmoney = pdo_fetchcolumn("SELECT SUM(money) FROM ".tablename(BEST_MERCHANTACCOUNT)." WHERE merchant_id = '{$merchant['id']}'");
	$allmoney = empty($allmoney) ? 0 : $allmoney;
	include $this->template('web/merchant');
}elseif($operation == 'doaccount') {
	$money = $_GPC['money'];
	if(empty($money)){
		message('请填写金额！');
	}
	$data = array(
		'weid'=>$_W['uniacid'],
		'merchant_id'=>$_GPC['merchant_id'],
		'money'=>$money,
		'explain'=>$_GPC['explain'],
		'time'=>TIMESTAMP,
	);
	pdo_insert(BEST_MERCHANTACCOUNT,$data);
	message('操作成功！', '', 'success');
}elseif($operation == 'addztd'){
	$id = intval($_GPC['id']);
	$merchant = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE id = {$id} AND weid = {$_W['uniacid']}");
	include $this->template('web/merchant');
}elseif($operation == 'doaddztd'){
	if (checksubmit('submit')) {
		$address = $_GPC['address'];
		if(empty($address)){
			message("请填写自提点地址！");
		}
		$zbs = $_GPC['zbs'];
		if(empty($zbs)){
			message("请填写自提点坐标！");
		}
		$zbs = explode(",",$zbs);
		$data = array(
			'address' => $address,
			'weid'=>$_W['uniacid'],
			'openid' => $_GPC['openid'],
			'ztdtype'=>1,
			'jingdu' => $zbs[1],
			'weidu' => $zbs[0],
		);
		pdo_insert(BEST_ZITIDIAN, $data);
		message('操作成功！', $this->createWebUrl('merchant', array('op' => 'display')), 'success');
	}else{
		message("参数错误！");
	}
}
?>