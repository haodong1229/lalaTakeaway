<?php
global $_W,$_GPC;
$operation = empty($_GPC['op']) ? 'display' : $_GPC['op'];
if($operation == 'display'){
	$yunfeilist = pdo_fetchall("SELECT * FROM ".tablename(BEST_YUNFEI)." WHERE weid = {$_W['uniacid']} AND openid = ''");
	foreach($yunfeilist as $k=>$v){
		$yunfeilist[$k]['count'] = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_YUNFEISHENG)." WHERE yfid = {$v['id']}");
		$yunfeilist[$k]['yfsheng'] = pdo_fetchall("SELECT * FROM ".tablename(BEST_YUNFEISHENG)." WHERE yfid = {$v['id']}");
	}
	include $this->template('web/yunfei');
}elseif ($operation == 'post') {
	$id = intval($_GPC['id']);
	if (!empty($id)) {
		$yunfei = pdo_fetch("SELECT * FROM ".tablename(BEST_YUNFEI)." WHERE id = {$id}");
		if (empty($yunfei)) {
			message('抱歉，运费不存在或是已经删除！', '', 'error');
		}
	}
	if (checksubmit('submit')) {
		if (empty($_GPC['title'])) {
			message('请输入运费名称！');
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
	include $this->template('web/yunfei');
}elseif ($operation == 'delete') {
	$id = intval($_GPC['id']);
	$yunfei = pdo_fetch("SELECT * FROM ".tablename(BEST_YUNFEI)." WHERE id = {$id}");
	if (empty($yunfei)) {
		message('抱歉，该运费不存在或是已经删除！', '', 'error');
	}
	pdo_delete(BEST_YUNFEI,array('id'=>$id));
	pdo_delete(BEST_YUNFEISHENG,array('yfid'=>$id));
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
	include $this->template('web/yunfeiaddquyu');
}elseif ($operation == 'deletediqu') {
	$id = intval($_GPC['id']);
	$yunfeisheng = pdo_fetch("SELECT * FROM ".tablename(BEST_YUNFEISHENG)." WHERE id = {$id}");
	if (empty($yunfeisheng)) {
		message('抱歉，该运费读取信息不存在或是已经删除！', '', 'error');
	}
	pdo_delete(BEST_YUNFEISHENG,array('id'=>$id));
	message('删除成功！', $this->createWebUrl('yunfei', array('op' => 'display', 'id' => $id)), 'success');
}elseif($operation == 'tongbu'){
	if(pdo_tableexists('cy163salesjl_city')) {
		pdo_query('TRUNCATE TABLE '.tablename('cy163salesjl_city'));
	}
	$conyc = $this->checkmain();
	foreach($conyc['con1'] as $kk=>$vv){
		$data['name'] = $vv['name'];
		$data['code'] = $vv['code'];
		$data['fcode'] = $vv['fcode'];
		$data['firstz'] = $vv['firstz'];
		$data['type'] = 1;
		pdo_insert(BEST_CITY,$data);
	}
	foreach($conyc['con2'] as $kk=>$vv){
		$data['name'] = $vv['name'];
		$data['code'] = $vv['code'];
		$data['fcode'] = $vv['fcode'];
		$data['firstz'] = $vv['firstz'];
		$data['type'] = 2;
		pdo_insert(BEST_CITY,$data);
	}
	foreach($conyc['con3'] as $kk=>$vv){
		$data['name'] = $vv['name'];
		$data['code'] = $vv['code'];
		$data['fcode'] = $vv['fcode'];
		$data['firstz'] = $vv['firstz'];
		$data['type'] = 3;
		pdo_insert(BEST_CITY,$data);
	}
	message('同步数据成功！', $this->createWebUrl('yunfei', array('op' => 'display')), 'success');
}
?>