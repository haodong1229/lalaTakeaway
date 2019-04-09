<?php
global $_GPC, $_W;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {	
	$keyword = trim($_GPC['keyword']);
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$starttime  = $_GPC['starttime'];
	$endtime  = $_GPC['endtime'];
	if (empty($starttime)) {
		$starttime = strtotime('-1 year');
	}else{
		$starttime = strtotime($_GPC['starttime']);
	}
	if (empty($endtime)) {
		$endtime = TIMESTAMP;
	}else{
		$endtime = strtotime($_GPC['endtime']);
	}
	
	$condition = "weid = {$_W['uniacid']} AND isjl = 0 AND createtime >= {$starttime} AND createtime <= {$endtime} AND status = 4 ";
	
	if (!empty($_GPC['merkeyword'])) {
		$merchant = pdo_fetch("SELECT id FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} AND (name like '%{$_GPC['merkeyword']}%' OR realname like '%{$_GPC['merkeyword']}%' OR telphone like '%{$_GPC['merkeyword']}%')");
		$condition .= " AND merchant_id = {$merchant['id']}";
	}

	$allyongjin = pdo_fetchcolumn("SELECT SUM(alllirun) FROM ".tablename(BEST_ORDER)." WHERE ".$condition);
	$allyongjin = empty($allyongjin) ? 0 : $allyongjin;
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_ORDER)." WHERE ".$condition);
	$total = empty($total) ? 0 : $total;
	
	if ($total > 0) {
		if ($_GPC['export'] == '') {
			$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
		}else{
			$limit = '';
		}
		$list2 = pdo_fetchall("SELECT id FROM ".tablename(BEST_ORDER)." WHERE ".$condition);
		$allorderidarr = array();
		foreach($list2 as $k=>$v){
			$allorderidarr[] = $v['id'];
		}
		$allprice = pdo_fetchcolumn("SELECT SUM(price*total) FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid in (".implode(',',$allorderidarr).")");
		$allcbprice = pdo_fetchcolumn("SELECT SUM(cbprice*total) FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid in (".implode(',',$allorderidarr).")");
		$alldlprice = pdo_fetchcolumn("SELECT SUM(dlprice*total) FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid in (".implode(',',$allorderidarr).")");
		$alllirun = $allprice - $allcbprice;
		

		$list = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE ".$condition." ORDER BY createtime DESC".$limit);
		foreach($list as $k=>$v){
			$list[$k]['goodslist'] = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$v['id']}");
			$list[$k]['merchant'] = pdo_fetch("SELECT name FROM ".tablename(BEST_MERCHANT)." WHERE id = {$v['merchant_id']}");
		}
		$pager = pagination($total, $pindex, $psize);
		if ($_GPC['export'] == 'export') {
			/* 输入到CSV文件 */
			$html = "\xEF\xBB\xBF";
			/* 输出表头 */
			$filter = array('订单号','商品名称','数量','成本价','代理价','销售价','利润','佣金','订单状态','下单时间','代理商家');
			foreach ($filter as $key => $title) {
				$html .= $title . "\t,";
			}
			$html .= "\n";
			$alltotal = 0;
			
			foreach ($list as $k => $v) {
				foreach($v['goodslist'] as $kk=>$vv){
					$alltotal += $vv['total'];

					$html .= $v['ordersn']. "\t, ";
					$html .= empty($vv['optionname']) ? $vv['goodsname']. "\t, " : $vv['goodsname']."[".$vv['optionname']."]". "\t, ";
					$html .= $vv['total']. "\t, ";
					$html .= $vv['cbprice']*$vv['total']. "\t, ";
					$html .= $vv['dlprice']*$vv['total']. "\t, ";
					$html .= $vv['price']*$vv['total']. "\t, ";
					$html .= $vv['price']*$vv['total']-$vv['cbprice']*$vv['total']. "\t, ";
					$html .= $vv['lirun']. "\t, ";
					if($v['status'] == 0){
						$html .= "未付款". "\t, ";
					}
					if($v['status'] == 1){
						$html .= "已付款". "\t, ";
					}
					if($v['status'] == 2){
						$html .= "待收货". "\t, ";
					}
					if($v['status'] == 4){
						$html .= "已完成". "\t, ";
					}
					$html .= date("Y-m-d H:i:s",$v['createtime']). "\t, ";
					$html .= $v['merchant']['name']. "\t, ";
					$html .= "\n";
				}
			}
			$html .= "". "\t, ";
			$html .= "". "\t, ";
			$html .= $alltotal. "\t, ";
			$html .= $allcbprice. "\t, ";
			$html .= $alldlprice. "\t, ";
			$html .= $allprice. "\t, ";
			$html .= $alllirun. "\t, ";
			$html .= $allyongjin. "\t, ";
			$html .= "". "\t, ";
			$html .= "". "\t, ";
			$html .= "". "\t, ";
			$html .= "\n";
			/* 输出CSV文件 */
			header("Content-type:text/csv");
			header("Content-Disposition:attachment; filename=结算数据.csv");
			echo $html;
			exit();
		}		
	}
	include $this->template('web/jiesuan');
}
?>