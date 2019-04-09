<?php
global $_W,$_GPC;
$member = $this->Mcheckmember();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$allmoney = pdo_fetchcolumn("SELECT SUM(money) as allmoney FROM ".tablename(BEST_MEMBERACCOUNT)." WHERE openid = '{$member['openid']}' AND weid = {$_W['uniacid']} AND istking = 0 AND candotime < ".TIMESTAMP);
$allmoney = empty($allmoney) ? 0 : $allmoney;
if($operation == 'display'){
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_MEMBERACCOUNT)." WHERE weid = {$_W['uniacid']} AND openid = '{$member['openid']}'");
	$allpage = ceil($total/10)+1;
	$page = intval($_GPC["page"]);
	$pindex = max(1, $page);
	$psize = 10;
	$moneylist = pdo_fetchall("SELECT * FROM ".tablename(BEST_MEMBERACCOUNT)." WHERE weid = {$_W['uniacid']} AND openid = '{$member['openid']}' ORDER BY time DESC LIMIT ".($pindex - 1)*$psize.",".$psize);
	$isajax = intval($_GPC['isajax']);
	if($isajax == 1){
		$html = '';
		foreach($moneylist as $k=>$v){						
			$money = $v['money'] > 0 ? '<div class="num add text-r">+'.$v['money'].'</div>' : '<div class="num min text-r">'.$v['money'].'</div>';
			$daijiesuan = $v['money'] > 0 && $v['candotime'] > TIMESTAMP ? '<span style="font-size:0.28rem;color:red;margin-left:0.2rem;">待结算</span>' : '';
			$html .= '<div class="item">
						<div class="title textellipsis1">'.$v['explain'].$daijiesuan.'</div>
						<div class="time textellipsis1">'.date("Y-m-d H:i:s",$v['time']).'</div>
						'.$money.'
					</div>';
		}
		echo $html;
		exit;
	}else{
		include $this->template('account');
	}
}elseif($operation == 'dotixian'){
	if(!checksubmit('submit')){
		exit;
	}
	if($this->module['config']['rztixian'] == 1 && $member['rztype'] == 0){
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
	
	$shouxufei = abs($money)*$this->module['config']['usertxdisaccount']/100;
	$shouxufei = sprintf("%.2f", $shouxufei);
	$shidao = abs($money) - $shouxufei;
	$data = array(
		'weid'=>$_W['uniacid'],
		'openid'=>$member['openid'],
		'money'=>-$money,
		'time'=>TIMESTAMP,
		'explain'=>'提现',
		'feilv'=>$this->module['config']['usertxdisaccount'],
		'dzprice'=>$shidao,
		'membertype'=>1,
		'realname'=>$realname,
		'zhanghao'=>$zhanghao,
		'txtype'=>$txtype,
	);
	pdo_insert(BEST_TIXIAN,$data);
	$txid = pdo_insertid();
	$data2 = array(
		'weid'=>$_W['uniacid'],
		'openid'=>$member['openid'],
		'money'=>-$money,
		'time'=>TIMESTAMP,
		'explain'=>'提现',
		'candotime'=>TIMESTAMP,
	);
	pdo_insert(BEST_MEMBERACCOUNT,$data2);
	
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
	$resArr['message'] = '提现申请成功！';
	echo json_encode($resArr);
	exit();
}
?>