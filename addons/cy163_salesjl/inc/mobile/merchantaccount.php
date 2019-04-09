<?php
global $_W,$_GPC;
$merchant = $this->checkmergentauth();
$memberrz = pdo_fetch("SELECT rztype FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$merchant['openid']}'");
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$allmoney = pdo_fetchcolumn("SELECT SUM(money) FROM ".tablename(BEST_MERCHANTACCOUNT)." WHERE weid = {$_W['uniacid']} AND merchant_id = {$merchant['id']} AND istking = 0 AND candotime < ".TIMESTAMP);
$allmoney = empty($allmoney) ? 0.00 : round($allmoney,2);
if($operation == 'display'){
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_MERCHANTACCOUNT)." WHERE weid = {$_W['uniacid']} AND merchant_id = {$merchant['id']}");
	$psize = 10;
	$allpage = ceil($total/$psize)+1;
	$page = intval($_GPC["page"]);
	$pindex = max(1, $page);
	$moneylist = pdo_fetchall("SELECT * FROM ".tablename(BEST_MERCHANTACCOUNT)." WHERE merchant_id = {$merchant['id']} AND weid = {$_W['uniacid']} ORDER BY time DESC LIMIT ".($pindex - 1)*$psize.",".$psize);
	foreach($moneylist as $k=>$v){
		if($v['orderid'] > 0){
			$moneyorderres = pdo_fetch("SELECT from_user FROM ".tablename(BEST_ORDER)." WHERE id = {$v['orderid']}");
			$moneylist[$k]['user'] = pdo_fetch("SELECT avatar FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$moneyorderres['from_user']}'");
		}else{
			$moneylist[$k]['user'] = '';
		}
	}
	$isajax = intval($_GPC['isajax']);
	if($isajax == 1){
		$html = '';
		foreach($moneylist as $k=>$v){
			$money = $v['money'] > 0 ? '<div class="num add text-r">+'.$v['money'].'</div>' : '<div class="num min text-r">'.$v['money'].'</div>';
			$daijiesuan = $v['money'] > 0 && $v['candotime'] > TIMESTAMP ? '<span style="font-size:0.28rem;color:red;margin-left:0.2rem;">待结算</span>' : '';
			if(!empty($v['user'])){
				$imghtml = '<img src="'.$v['user']['avatar'].'" />';
			}else{
				$imghtml = '';
			}
			$html .= '<div class="item flex">
							'.$imghtml.'
							<div class="right">
								<div class="title textellipsis1">'.$v['explain'].$daijiesuan.'</div>
								<div class="time textellipsis1">'.date("Y-m-d H:i:s",$v['time']).'</div>
								'.$money.'
							</div>
						</div>';
		}
		echo $html;
		exit;
	}else{
		include $this->template('merchantaccount');
	}
}elseif($operation == 'dotixian'){
	if(!checksubmit('submit')){
		exit;
	}
	if($this->module['config']['rztixian'] == 1 && $memberrz['rztype'] == 0){
		$resArr['error'] = 1;
		$resArr['message'] = '实名认证后才能提现！';
		echo json_encode($resArr);
		exit();
	}
	$money = $_GPC['money'];
	if($money <= 0){
		$resArr['error'] = 1;
		$resArr['message'] = '请输入正确的提现金额！';
		echo json_encode($resArr);
		exit();
	}
	if($money > $allmoney){
		$resArr['error'] = 1;
		$resArr['message'] = '您的余额不足！';
		echo json_encode($resArr);
		exit();
	}
	if($money < $this->module['config']['present_money'] || $money > $this->module['config']['present_money_end']){
		$resArr['error'] = 1;
		$resArr['message'] = '提现金额必须在'.$this->module['config']['present_money'].'元 ~ '.$this->module['config']['present_money_end'].'元之间！';
		echo json_encode($resArr);
		exit();
	}
	
	$txtype = intval($_GPC['txtype']);
	if($txtype == 1){
		$realname = trim($_GPC['realname']);
		$zhanghao = trim($_GPC['zhanghao']);
		if($realname == '' || $zhanghao == ''){
			$resArr['error'] = 1;
			$resArr['message'] = '请填写姓名和支付宝账号！';
			echo json_encode($resArr);
			exit();
		}
	}else{
		$realname = $zhanghao = '';
	}
	
	if($merchant['usetx'] == 1){
		$txdisaccount = $merchant['txdisaccount'];
	}else{
		$txdisaccount = $this->module['config']['txdisaccount'];
	}
	$shouxufei = abs($money)*$txdisaccount/100;
	$shouxufei = sprintf("%.2f", $shouxufei);
	$shidao = abs($money) - $shouxufei;
	$data = array(
		'weid'=>$_W['uniacid'],
		'openid'=>$merchant['openid'],
		'money'=>-$money,
		'time'=>TIMESTAMP,
		'explain'=>'提现',
		'feilv'=>$txdisaccount,
		'dzprice'=>$shidao,
		'membertype'=>2,
		'realname'=>$realname,
		'zhanghao'=>$zhanghao,
		'txtype'=>$txtype,
	);
	pdo_insert(BEST_TIXIAN,$data);
	$txid = pdo_insertid();
	$data2 = array(
		'weid'=>$_W['uniacid'],
		'merchant_id'=>$merchant['id'],
		'money'=>-$money,
		'time'=>TIMESTAMP,
		'explain'=>'提现',
		'candotime'=>TIMESTAMP,
	);
	pdo_insert(BEST_MERCHANTACCOUNT,$data2);
	
	
	if($txtype == 0){
		$msmoney = $this->module['config']['msmoney'];
		if($msmoney >= $money){
			$tixian = pdo_fetch("SELECT * FROM ".tablename(BEST_TIXIAN)." WHERE id = {$txid} AND weid = {$_W['uniacid']}");
			try {
				$ret=$this->transferByPay(array(
					'id'=>$tixian['id'],
					'openid'=>$tixian['openid'],
					'amount'=>abs($tixian['dzprice']*100),
					'desc'=>'提现'.abs($tixian['dzprice']).'元',
				));
			} catch (Exception $e) {
				$ret=false;
			}
			if(!is_error($ret)){ // 发放失败
				// 更新记录状态
				$datatx['txstatus'] = 1;
				$datatx['dztime'] = strtotime($ret['payment_time']);
				pdo_update(BEST_TIXIAN,$datatx,array('id'=>$tixian['id']));
			}
		}
	}
	$resArr['error'] = 0;
	$resArr['message'] = '提交提现申请成功！';
	echo json_encode($resArr);
	exit();
}
?>