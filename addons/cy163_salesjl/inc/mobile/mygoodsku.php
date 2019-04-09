<?php
global $_W,$_GPC;
$member = $this->Mcheckmember();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if($operation == 'display'){	
	$goodslist = pdo_fetchall("SELECT * FROM ".tablename(BEST_MEMBERGOODSKU)." WHERE weid = {$_W['uniacid']} AND openid = '{$member['openid']}'");
	foreach($goodslist as $k=>$v){
		$thumbs = unserialize($v['thumbs']);
		$goodslist[$k]['thumb'] = tomedia($thumbs[0]);
	}
	include $this->template('mygoodsku');
}elseif($operation == 'post'){
	$id = intval($_GPC['id']);
	$goods = pdo_fetch("SELECT * FROM " . tablename(BEST_MEMBERGOODSKU) . " WHERE weid = {$_W['uniacid']} AND id = {$id}");
	$thumbs = unserialize($goods['thumbs']);
	include $this->template('mygoodsku');
}elseif($operation == 'doedit'){
	$id = intval($_GPC['id']);
	if(empty($member)){
		$resArr['error'] = 1;
		$resArr['message'] = "获取身份信息失败";
		echo json_encode($resArr);
		exit;
	}
	$title = trim($_GPC['title']);
	if(empty($title)){
		$resArr['error'] = 1;
		$resArr['message'] = "请填写商品名称";
		echo json_encode($resArr);
		exit;
	}
	$optionname = trim($_GPC['optionname']);
	if(empty($optionname)){
		$resArr['error'] = 1;
		$resArr['message'] = "请填写商品规格";
		echo json_encode($resArr);
		exit;
	}
	$normalprice = $_GPC['normalprice'];
	if($normalprice <= 0){
		$resArr['error'] = 1;
		$resArr['message'] = "请填写商品价格";
		echo json_encode($resArr);
		exit;
	}
	$thumbs = $_GPC['thumbs'];
	if(empty($thumbs)){
		$resArr['error'] = 1;
		$resArr['message'] = "请上传商品图片！";
		echo json_encode($resArr);
		exit;
	}
	$data['thumbs'] = serialize($thumbs);
	$data['title'] = $title;
	$data['optionname'] = $optionname;
	$data['normalprice'] = $normalprice;
	if($id <= 0){
		$data['createtime'] = TIMESTAMP;
		$data['weid'] = $_W['uniacid'];
		$data['openid'] = $member['openid'];
		pdo_insert(BEST_MEMBERGOODSKU,$data);
	}else{
		pdo_update(BEST_MEMBERGOODSKU,$data,array('id'=>$id));
	}
	$resArr['error'] = 0;
	$resArr['message'] = "操作成功！";
	echo json_encode($resArr);
	exit;
}elseif($operation == 'delete'){
	$id = intval($_GPC['id']);
	$row = pdo_fetch("SELECT id FROM " . tablename(BEST_MEMBERGOODSKU) . " WHERE openid = '{$member['openid']}' AND weid = {$_W['uniacid']} AND id = {$id}");
	if (empty($row)) {
		$resArr['error'] = 1;
		$resArr['message'] = "抱歉，不存在该商品！";
		echo json_encode($resArr);
		exit;
	}
	pdo_delete(BEST_MEMBERGOODSKU, array('id' => $id));
	$resArr['error'] = 0;
	$resArr['message'] = "删除商品成功！";
	echo json_encode($resArr);
	exit;
}
?>