<?php
global $_W,$_GPC;
$longitude = $_GPC['latitude'];
$latitude = $_GPC['longitude'];
$radius = 6378.138; // 地球半径 KM
$hm = empty($this->module['config']['kms']) ? 0 : $this->module['config']['kms'];
if($this->module['config']['gqzfjlzs'] == 1){
	$allfujin = pdo_fetchall("SELECT *,({$radius}*acos(cos(radians({$latitude}))*cos(radians(jingdu))*cos(radians(weidu)-radians({$longitude}))+sin(radians({$latitude}))*sin(radians(jingdu)))) AS distance FROM ".tablename(BEST_MEMBERHUODONG)." HAVING distance < {$hm} AND weid = {$_W['uniacid']} AND status = 1 AND owndel = 0 AND admindel = 0 ORDER BY distance");
}else{			
	$allfujin = pdo_fetchall("SELECT *,({$radius}*acos(cos(radians({$latitude}))*cos(radians(jingdu))*cos(radians(weidu)-radians({$longitude}))+sin(radians({$latitude}))*sin(radians(jingdu)))) AS distance FROM ".tablename(BEST_MEMBERHUODONG)." HAVING distance < {$hm} AND weid = {$_W['uniacid']} AND status = 1 AND owndel = 0 AND admindel = 0 AND endtime > ".TIMESTAMP." ORDER BY distance");
}
$total = count($allfujin);
$psize = 10;
$allpage = ceil($total/$psize)+1;
$page = intval($_GPC["page"]);
$pindex = max(1, $page);
if($this->module['config']['gqzfjlzs'] == 1){
	$sql = "SELECT *,({$radius}*acos(cos(radians({$latitude}))*cos(radians(jingdu))*cos(radians(weidu)-radians({$longitude}))+sin(radians({$latitude}))*sin(radians(jingdu)))) AS distance FROM ".tablename(BEST_MEMBERHUODONG)." HAVING distance < {$hm} AND weid = {$_W['uniacid']} AND status = 1 AND owndel = 0 AND admindel = 0 ORDER BY distance LIMIT ".($pindex - 1)*$psize.",".$psize;
}else{
	$sql = "SELECT *,({$radius}*acos(cos(radians({$latitude}))*cos(radians(jingdu))*cos(radians(weidu)-radians({$longitude}))+sin(radians({$latitude}))*sin(radians(jingdu)))) AS distance FROM ".tablename(BEST_MEMBERHUODONG)." HAVING distance < {$hm} AND weid = {$_W['uniacid']} AND status = 1 AND owndel = 0 AND admindel = 0 AND endtime > ".TIMESTAMP." ORDER BY distance LIMIT ".($pindex - 1)*$psize.",".$psize;
}
$jielonglist = pdo_fetchall($sql);

foreach($jielonglist as $k=>$v){
	$jielonglist[$k]['time'] = date("m-d",$v['time']);
	if($v['starttime'] > TIMESTAMP){
		$jielonglist[$k]['status'] = '未开始';
		$jielonglist[$k]['color'] = 'hui';
	}elseif($v['endtime'] < TIMESTAMP){
		$jielonglist[$k]['status'] = '已结束';
		$jielonglist[$k]['color'] = 'hui';
	}else{
		$jielonglist[$k]['status'] = '进行中';
		$jielonglist[$k]['color'] = 'buhui';
	}
	$thumbs = unserialize($v['thumbs']);
	$jielonglist[$k]['thumb1'] = tomedia($thumbs[0]);
	$jielonglist[$k]['thumb2'] = tomedia($thumbs[1]);
	$jielonglist[$k]['thumb3'] = tomedia($thumbs[2]);
}
$this->result(0,$allpage, $jielonglist);
?>