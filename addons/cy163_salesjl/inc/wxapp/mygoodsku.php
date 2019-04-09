<?php
global $_W,$_GPC;
$openid = trim($_GPC['openid']);
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if($operation == 'display'){	
	$goodslist = pdo_fetchall("SELECT * FROM ".tablename(BEST_MEMBERGOODSKU)." WHERE weid = {$_W['uniacid']} AND openid = '{$openid}'");
	foreach($goodslist as $k=>$v){
		$thumbs = unserialize($v['thumbs']);
		$thumbsarr = array();
		foreach($thumbs as $kk=>$vv){
			$thumbsarr[$kk]['path'] = tomedia($vv);
			$thumbsarr[$kk]['realpath'] = $vv;
		}
		$goodslist[$k]['thumbs'] = $thumbsarr;
		$goodslist[$k]['thumb'] = tomedia($thumbs[0]);
	}
	$data['goodslist'] = $goodslist;
	$this->result(0,"商品库", $data);
}elseif($operation == 'doedit'){
	$id = intval($_GPC['id']);
	$openid = trim($_GPC['openid']);
	if(empty($openid)){
		$resArr['error'] = 1;
		$resArr['message'] = "获取身份信息失败";
		$this->result(0,"商品库", $resArr);
		exit;
	}
	$title = trim($_GPC['title']);
	if(empty($title)){
		$resArr['error'] = 1;
		$resArr['message'] = "请填写商品名称";
		$this->result(0,"商品库", $resArr);
		exit;
	}
	$optionname = trim($_GPC['optionname']);
	if(empty($optionname)){
		$resArr['error'] = 1;
		$resArr['message'] = "请填写商品规格";
		$this->result(0,"商品库", $resArr);
		exit;
	}
	$normalprice = $_GPC['normalprice'];
	if($normalprice <= 0){
		$resArr['error'] = 1;
		$resArr['message'] = "请填写商品价格";
		$this->result(0,"商品库", $resArr);
		exit;
	}
	$thumbs = $this->messistr2array($_GPC['thumbs']);
	if(empty($thumbs)){
		$resArr['error'] = 1;
		$resArr['message'] = "请上传商品图片！";
		$this->result(0,"发布", $resArr);
		exit;
	}
	$thumbss = array();
	foreach($thumbs as $k=>$v){
		$thumbss[] = $v['realpath'];
	}
	$data['thumbs'] = serialize($thumbss);
	$data['title'] = $title;
	$data['optionname'] = $optionname;
	$data['normalprice'] = $normalprice;
	if($id <= 0){
		$data['createtime'] = TIMESTAMP;
		$data['weid'] = $_W['uniacid'];
		$data['openid'] = $openid;
		pdo_insert(BEST_MEMBERGOODSKU,$data);
	}else{
		pdo_update(BEST_MEMBERGOODSKU,$data,array('id'=>$id));
	}
	$resArr['error'] = 0;
	$resArr['message'] = "操作成功！";
	$this->result(0,"商品库", $resArr);
}elseif($operation == 'delete'){
	$id = intval($_GPC['id']);
	$row = pdo_fetch("SELECT id FROM " . tablename(BEST_MEMBERGOODSKU) . " WHERE openid = '{$openid}' AND weid = {$_W['uniacid']} AND id = {$id}");
	if (empty($row)) {
		$resArr['error'] = 1;
		$resArr['message'] = "抱歉，不存在该商品！";
		$this->result(0,"商品库", $resArr);
		exit;
	}
	pdo_delete(BEST_MEMBERGOODSKU, array('id' => $id));
	$resArr['error'] = 0;
	$resArr['message'] = "删除商品成功！";
	$this->result(0,"商品库", $resArr);
}elseif($operation == 'detail'){
	$id = intval($_GPC['id']);
	$goods = pdo_fetch("SELECT * FROM " . tablename(BEST_MEMBERGOODSKU) . " WHERE weid = {$_W['uniacid']} AND id = {$id}");
	$thumbs = unserialize($goods['thumbs']);
	$thumbsarr = array();
	foreach($thumbs as $k=>$v){
		$thumbsarr[$k]['path'] = tomedia($v);
		$thumbsarr[$k]['realpath'] = $v;
	}
	$resArr['thumbs'] = $thumbsarr;
	$resArr['goods'] = $goods;
	$this->result(0,"商品库", $resArr);
}
?>