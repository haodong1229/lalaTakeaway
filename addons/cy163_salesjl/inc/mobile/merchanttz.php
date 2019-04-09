<?php
global $_GPC, $_W;
$merchant = $this->checkmergentauth();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
	if($merchant['yqcode'] == ""){
		$yqcode = $this->randomkeys(4);
		pdo_update(BEST_MERCHANT,array('yqcode'=>$yqcode),array('id'=>$merchant['id']));
	}
	$merchant = $this->checkmergentauth();
	include $this->template('merchanttz');
}
?>