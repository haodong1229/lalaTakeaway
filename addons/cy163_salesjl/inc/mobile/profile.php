<?php
global $_W,$_GPC;
$member = $this->Mcheckmember();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if($operation == 'update'){
	$data['shname'] = trim($_GPC['shname']);
	if($data['shname'] == ''){
		$resarr['error'] = 1;
		$resarr['message'] = '请填写收货人姓名！';
		echo json_encode($resarr);
		exit();
	}
	$data['shphone'] = trim($_GPC['shphone']);
	if(!$this->isMobile($data['shphone'])){
		$resArr['error'] = 1;
		$resArr['message'] = "请填写正确的收货人手机号码！";
		echo json_encode($resArr);
		exit;
	}
	$shcity = trim($_GPC['shcity']);
	$shaddress = trim($_GPC['shaddress']);
	if(empty($shcity)){
		$resArr['error'] = 1;
		$resArr['message'] = "请选择地区！";
		echo json_encode($resArr);
		exit;
	}
	if(empty($shaddress)){
		$resArr['error'] = 1;
		$resArr['message'] = "请填写详细地址！";
		echo json_encode($resArr);
		exit;
	}
	$data['shcity'] = $shcity;
	$data['shaddress'] = $shaddress;
	$data['xqaddress'] = $_GPC['xqaddress'];
	pdo_update(BEST_MEMBER,$data,array('openid'=>$member['openid']));
	$resarr['error'] = 0;
	$resarr['message'] = '修改个人资料成功！';
	echo json_encode($resarr);
	exit();
}
include $this->template('profile');
?>