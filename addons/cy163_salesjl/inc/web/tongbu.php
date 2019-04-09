<?php
global $_W, $_GPC;
$tbtype = intval($_GPC['tbtype']);
if($tbtype == 0){
	$orders = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE ztdid > 0 AND ztdaddress = ''");
	foreach($orders as $k=>$v){
		$ztdres = pdo_fetch("SELECT * FROM ".tablename(BEST_ZITIDIAN)." WHERE id = {$v['ztdid']}");
		if(!empty($ztdres)){
			$data['ztdaddress'] = $ztdres['address'];
			$data['ztdjingdu'] = $ztdres['jingdu'];
			$data['ztdweidu'] = $ztdres['weidu'];
			pdo_update(BEST_ORDER,$data,array('id'=>$v['id']));
		}
	}
	
	$orders2 = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE status = -2 AND refund_status = 1");
	foreach($orders2 as $k=>$v){
		$ddd['status'] = -3;
		pdo_update(BEST_ORDER,$ddd,array('id'=>$v['id']));
	}
}
message('操作成功！', "", 'success');
?>