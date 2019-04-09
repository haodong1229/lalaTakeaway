<?php
global $_W,$_GPC;
$openid = trim($_GPC['openid']);

$datahd['weid'] = $_W['uniacid'];
$datahd['time'] = TIMESTAMP;
$datahd['openid'] = $openid;
$datahd['nickname'] = trim($_GPC['nickname']);
$datahd['avatar'] = trim($_GPC['avatar']);
$datahd['title'] = $_GPC['title'];
$datahd['canziti'] = intval($_GPC['canziti']);
$datahd['cansh'] = intval($_GPC['cansh']);

if(empty($datahd['title'])){
	$resArr['error'] = 1;
	$resArr['message'] = "请填写接龙主题名称！";
	$this->result(0,"发布", $resArr);
	exit;
}
$datahd['des'] = $_GPC['des'];
if(empty($datahd['des'])){
	$resArr['error'] = 1;
	$resArr['message'] = "请填写接龙主题介绍！";
	$this->result(0,"发布", $resArr);
	exit;
}
$thumbs = $this->messistr2array($_GPC['thumbs']);
if(empty($thumbs)){
	$resArr['error'] = 1;
	$resArr['message'] = "请上传接龙主题图片！";
	$this->result(0,"发布", $resArr);
	exit;
}
$thumbss = array();
foreach($thumbs as $k=>$v){
	$thumbss[] = $v['realpath'];
}
$datahd['thumbs'] = serialize($thumbss);
$datahd['starttime'] = strtotime($_GPC['starttime']);
$datahd['endtime'] = strtotime($_GPC['endtime']);
$datahd['telphone'] = $_GPC['telphone'];
$datahd['yunfei'] = $_GPC['yunfei'];
$datahd['manjian'] = $_GPC['manjian'];
if(empty($datahd['starttime']) || empty($datahd['endtime'])){	
	$resArr['error'] = 1;
	$resArr['message'] = "请选择接龙主题开始时间和截止时间！";
	$this->result(0,"发布", $resArr);
	exit;
}
if($datahd['starttime'] > $datahd['endtime']){
	$resArr['error'] = 1;
	$resArr['message'] = "接龙主题开始时间不能大于截止时间！";
	$this->result(0,"发布", $resArr);
	exit;
}
if(empty($datahd['telphone'])){
	$resArr['error'] = 1;
	$resArr['message'] = "请填写联系方式！";
	$this->result(0,"发布", $resArr);
	exit;
}
if($datahd['canziti'] == 0 && $datahd['cansh'] == 0){	
	$resArr['error'] = 1;
	$resArr['message'] = "自提与送货必须选择至少选择其中一项！";
	$this->result(0,"发布", $resArr);
	exit;
}


if($datahd['canziti'] == 1){
	$zitidian = pdo_fetch("SELECT id FROM ".tablename(BEST_ZITIDIAN)." WHERE openid = '{$openid}' AND weid = {$_W['uniacid']} AND ztdtype = 0");
	if(empty($zitidian)){
		$resArr['error'] = 2;
		$resArr['message'] = "您还没有创建自提点，不能选择支持自提。请至个人中心添加自提点！";
		$this->result(0,"发布", $resArr);
		exit;
	}
}

$goods = $this->messistr2array($_GPC['goods']);
foreach($goods as $k=>$v){
	if(empty($v['goodsname'])){
		$resArr['error'] = 1;
		$resArr['message'] = "请填写商品名称！";
		$this->result(0,"发布", $resArr);
		exit;
	}
	if(empty($v['imgs'])){
		$resArr['error'] = 1;
		$resArr['message'] = "请上传商品图片！";
		$this->result(0,"发布", $resArr);
		exit;
	}
	if($v['normalprice'] <= 0){
		$resArr['error'] = 1;
		$resArr['message'] = "请填写正确的商品价格！";
		$this->result(0,"发布", $resArr);
		exit;
	}	

	$lastshuliang = 0;
	$lastprice = 0;
	foreach($v['jieti'] as $kk=>$vv){
		if(empty($vv['jietiprice']) || empty($vv['jietinum'])){			
			$resArr['error'] = 1;
			$resArr['message'] = "请填写完整的阶梯价格和阶梯数量！";
			$this->result(0,"发布", $resArr);
			exit;
		}
		if($vv['jietinumstart'] != 0 && $kk == 0){
			$resArr['error'] = 1;
			$resArr['message'] = "第一个阶梯起始数量应该为0！";
			$this->result(0,"发布", $resArr);
			exit;
		}
		if($vv['jietinumstart'] <= $lastshuliang){
			if($lastshuliang != 0){
				$resArr['error'] = 1;
				$resArr['message'] = "阶梯起始数量不能小于上一个阶梯的结束数量！";
				$this->result(0,"发布", $resArr);
				exit;
			}
		}
		if($vv['jietiprice'] >= $lastprice && $lastshuliang != 0){
			$resArr['error'] = 1;
			$resArr['message'] = "阶梯价格不能大于等于上一个阶梯的价格！";
			$this->result(0,"发布", $resArr);
			exit;
		}
		$lastshuliang = $vv['jietinum'];
		$lastprice = $vv['jietiprice'];
	}
	$total = $v['total'];
	if(empty($total)){
		$resArr['error'] = 1;
		$resArr['message'] = "请填写商品库存！";
		$this->result(0,"发布", $resArr);
		exit;
	}
}
if($this->module['config']['iszfjlsh'] == 0){
	$datahd['status'] = 1;
}
$datahd['address'] = $_GPC['address'];
$datahd['jingdu'] = $_GPC['jingdu'];
$datahd['weidu'] = $_GPC['weidu'];
$datahd['xingyun'] = $_GPC['xingyun'];
pdo_insert(BEST_MEMBERHUODONG,$datahd);
$hdid = pdo_insertid();
foreach($goods as $k=>$v){
	$data['weid'] = $_W['uniacid'];
	$data['openid'] = $openid;
	$data['title'] = $v['goodsname'];
	$data['normalprice'] = $v['normalprice'];
	$data['total'] = $v['total'];
	$data['optionname'] = $v['optionname'];
	$data['xiangounum'] = $v['xiangounum'];
	$data['createtime'] = TIMESTAMP;
	$data['hdid'] = $hdid;
	$gthumbs = array();
	foreach($v['imgs'] as $kk=>$vv){
		$gthumbs[] = $vv['realpath'];
	}
	$data['thumbs'] = serialize($gthumbs);
	pdo_insert(BEST_MEMBERGOODS,$data);
	$goodsid = pdo_insertid();

	foreach($v['jieti'] as $kkk=>$vvv){
		$datajt['goodsid'] = $goodsid;
		$datajt['jietiprice'] = $vvv['jietiprice'];
		$datajt['jietinumstart'] = $vvv['jietinumstart'];
		$datajt['jietinum'] = $vvv['jietinum'];
		$datajt['displayorder'] = $kkk;
		pdo_insert(BEST_MEMBERGOODSJIETI,$datajt);
	}
}
$resArr['error'] = 0;
$resArr['message'] = "发布成功！";
$this->result(0,"发布", $resArr);
exit;
?>