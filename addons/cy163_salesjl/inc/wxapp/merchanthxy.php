<?php
global $_W,$_GPC;
$openid = trim($_GPC['openid']);
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if($operation == 'display'){	
	$merchant = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE openid = '{$openid}' AND weid = {$_W['uniacid']} AND status = 1");
	$hxys = pdo_fetchall("SELECT * FROM ".tablename(BEST_HEXIAOYUAN)." WHERE weid = {$_W['uniacid']} AND merchant_id = {$merchant['id']}");
	$this->result(0,"核销员管理", $hxys);
}elseif($operation == 'edit'){
	$id = intval($_GPC['id']);
	$merchant = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE openid = '{$openid}' AND weid = {$_W['uniacid']} AND status = 1");
	$hxy = pdo_fetch("SELECT * FROM ".tablename(BEST_HEXIAOYUAN)." WHERE id = {$id} AND merchant_id = {$merchant['id']}");
	$this->result(0,"核销员详情", $hxy);
}elseif($operation == 'doedit'){
	$id = intval($_GPC['id']);
	$merchant = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE openid = '{$openid}' AND weid = {$_W['uniacid']} AND status = 1");
	$name = trim($_GPC['name']);
	if(empty($name)){
		$this->result(1,"请填写核销员名称！", '');
	}
	$openidopenid = trim($_GPC['openidopenid']);
	if(empty($openidopenid)){
		$this->result(1,"请填写openid！", '');
	}
	$data = array(
		'weid'=>$_W['uniacid'],
		'name'=>$name,
		'openid'=>$openidopenid,
		'merchant_id'=>$merchant['id'],
	);
	if(!empty($id)){
		pdo_update(BEST_HEXIAOYUAN,$data,array('id'=>$id));
	}else{
		pdo_insert(BEST_HEXIAOYUAN,$data);
	}
	$this->result(0,"操作成功！", '');
}elseif($operation == 'delete'){	
	$id = intval($_GPC['id']);
	$merchant = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE openid = '{$openid}' AND status = 1 AND weid = {$_W['uniacid']}");
	$hxy = pdo_fetch("SELECT * FROM ".tablename(BEST_HEXIAOYUAN)." WHERE id = {$id} AND weid = {$_W['uniacid']} AND merchant_id = {$merchant['id']}");
	if(empty($hxy)){
		$this->result(1,"抱歉，不存在该核销员！", '');
	}
	pdo_delete(BEST_HEXIAOYUAN,array('id'=>$id));
	$this->result(0,"核销员删除成功！", '');
}elseif($operation == 'getqrcode'){
	$account_api = WeAccount::create();
	$access_token = $account_api->getAccessToken();
	$url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=".$access_token;
	$data = array(
		"scene"=>$_GPC['scene'],
		"page"=>"cy163_salesjl/pages/mersmhxy/mersmhxy",
		"width"=>430,
	);
	$data = json_encode($data);
	$response = $this->send_post($url,$data);
	$result=$this->data_uri($response,'image/png');
	$this->result(0,'核销码！', $result);
}elseif($operation == 'saoma'){
	$member = pdo_fetch("SELECT nickname FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$openid}' AND weid = {$_W['uniacid']}");
	if (empty($member)) {
		$this->result(0,"参数错误！", '未获取到您的信息！');
	}
	$mopenid = trim($_GPC['mopenid']);
	$merchant = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANT)." WHERE openid = '{$mopenid}' AND status = 1 AND weid = {$_W['uniacid']}");
	if (empty($merchant)) {
		$this->result(0,"参数错误！", '未获取到您的信息！');
	}
	$merchant_id = $merchant['id'];
	if (empty($merchant_id)) {
		$this->result(0,"参数错误！", '参数错误！');
	}
	$has = pdo_fetch("SELECT id FROM ".tablename(BEST_HEXIAOYUAN)." WHERE merchant_id = {$merchant_id} AND openid = '{$openid}' AND weid = {$_W['uniacid']}");
	if (!empty($has)) {
		$this->result(0,"你已绑定成为核销员！", '你已绑定成为核销员！');
	}
	$data = array(			
		'weid' => intval($_W['uniacid']),
		'name' => $member['nickname'],
		'openid' => $openid,
		'merchant_id'=>$merchant_id,
	);
	pdo_insert(BEST_HEXIAOYUAN, $data);
	$this->result(0,"绑定成为核销员成功！", 1);
}
?>