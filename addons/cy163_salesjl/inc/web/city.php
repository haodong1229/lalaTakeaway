<?php
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
	/*$erji = pdo_fetchall("SELECT * FROM ".tablename(BEST_CITY)." WHERE type = 2");
	foreach($erji as $k=>$v){
		$data['weid'] = $_W['uniacid'];
		$data['firstz'] = $v['firstz'];
		$data['name'] = $v['name'];
		$data['fid'] = 0;
		pdo_insert(BEST_QUYU,$data);
		$fid = pdo_insertid();
		$sanji = pdo_fetchall("SELECT * FROM ".tablename(BEST_CITY)." WHERE type = 3 AND fcode = '{$v['code']}'");
		foreach($sanji as $kk=>$vv){
			$data2['weid'] = $_W['uniacid'];
			$data2['firstz'] = $vv['firstz'];
			$data2['name'] = $vv['name'];
			$data2['fid'] = $fid;
			pdo_insert(BEST_QUYU,$data2);
		}
	}*/
	
	if (!empty($_GPC['displayorder'])) {
		foreach ($_GPC['displayorder'] as $id => $displayorder) {
			pdo_update(BEST_QUYU, array('displayorder' => $displayorder), array('id' => $id, 'weid' => $_W['uniacid']));
		}
		message('排序更新成功！', $this->createWebUrl('city', array('op' => 'display')), 'success');
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 5;
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_QUYU)." WHERE weid = {$_W['uniacid']} AND fid = 0");
	$list = pdo_fetchall("SELECT * FROM ".tablename(BEST_QUYU)." WHERE weid = {$_W['uniacid']} AND fid = 0 ORDER BY displayorder ASC LIMIT ".($pindex - 1)*$psize.",".$psize);
	foreach($list as $k=>$v){
		$list[$k]['erji'] = pdo_fetchall("SELECT * FROM ".tablename(BEST_QUYU)." WHERE weid = {$_W['uniacid']} AND fid = {$v['id']} ORDER BY displayorder ASC");
	}
	$pager = pagination($total, $pindex, $psize);
} elseif ($operation == 'post') {
	$id = intval($_GPC['id']);
	$fid = intval($_GPC['fid']);
	if($fid > 0){
		$citylist = pdo_fetchall("SELECT * FROM ".tablename(BEST_QUYU)." WHERE weid = {$_W['uniacid']} AND fid = 0 ORDER BY displayorder ASC");
	}
	if (checksubmit('submit')) {
		$data = array(
			'weid' => $_W['uniacid'],
			'displayorder'=> intval($_GPC['displayorder']),
			'name'=>$_GPC['name'],
			'firstz'=>$_GPC['firstz'],
			'fid'=>intval($_GPC['fid']),
		);
		if (!empty($id)) {
			pdo_update(BEST_QUYU, $data, array('id' => $id));
		} else {
			pdo_insert(BEST_QUYU, $data);
		}
		message('操作成功！', $this->createWebUrl('city', array('op' => 'display')), 'success');
	}
	$city = pdo_fetch("SELECT * FROM ".tablename(BEST_QUYU)." WHERE id = {$id} and weid= {$_W['uniacid']}");
} elseif ($operation == 'delete') {
	$id = intval($_GPC['id']);
	$city = pdo_fetch("SELECT id FROM ".tablename(BEST_QUYU)." WHERE id = {$id} AND weid = {$_W['uniacid']}");
	if (empty($city)) {
		message('抱歉，城市不存在或是已经被删除！', $this->createWebUrl('city', array('op' => 'display')), 'error');
	}
	pdo_delete(BEST_QUYU, array('id' => $id));
	if($city['fid'] == 0){
		pdo_delete(BEST_QUYU, array('fid' => $id));
	}
	message('城市删除成功！', $this->createWebUrl('city', array('op' => 'display')), 'success');
}else {
	message('请求方式不存在');
}
include $this->template('web/city');
?>