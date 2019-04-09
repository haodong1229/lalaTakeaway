<?php
global $_W,$_GPC;
$openid = trim($_GPC['openid']);
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if($operation == 'display'){	
	$hxys = pdo_fetchall("SELECT * FROM ".tablename(BEST_HEXIAOYUAN)." WHERE weid = {$_W['uniacid']} AND fopenid = '{$openid}'");
	$this->result(0,"核销员管理", $hxys);
}elseif($operation == 'edit'){
	$id = intval($_GPC['id']);
	$item = pdo_fetch("SELECT * FROM " . tablename(BEST_HEXIAOYUAN) . " WHERE fopenid = '{$openid}' AND id = {$id}");
	$this->result(0,"核销员详情", $item);
}elseif($operation == 'doedit'){
	$id = intval($_GPC['id']);

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
		'fopenid'=>$openid,
	);
	if(!empty($id)){
		pdo_update(BEST_HEXIAOYUAN,$data,array('id'=>$id));
	}else{
		pdo_insert(BEST_HEXIAOYUAN,$data);
	}
	$this->result(0,"操作成功！", '');
}elseif($operation == 'delete'){
	$id = intval($_GPC['id']);
	$row = pdo_fetch("SELECT id FROM " . tablename(BEST_HEXIAOYUAN) . " WHERE fopenid = '{$openid}' AND weid = {$_W['uniacid']} AND id = {$id}");
	if (empty($row)) {
		$this->result(1,"抱歉，不存在该核销员！", '');
	}
	pdo_delete(BEST_HEXIAOYUAN, array('id' => $id));
	$this->result(0,"核销员删除成功！", '');
}elseif($operation == 'getqrcode'){
	$account_api = WeAccount::create();
	$access_token = $account_api->getAccessToken();
	$url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=".$access_token;
	$data = array(
		"scene"=>$_GPC['scene'],
		"page"=>"cy163_salesjl/pages/smhxy/smhxy",
		"width"=>430,
	);
	$data = json_encode($data);
	$response = $this->send_post($url,$data);
	$result=$this->data_uri($response,'image/png');
	$this->result(0,'核销码！', $result);
}elseif($operation == 'saoma'){
	$openid = trim($_GPC['openid']);
	$member = pdo_fetch("SELECT nickname FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$openid}' AND weid = {$_W['uniacid']}");
	if (empty($member)) {
		$this->result(0,"参数错误！", '未获取到您的信息！');
	}
	$fopenid = trim($_GPC['fopenid']);
	if (empty($fopenid)) {
		$this->result(0,"参数错误！", '参数错误！');
	}
	$has = pdo_fetch("SELECT id FROM ".tablename(BEST_HEXIAOYUAN)." WHERE fopenid = '{$fopenid}' AND openid = '{$openid}' AND weid = {$_W['uniacid']}");
	if (!empty($has)) {
		$this->result(0,"你已绑定成为核销员！", '你已绑定成为核销员！');
	}
	$data = array(			
		'weid' => intval($_W['uniacid']),
		'name' => $member['nickname'],
		'openid' => $openid,
		'fopenid'=>$fopenid,
	);
	pdo_insert(BEST_HEXIAOYUAN, $data);
	$this->result(0,"绑定成为核销员成功！", 1);
}
?>