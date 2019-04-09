<?php
global $_W,$_GPC;
$operation = empty($_GPC['op']) ? 'display' : $_GPC['op'];
if($operation == 'display'){
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$typefilter = intval($_GPC['typefilter']);
	if($typefilter == 0){
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_MEMBERRZ)." as a,".tablename(BEST_MEMBER)." as b WHERE a.rztype = 1 AND b.rztype = 1 AND a.openid = b.openid AND a.weid= {$_W['uniacid']}");
		$list = pdo_fetchall("SELECT a.*,b.nickname,b.avatar,b.rztype as brztype FROM ".tablename(BEST_MEMBERRZ)." as a,".tablename(BEST_MEMBER)." as b WHERE a.rztype = 1 AND b.rztype = 1 AND a.openid = b.openid AND a.weid= {$_W['uniacid']} ORDER BY a.rztime DESC LIMIT ".($pindex - 1)*$psize.",".$psize);
	}
	
	if($typefilter == 1){
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_MEMBERRZ)." as a,".tablename(BEST_MEMBER)." as b WHERE a.rztype = 1 AND b.rztype = 0 AND a.openid = b.openid AND a.weid= {$_W['uniacid']}");
		$list = pdo_fetchall("SELECT a.*,b.nickname,b.avatar,b.rztype as brztype FROM ".tablename(BEST_MEMBERRZ)." as a,".tablename(BEST_MEMBER)." as b WHERE a.rztype = 1 AND b.rztype = 0 AND a.openid = b.openid AND a.weid= {$_W['uniacid']} ORDER BY a.rztime DESC LIMIT ".($pindex - 1)*$psize.",".$psize);
	}
	
	if($typefilter == 2){
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_MEMBERRZ)." as a,".tablename(BEST_MEMBER)." as b WHERE a.rztype = 2 AND b.rztype = 2 AND a.openid = b.openid AND a.weid= {$_W['uniacid']}");
		$list = pdo_fetchall("SELECT a.*,b.nickname,b.avatar,b.rztype as brztype FROM ".tablename(BEST_MEMBERRZ)." as a,".tablename(BEST_MEMBER)." as b WHERE a.rztype = 2 AND b.rztype = 2 AND a.openid = b.openid AND a.weid= {$_W['uniacid']} ORDER BY a.rztime DESC LIMIT ".($pindex - 1)*$psize.",".$psize);
	}
	
	if($typefilter == 3){
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_MEMBERRZ)." as a,".tablename(BEST_MEMBER)." as b WHERE a.rztype = 2 AND b.rztype = 0 AND a.openid = b.openid AND a.weid= {$_W['uniacid']}");
		$list = pdo_fetchall("SELECT a.*,b.nickname,b.avatar,b.rztype as brztype FROM ".tablename(BEST_MEMBERRZ)." as a,".tablename(BEST_MEMBER)." as b WHERE a.rztype = 2 AND b.rztype = 0 AND a.openid = b.openid AND a.weid= {$_W['uniacid']} ORDER BY a.rztime DESC LIMIT ".($pindex - 1)*$psize.",".$psize);
	}

	$pager = pagination($total, $pindex, $psize);
}elseif($operation == 'delete'){
	$id = intval($_GPC['id']);
	$memberrz = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERRZ)." WHERE id = {$id}");
	if (empty($memberrz)) {
		returnError('抱歉，该记录不存在或是已经被删除！');
	}
	$jujuesm = trim($_GPC['jujuesm']);
	if (empty($jujuesm)) {
		returnError('抱歉，请填写拒绝原因！');
	}
	pdo_update(BEST_MEMBERRZ,array('isjujue'=>1,'jujuesm'=>$jujuesm,'jjtime'=>TIMESTAMP),array('id'=>$id));
	pdo_update(BEST_MEMBER,array('rztype'=>0),array('openid'=>$memberrz['openid']));
	
	
	/*
	if($this->module['config']['istplon'] == 1){
		if($memberrz['rztype'] == 1){
			$or_paysuccess_redirect = $_W["siteroot"].'app/'.str_replace("./","",$this->createMobileUrl("gerenrz"));
		}else{
			$or_paysuccess_redirect = $_W["siteroot"].'app/'.str_replace("./","",$this->createMobileUrl("qiyerz"));
		}
		$postdata = array(
			'first' => array(
				'value' => "您提交的认证已被拒绝，拒绝原因：".$jujuesm,
				'color' => '#ff510'
			),
			'keyword1' => array(
				'value' => "认证",
				'color' => '#ff510'
			),
			'keyword2' => array(
				'value' => "被拒绝",
				'color' => '#ff510'
			),
			'keyword3' => array(
				'value' => date("Y年m月d日H:i",TIMESTAMP),
				'color' => '#ff510'
			),
			'remark' => array(
				'value' => '点击查看详情',
				'color' => '#ff510'
			),			
		);
		$account_api = WeAccount::create();
		$account_api->sendTplNotice($memberrz['openid'],$this->module['config']['agent_tz'],$postdata,$or_paysuccess_redirect,'#980000');
	}
	*/
	
	returnSuccess('拒绝成功!');
}elseif($operation == 'tongguo'){
	$id = intval($_GPC['id']);
	$memberrz = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERRZ)." WHERE id = {$id}");
	if (empty($memberrz)) {
		message('抱歉，该记录不存在或是已经被删除！', $this->createWebUrl('renzheng', array('op' => 'display')), 'error');
	}
	pdo_update(BEST_MEMBERRZ,array('isjujue'=>0,'tgtime'=>TIMESTAMP),array('id'=>$id));
	pdo_update(BEST_MEMBER,array('rztype'=>$memberrz['rztype']),array('openid'=>$memberrz['openid']));
	
	/*
	if($this->module['config']['istplon'] == 1){
		$or_paysuccess_redirect = $_W["siteroot"].'app/'.str_replace("./","",$this->createMobileUrl("renzheng"));
		$postdata = array(
			'first' => array(
				'value' => "恭喜您，提交的认证已通过审核。",
				'color' => '#ff510'
			),
			'keyword1' => array(
				'value' => "认证",
				'color' => '#ff510'
			),
			'keyword2' => array(
				'value' => "已审核",
				'color' => '#ff510'
			),
			'keyword3' => array(
				'value' => date("Y年m月d日H:i",TIMESTAMP),
				'color' => '#ff510'
			),
			'remark' => array(
				'value' => '点击查看详情',
				'color' => '#ff510'
			),			
		);
		$account_api = WeAccount::create();
		$account_api->sendTplNotice($memberrz['openid'],$this->module['config']['agent_tz'],$postdata,$or_paysuccess_redirect,'#980000');
	}
	*/
	message('操作成功！', $this->createWebUrl('renzheng', array('op' => 'display')), 'success');
}elseif($operation == 'delete2'){
	$id = intval($_GPC['id']);
	$renzheng = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBERRZ)." WHERE id = {$id}");
	if(empty($renzheng)){
		message("不存在该认证记录！");
	}
	pdo_delete(BEST_MEMBERRZ,array('id'=>$id));
	pdo_update(BEST_MEMBER,array('rztype'=>0),array('openid'=>$renzheng['openid']));
	message('删除成功！', referer(), 'success');
}
include $this->template('web/renzheng');
?>