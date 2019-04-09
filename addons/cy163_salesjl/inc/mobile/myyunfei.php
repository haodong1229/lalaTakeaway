<?php
global $_W,$_GPC;
$member = $this->Mcheckmember();
$operation = empty($_GPC['op']) ? 'display' : $_GPC['op'];
if($operation == 'display'){
	$yunfeilist = pdo_fetchall("SELECT * FROM ".tablename(BEST_YUNFEI)." WHERE weid = {$_W['uniacid']} AND openid = '{$member['openid']}'");
	foreach($yunfeilist as $k=>$v){
		$yunfeilist[$k]['count'] = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_YUNFEISHENG)." WHERE yfid = {$v['id']}");
	}
	include $this->template('myyunfei');
}elseif ($operation == 'post') {
	$id = intval($_GPC['id']);
	if (!empty($id)) {
		$yunfei = pdo_fetch("SELECT * FROM ".tablename(BEST_YUNFEI)." WHERE id = {$id} AND openid = '{$member['openid']}'");
	}
	$provincelist = pdo_fetchall("SELECT * FROM ".tablename(BEST_CITY)." WHERE type = 1");
	if ($_GPC['isdo'] == 1) {
		if (empty($_GPC['title'])) {
			$resArr['error'] = 1;
			$resArr['msg'] = '请输入运费模板名称！';
			echo json_encode($resArr);
			exit();
		}
		$data = array(
			'weid' => intval($_W['uniacid']),
			'title' => $_GPC['title'],
		);
		if (empty($id)) {
			pdo_insert(BEST_YUNFEI, $data);
		} else {
			pdo_update(BEST_YUNFEI, $data, array('id' => $id));		
		}
		message('操作成功！', $this->createWebUrl('yunfei', array('op' => 'display', 'id' => $id)), 'success');
	}
	include $this->template('myyunfei');
}elseif ($operation == 'delete') {
	$id = intval($_GPC['id']);
	$yunfei = pdo_fetch("SELECT * FROM ".tablename(BEST_YUNFEI)." WHERE id = {$id} AND openid = '{$member['openid']}'");
	if (empty($yunfei)) {
		$resArr['error'] = 0;
		$resArr['msg'] = '抱歉，该运费不存在或是已经删除！';
		echo json_encode($resArr);
		exit();
	}
	pdo_delete(BEST_YUNFEI,$data,array('id'=>$id));
	pdo_delete(BEST_YUNFEISHENG,$data,array('yfid'=>$id));
	message('删除成功！', $this->createWebUrl('yunfei', array('op' => 'display', 'id' => $id)), 'success');
}elseif($operation == 'getcitys'){
	$fcode = trim($_GPC['fcode']);
	$citys = pdo_fetchall("SELECT name,code FROM ".tablename(BEST_CITY)." WHERE fcode = '{$fcode}' AND type = 2");
	$html = '<option value="">选择城市</option>';
	foreach($citys as $k=>$v){
		$html .= '<option value="'.$v['name'].'" data-code="'.$v['code'].'">'.$v['name'].'</option>';
	}
	echo $html;
	exit;
}elseif($operation == 'getdistricts'){
	$fcode = trim($_GPC['fcode']);
	$districts = pdo_fetchall("SELECT name FROM ".tablename(BEST_CITY)." WHERE fcode = '{$fcode}' AND type = 3");
    $html = '';
	foreach($districts as $k=>$v){
		$html .= '<option value="'.$v['name'].'">'.$v['name'].'</option>';
	}
	echo $html;
	exit;
}elseif($operation == 'addquyu'){
	$yfid = intval($_GPC['yfid']);
	$provincelist = pdo_fetchall("SELECT * FROM ".tablename(BEST_CITY)." WHERE type = 1");
	if (checksubmit('submit')) {
		$money = $_GPC['money'];
		if(empty($money)){
			$resArr['error'] = 1;
			$resArr['msg'] = '请填写运费！';
			echo json_encode($resArr);
			exit();
		}
		$diqutype = intval($_GPC['diqutype']);
		$yfid = intval($_GPC['yfid']);
		if($diqutype == 1){
		if(!empty($_GPC['name'])){
				foreach($_GPC['name'] as $k=>$v){
					$datam['weid'] = $_W['uniacid'];
					$datam['name'] = $v;
					$datam['city'] = "";
					$datam['xian'] = "";
					$datam['yfid'] = $yfid;
					$datam['money'] = $money;
					$datam['diqutype'] = $diqutype;
					pdo_insert(BEST_YUNFEISHENG,$datam); 
				}
			}
		}
		if($diqutype == 2){
			if(!empty($_GPC['city']) && !empty($_GPC['name'])){
				foreach($_GPC['city'] as $k=>$v){
					$datam['weid'] = $_W['uniacid'];
					$datam['name'] = $_GPC['name'][0];
					$datam['city'] = $v;
					$datam['xian'] = "";
					$datam['yfid'] = $yfid;
					$datam['money'] = $money;
					$datam['diqutype'] = $diqutype;
					pdo_insert(BEST_YUNFEISHENG,$datam); 
				}
			}
		}
		if($diqutype == 3){
			if(!empty($_GPC['xian']) && !empty($_GPC['name']) && !empty($_GPC['city'])){
				foreach($_GPC['xian'] as $k=>$v){
					$datam['weid'] = $_W['uniacid'];
					$datam['name'] = $_GPC['name'][0];
					$datam['city'] = $_GPC['city'][0];
					$datam['xian'] = $v;
					$datam['yfid'] = $yfid;
					$datam['money'] = $money;
					$datam['diqutype'] = $diqutype;
					pdo_insert(BEST_YUNFEISHENG,$datam); 
				}
			}
		}
		message('操作成功！', $this->createWebUrl('yunfei', array('op' => 'display', 'id' => $id)), 'success');
	}
	include $this->template('myyunfeiaddquyu');
}elseif ($operation == 'deletediqu') {
	$id = intval($_GPC['id']);
	$yunfeisheng = pdo_fetch("SELECT * FROM ".tablename(BEST_YUNFEISHENG)." WHERE id = {$id}");
	if (empty($yunfeisheng)) {
		message('抱歉，该运费读取信息不存在或是已经删除！', '', 'error');
	}
	pdo_delete(BEST_YUNFEISHENG,array('id'=>$id));
	$resArr['error'] = 0;
	$resArr['msg'] = '删除成功！';
	echo json_encode($resArr);
	exit();
}
?>