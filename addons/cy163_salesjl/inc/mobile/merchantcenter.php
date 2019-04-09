<?php
global $_W, $_GPC;
$member = $this->Mcheckmember();
$merchant = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} AND openid = '{$member['openid']}' AND openid != ''");
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
	if(empty($merchant) || $merchant['status'] == 0){
		if($this->module['config']['agentreg'] == 0){
			$message = "团长申请通道已关闭！";
			include $this->template('error');
			exit;
		}
		
		/*if($this->module['config']['agentreg'] == 2){
			if($member['dlopenid'] == ''){
				$message = "你的注册链接不合法！";
				include $this->template('error');
				exit;
			}else{
				$dlmerchant = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} AND openid = '{$member['dlopenid']}' AND openid != '' AND istz = 1");
				if(empty($dlmerchant)){
					$message = "你的推荐人已被取消团长身份！";
					include $this->template('error');
					exit;
				}
			}
		}*/
		/*if($this->module['config']['istplon'] == 1){
		$or_paysuccess_redirect = $_W["siteroot"].'app/'.str_replace("./","",$this->createMobileUrl("merchantteam",array("op"=>"xiaji")));
		$postdata = array(
			'first' => array(
				'value' => "您好，您的团队有新的成员加入。",
				'color' => '#ff510'
			),
			'keyword1' => array(
				'value' => $merchant['name'],
				'color' => '#ff510'
			),
			'keyword2' => array(
				'value' => date("Y-m-d H:i:s",TIMESTAMP),
				'color' => '#ff510'
			),
			'remark' => array(
				'value' => "点击查看详情",
				'color' => '#ff510'
			),	
		);
		$account_api = WeAccount::create();
		$account_api->sendTplNotice($fopenid,$this->module['config']['rutuan_tz'],$postdata,$or_paysuccess_redirect,'#980000');
	}*/
		$yqcode = trim($_GPC['yqcode']);
		$sqtext = $this->module['config']['tzmoney'] > 0 ? '支付'.$this->module['config']['tzmoney'].'元开通' : '免费开通';
		include $this->template('merchantregister');
	}else{
		$total1 = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND merchant_id = {$merchant['id']} AND status = 0");
		$total2 = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND merchant_id = {$merchant['id']} AND status = 1");
		$total3 = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND merchant_id = {$merchant['id']} AND status = 2");
		include $this->template('merchantcenter');
	}
}elseif ($operation == 'register') {
	if(!checksubmit('submit')){
		exit;
	}
	$has = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANT)." WHERE openid = '{$member['openid']}' AND weid = {$_W['uniacid']}");
	if (!empty($has)) {
		$resArr['error'] = 1;
		$resArr['msg'] = '您已提交申请，请勿重复操作！';
		echo json_encode($resArr);
		exit();
	}

	if (empty($_GPC['name'])) {
		$resArr['error'] = 1;
		$resArr['msg'] = '请填写团长名称！';
		echo json_encode($resArr);
		exit();
	}
	if (empty($_GPC['address'])) {
		$resArr['error'] = 1;
		$resArr['msg'] = '请填写团长地址！';
		echo json_encode($resArr);
		exit();
	}
	if (empty($_GPC['realname'])) {
		$resArr['error'] = 1;
		$resArr['msg'] = '请填写真实姓名！';
		echo json_encode($resArr);
		exit();
	}
	if(!$this->isMobile($_GPC['telphone'])){
		$resArr['error'] = 1;
		$resArr['msg'] = '请填写正确的手机号码！';
		echo json_encode($resArr);
		exit();
	}
	if($_GPC['xqid'] > 0){
		$hassq = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANT)." WHERE sqxqid = {$_GPC['xqid']} AND weid = {$_W['uniacid']}");
		if (!empty($hassq)) {
			$resArr['error'] = 1;
			$resArr['msg'] = '该小区已被别的团长申请啦，请再看看其他小区吧！';
			echo json_encode($resArr);
			exit();
		}
	}

	$data = array(
		'weid' => $_W['uniacid'],
		'name' => trim($_GPC['name']),
		'address' => trim($_GPC['address']),
		'realname' => trim($_GPC['realname']),
		'avatar' => $member['avatar'],
		'telphone' => trim($_GPC['telphone']),
		'addtime'=>TIMESTAMP,
		'openid'=>$member['openid'],
		'sqxqid'=>$_GPC['xqid'],
	);
	$yqcode = trim($_GPC['yqcode']);
	$hasyqmer = pdo_fetch("SELECT openid FROM ".tablename(BEST_MERCHANT)." WHERE yqcode = '{$yqcode}' AND yqcode != '' AND weid = {$_W['uniacid']}");
	if (!empty($hasyqmer)) {
		$data['yqfcode'] = $yqcode;
		$data['tzfopenid'] = $data['fopenid'] = $hasyqmer['openid'];
		$data['tzyongjin'] = $this->module['config']['tzyongjin'];
	}
	if($this->module['config']['tzmoney'] > 0){
		$data['tzprice'] = $this->module['config']['tzmoney'];
		$data['ordersn'] = date('Ymd').random(10,1);
	}else{
		if(!empty($hasyqmer)){
			$data2['istz'] = 1;
			if($hasyqmer['tztime'] == 0){
				$data2['tztime'] = TIMESTAMP;
			}
			pdo_update(BEST_MERCHANT,$data2,array('id'=>$hasyqmer['id']));
		}
	}
	pdo_insert(BEST_MERCHANT,$data);
	
	$resArr['price'] = $data['tzprice'];
	$resArr['ordersn'] = $data['ordersn'];
	$resArr['error'] = 0;
	$resArr['msg'] = '申请开通资料提交成功，请等待管理员审核！';
	echo json_encode($resArr);
	exit();
}elseif ($operation == 'searchxqsq') {
	$keyword = trim($_GPC['keyword']);
	if(empty($keyword)){
		$resArr['error'] = 1;
		$resArr['message'] = '请输入小区名称搜索哦~';
		echo json_encode($resArr);
		exit();
	}
	$xiaoqu = pdo_fetchall("SELECT * FROM ".tablename(BEST_XIAOQU)." WHERE weid = {$_W['uniacid']} AND zzopenid = '' AND name like '%{$keyword}%'");
	if(empty($xiaoqu)){
		$resArr['error'] = 1;
		$resArr['nodata'] = 1;
		$resArr['message'] = '没有搜索到小区哦~';
		echo json_encode($resArr);
		exit();
	}
	$html = '<div class="xqdiv">';
	foreach($xiaoqu as $k=>$v){
		$html .= '<div class="xqlist flex" data-id="'.$v['id'].'" data-name="'.$v['name'].'">
					<div class="right">
					  <div class="name">'.$v['name'].'</div>
					  <div class="address">'.$v['address'].'</div>
					</div>
					<img src="'.STATIC_ROOT.'/right.png" class="to" />
				</div>';
	}
	$html .= '</div>';
	$resArr['error'] = 0;
	$resArr['html'] = $html;
	echo json_encode($resArr);
	exit();
}
?>