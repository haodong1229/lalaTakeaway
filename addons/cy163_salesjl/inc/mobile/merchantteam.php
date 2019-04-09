<?php
global $_W, $_GPC;
$merchant = $this->checkmergentauth();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if($operation == 'xiaji'){
	if(!empty($merchant)){
		$fopenid = $merchant['openid'];
	}else{
		$fopenid = '333';
	}
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} AND fopenid = '{$fopenid}'");
	$psize = 10;
	$allpage = ceil($total/$psize)+1;
	$page = intval($_GPC["page"]);
	$pindex = max(1, $page);
	$branchlist = pdo_fetchall("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} AND fopenid = '{$fopenid}' ORDER BY addtime DESC LIMIT ".($pindex - 1)*$psize.",".$psize);
	foreach($branchlist as $k=>$v){
		$branchlist[$k]['member'] = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE weid = {$_W['uniacid']} AND openid = '{$v['openid']}'");
	}
	$isajax = intval($_GPC['isajax']);
	if($isajax == 1){
		$html = '';
		foreach($branchlist as $k=>$v){
			$html .= '<div class="item flex">
						<img src="'.$v['member']['avatar'].'" />
						<div class="text">
							<div class="nickname">'.$v['name'].'（'.$v['member']['nickname'].'）</div>
							<div class="time">加入时间：'.date("Y-m-d H:i:s",$v['addtime']).'</div>
						</div>
					</div>';
		}
		echo $html;
		exit;
	}
	include $this->template('merchantxiaji');
}elseif($operation == 'haibao'){
	$this->mkdirs(HB_ROOT_TD);
	$qrcodename = HB_ROOT_TD."{$member['openid']}.png";
	//if(!file_exists($qrcodename)){
		include ROOT_PATH.'qrcode.class.php';    
		$value = $_W['siteroot'].'app/'.str_replace('./','',$this->createMobileUrl('merchant',array('yqcode'=>$merchant['yqcode']))); //二维码内容   
		$errorCorrectionLevel = 'L';//容错级别   
		$matrixPointSize = 6;//生成图片大小   
		//生成二维码图片
		QRcode::png($value,$qrcodename,$errorCorrectionLevel,$matrixPointSize,2); 
	//}
	
	$filename = HB_ROOT_TD."{$member['openid']}-hb.jpg";
	
	$data2 = array(
		'new_img' => $filename
	);
	$this->createmerImg($data2,$qrcodename);
	
	$resarr['imgurl'] = $_W["siteroot"].'addons/cy163_salesjl/haibao/tuandui/'.$member['openid'].'-hb.jpg?v='.TIMESTAMP;
	echo json_encode($resarr,true);
	exit;
}
?>