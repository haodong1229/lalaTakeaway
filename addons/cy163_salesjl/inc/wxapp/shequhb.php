<?php
global $_W,$_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if($operation == 'display'){
	$sence = $_GPC['scene'];
	$sencearr = explode("-",$sence);
	$id = $sencearr[0];
		
	$goods = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODS)." WHERE id = {$id} AND weid = {$_W['uniacid']}");
	$thumb = tomedia($goods['thumb']);
	$price = $goods['normalprice'];
	$scprice = $goods['scprice'];

	$account_api = WeAccount::create();
	$access_token = $account_api->getAccessToken();
	$url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=".$access_token;
	$data = array(
		"scene"=>$_GPC['scene'],
		"page"=>"cy163_salesjl/pages/ggdetail/ggdetail",
		"width"=>430,
	);
	$data = json_encode($data);
	$response = $this->curl_post($url,$data);
	
	//保存小程序二维码
	$this->mkdirs(HB_ROOT_SHEQU);
	$qrcodename = HB_ROOT_SHEQU.$_GPC['scene'].'.jpg';
	file_put_contents($qrcodename, $response);
	
	$filename = HB_ROOT_SHEQU.$_GPC['scene'].'-hb.jpg';
	$data2 = array(
		'title' =>$goods['title'],
		'price_market' => '￥'.$price,
		'price_member' => '￥'.$scprice,
		'shotdes' => $goods['shotdes'],
		'goods_img' => $thumb,
		'new_img' => $filename
	);
	$this->createImg($data2,$qrcodename);
	
	$resarr['filename'] = $_W["siteroot"].'addons/cy163_salesjl/haibao/shequ/'.$_GPC['scene'].'-hb.jpg?v='.TIMESTAMP;
	$resarr['qrcodename'] = $_W["siteroot"].'addons/cy163_salesjl/haibao/shequ/'.$_GPC['scene'].'.jpg?v='.TIMESTAMP;
	$this->result(0,'海报！', $resarr);
}
?>