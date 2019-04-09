<?php
global $_FILES,$_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
	if($_GPC['allpx'] == 'allpx' && !empty($_GPC['displayorder'])){
		foreach ($_GPC['displayorder'] as $id => $displayorder) {
			pdo_update(BEST_XIAOQU, array('displayorder' => $displayorder), array('id' => $id, 'weid' => $_W['uniacid']));
		}
		message('排序更新成功！', $this->createWebUrl('village', array('op' => 'display')), 'success');
	}
	
	if($_GPC['alldel'] == 'alldel' && !empty($_GPC['xqid'])){
		foreach ($_GPC['xqid'] as $id => $xqid) {
			$village = pdo_fetch("SELECT id,zzopenid FROM ".tablename(BEST_XIAOQU)." WHERE id = {$xqid} AND weid = {$_W['uniacid']}");
			if($village['zzopenid'] != ''){
				pdo_update(BEST_MERCHANT, array('xqz'=>0), array('openid' => $village['zzopenid']));
			}
			pdo_delete(BEST_XIAOQU, array('id' => $xqid));
		}
		message('小区删除成功！', $this->createWebUrl('village', array('op' => 'display')), 'success');
	}
	
	$keyword = $_GPC['keyword'];
	$condition = " weid = {$_W['uniacid']} ";
	if ($keyword != '') {
		$condition .= " AND name like '%{$keyword}%' ";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_XIAOQU)." WHERE ".$condition);
	$list = pdo_fetchall("SELECT * FROM ".tablename(BEST_XIAOQU)." WHERE ".$condition." ORDER BY displayorder ASC LIMIT ".($pindex - 1)*$psize.",".$psize);
	$pager = pagination($total, $pindex, $psize);
}elseif ($operation == 'search') {
	$zzkey = trim($_GPC['zzkey']);
	$merres = pdo_fetch("SELECT openid,name FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} AND xqz = 0 AND (name like '%{$zzkey}%' OR openid like '%{$zzkey}%') AND status = 1");
	if(empty($merres)){
		$resArr['error'] = 1;
		$resArr['merres'] = "没有搜索到团长哦！";
	}else{
		$resArr['error'] = 0;
		$resArr['merres'] = $merres;
	}
	echo json_encode($resArr,true);
	exit;
} elseif ($operation == 'piliang') {
	if($_GPC['moxing'] == "moxing"){
		/* 输入到CSV文件 */
		$html = "\xEF\xBB\xBF";
		/* 输出表头 */
		$filter = array('排序','小区名称','小区地址','坐标以xxx,xxx的格式经度在后纬度在前');
		foreach ($filter as $key => $title) {
			$html .= $title . "\t,";
		}
		/* 输出CSV文件 */
		header("Content-type:text/csv");
		header("Content-Disposition:attachment; filename=小区模型.csv");
		echo $html;
		exit();
	}
	if($_GPC['submit']){
		if($_FILES['wenjian']['tmp_name'] != ""){
			$file = fopen($_FILES['wenjian']['tmp_name'],"r");
			$now = 0;
			while(!feof($file)){
				if($now > 0){
					$data = $cityres = $cityres2 = array();
					$csvres = fgetcsv($file);
					if($csvres){
						$csvres[3] = iconv("GBK","UTF-8//TRANSLIT//IGNORE",$csvres[3]);
						$zbs = explode(",",$csvres[3]);
						
						//$zbres = $this->Convert_BD09_To_GCJ02($zbs[1],$zbs[0]);
						$data = array(
							'weid' => $_W['uniacid'],
							'displayorder'=> intval($csvres[0]),
							'name'=>iconv("GBK","UTF-8//TRANSLIT//IGNORE",$csvres[1]),
							'address'=>iconv("GBK","UTF-8//TRANSLIT//IGNORE",$csvres[2]),
							'jingdu' => $zbs[1],
							'weidu' => $zbs[0],
						);
						pdo_insert(BEST_XIAOQU, $data);
					}
				}
				$now++;
			}
			fclose($file);
			message('导入小区成功！', $this->createWebUrl('village', array('op' => 'display')), 'success');
		}else{
			message('请上传csv文件！');
		}
	}
}elseif ($operation == 'post') {
	$id = intval($_GPC['id']);
	$village = pdo_fetch("SELECT * FROM ".tablename(BEST_XIAOQU)." WHERE id = {$id} and weid= {$_W['uniacid']}");
	if (checksubmit('submit')) {
		$zbs = explode(",",$_GPC['zbs']);
		$data = array(
			'weid' => $_W['uniacid'],
			'displayorder'=> intval($_GPC['displayorder']),
			'name'=>$_GPC['name'],
			'logo'=>$_GPC['logo'],
			'address'=>$_GPC['address'],
			'jingdu' => $zbs[1],
			'weidu' => $zbs[0],
			'cityid'=> intval($_GPC['cityid']),
			'cityidd'=> intval($_GPC['cityidd']),
			'zzopenid'=>$_GPC['zzopenid'],
		);
		if (!empty($id)) {
			if($village['zzopenid'] != $_GPC['zzopenid']){
				pdo_update(BEST_MERCHANT, array('xqz'=>0), array('openid' => $village['zzopenid']));
			}
			pdo_update(BEST_XIAOQU, $data, array('id' => $id));
		} else {
			pdo_insert(BEST_XIAOQU, $data);
		}
		if($_GPC['zzopenid'] != ""){
			pdo_update(BEST_MERCHANT, array('xqz'=>1), array('openid' => $_GPC['zzopenid']));
		}
		message('操作成功！', $this->createWebUrl('village', array('op' => 'display')), 'success');
	}
} elseif ($operation == 'delete') {
	$id = intval($_GPC['id']);
	$village = pdo_fetch("SELECT id,zzopenid FROM ".tablename(BEST_XIAOQU)." WHERE id = {$id} AND weid = {$_W['uniacid']}");
	if (empty($village)) {
		message('抱歉，小区不存在或是已经被删除！', referer(), 'error');
	}
	if($village['zzopenid'] != ''){
		pdo_update(BEST_MERCHANT, array('xqz'=>0), array('openid' => $village['zzopenid']));
	}
	pdo_delete(BEST_XIAOQU, array('id' => $id));
	message('小区删除成功！', referer(), 'success');
}else {
	message('请求方式不存在');
}
include $this->template('web/village');
?>