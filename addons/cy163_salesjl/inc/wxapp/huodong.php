<?php
global $_W,$_GPC;
$mhdid = intval($_GPC['id']);
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if($operation == 'display'){
	$openid = trim($_GPC['openid']);
	$member = pdo_fetch("SELECT * FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$openid}'");
	$merchanthd = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANTHD)." WHERE weid = {$_W['uniacid']} AND id = {$mhdid}");
	$hdres = pdo_fetch("SELECT * FROM ".tablename(BEST_HUODONG)." WHERE weid = {$_W['uniacid']} AND id = {$merchanthd['hdid']}");
	$hdres['daojishi'] = 1;
	if($hdres['pstype'] == 1){
		$hdres['cansonghuo'] = $merchanthd['cansonghuo'];
		$hdres['canziti'] = $merchanthd['canziti'];
		$hdres['manjian'] = $merchanthd['manjian'];
	}else{
		$hdres['cansonghuo'] = 1;
		$hdres['canziti'] = 0;
	}
	if($hdres['canziti'] == 1 && $hdres['cansonghuo'] == 0){
		$hdres['pstype'] = 1;
	}else{
		$hdres['pstype'] = 0;
	}
	$hdres['mhdid'] = $merchanthd['id'];
	if($hdres['cansonghuo'] == 1){
		$hdres['shzt'] = '满'.$hdres['manjian'].'元免运费';
	}else{
		$hdres['shzt'] = '免运费';
	}
	$hdres['iserror'] = 0;
	if($hdres['tqjs'] == 1){
		$hdres['iserror'] = 1;
		$hdres['errormessage'] = '活动已经提前结束';
		$hdres['daojishi'] = 0;
	}
	if($hdres['starttime'] > TIMESTAMP){
		$hdres['iserror'] = 1;
		$hdres['errormessage'] = '活动还未开始';
	}
	if($hdres['endtime'] < TIMESTAMP){
		$hdres['iserror'] = 1;
		$hdres['errormessage'] = '活动已经结束';
		$hdres['daojishi'] = 0;
	}
	$hdres['color'] = empty($this->module['config']['temcolor']) ? '#E64340' : $this->module['config']['temcolor'];
	$hdres['sharedes'] = $merchanthd["sharedes"];
	if($merchanthd['sharetitle']){
		$hdres['title'] = $merchanthd['sharetitle'];
	}
	$hdres['sharethumb'] = tomedia($merchanthd['sharethumb']);
	$merchant = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} AND id = {$merchanthd['merchant_id']}");
	$merchant['avatar'] = tomedia($merchant['avatar']);
	
	$ztdlist = pdo_fetchall("SELECT * FROM ".tablename(BEST_ZITIDIAN)." WHERE openid = '{$merchant['openid']}' AND weid = {$_W['uniacid']} AND ztdtype = 1");
	if(empty($ztdlist)){
		$ztdlist[0]['id'] = 0;
	}
		
	$goodslist = pdo_fetchall("SELECT goodsid FROM ".tablename(BEST_MERCHANTHDGOODS)." WHERE mhdid = {$mhdid} GROUP BY goodsid");
	foreach($goodslist as $k=>$v){
		$hdgoods = pdo_fetch("SELECT displayorder FROM ".tablename(BEST_HUODONGGOODS)." WHERE weid = {$_W['uniacid']} AND hdid = {$merchanthd['hdid']} AND goodsid = {$v['goodsid']}");
		$goodsres = pdo_fetch("SELECT id,autofield,basicsales,thumb,thumbs,title3,title2,title,ftitle,chengbenprice,normalprice,dailiprice,scprice,total,shotdes,xiangounum FROM ".tablename(BEST_GOODS)." WHERE weid = {$_W['uniacid']} AND id = {$v['goodsid']}");
		
		$mhdoptions = pdo_fetchall("SELECT * FROM ".tablename(BEST_MERCHANTHDGOODS)." WHERE mhdid = {$mhdid} AND goodsid = {$v['goodsid']}");
		foreach($mhdoptions as $kk=>$vv){
			if($vv['optionid'] > 0){
				$option = pdo_fetch("SELECT * FROM ".tablename(BEST_GOODSOPTION)." WHERE id = {$vv['optionid']}");
				if(empty($option)){
					unset($mhdoptions[$kk]);
				}else{
					if($vv['dlprice'] > $option['dailiprice']){
						$mhdoptions[$kk]['saleprice'] = $vv['dlprice'];
					}else{
						$mhdoptions[$kk]['saleprice'] = $option['normalprice'];
					}
					$option['sales'] = pdo_fetchcolumn("SELECT SUM(a.total) FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.optionid = {$option['id']} AND a.orderid = b.id AND b.hdid = {$merchanthd['hdid']} AND b.status > 0");
					$option['sales'] = empty($option['sales']) ? $goodsres['basicsales'] : $goodsres['basicsales']+$option['sales'];
					if($option['xiangounum'] > 0){
						$optionmy['sales'] = pdo_fetchcolumn("SELECT SUM(a.total) FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.optionid = {$option['id']} AND a.orderid = b.id AND b.from_user = '{$member['openid']}' AND b.hdid = {$merchanthd['hdid']} AND b.status >= 0");
						$optionmy['sales'] = empty($optionmy['sales']) ? 0 : $optionmy['sales'];
						$option['xiangounum'] = $option['xiangounum']-$optionmy['sales'];
						if($option['stock'] > $option['xiangounum']){
							$option['canbuy'] = $option['xiangounum'];
						}
					}else{
						$option['canbuy'] = $option['stock'];
					}
					$mhdoptions[$kk]['option'] = $option;
				}
			}else{
				if($vv['dlprice'] > $goodsres['dailiprice']){
					$mhdoptions[$kk]['saleprice'] = $vv['dlprice'];
				}else{
					$mhdoptions[$kk]['saleprice'] = $goodsres['normalprice'];
				}
				$mhdoptions[$kk]['option'] = "";
			}
			$mhdoptions[$kk]['now'] = $kk;
			$mhdoptions[$kk]['nownum'] = 0;
		}
		$goodslist[$k]['options'] = $mhdoptions;
		

		$thumbs = unserialize($goodsres['thumbs']);
		$thumbsarr = array();
		foreach($thumbs as $kk=>$vv){
			$thumbsarr[$kk] = tomedia($vv);
		}
		$goodsres['thumb'] = tomedia($goodsres['thumb']);
		$goodsres['thumb1'] = tomedia($thumbs[0]);
		$goodsres['thumb2'] = tomedia($thumbs[1]);
		array_unshift($thumbsarr,$goodsres['thumb']);
		$goodsres['thumbsarr'] = $thumbsarr;
		
		$goodsres['sales'] = pdo_fetchcolumn("SELECT SUM(a.total) FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.goodsid = {$goodsres['id']} AND a.orderid = b.id AND b.hdid = {$merchanthd['hdid']} AND b.status > 0");
		$goodsres['sales'] = empty($goodsres['sales']) ? $goodsres['basicsales'] : $goodsres['basicsales']+$goodsres['sales'];
		if($goodsres['xiangounum'] > 0){
			$goodsresmy['sales'] = pdo_fetchcolumn("SELECT SUM(a.total) FROM ".tablename(BEST_ORDERGOODS)." as a,".tablename(BEST_ORDER)." as b WHERE a.goodsid = {$goodsres['id']} AND a.orderid = b.id AND b.from_user = '{$member['openid']}' AND b.hdid = {$merchanthd['hdid']} AND b.status >= 0");
			$goodsresmy['sales'] = empty($goodsresmy['sales']) ? 0 : $goodsresmy['sales'];
			$goodsres['xiangounum'] = $goodsres['xiangounum']-$goodsresmy['sales'];
			if($goodsres['total'] > $goodsres['xiangounum']){
				$goodsres['canbuy'] = $goodsres['xiangounum'];
			}
		}else{
			$goodsres['canbuy'] = $goodsres['total'];
		}
		$goodslist[$k]['goods'] = $goodsres;
		if(empty($thumbs)){
			$goodslist[$k]['count'] = 1;
		}else{
			$goodslist[$k]['count'] = count($thumbs)+1;
		}
		$goodslist[$k]['displayorder'] = $hdgoods['displayorder'];
	}
	if(!function_exists('array_column')){
		array_multisort($this->getNewArrByElement($goodslist,'displayorder'),SORT_DESC,$goodslist);
	}else{
		array_multisort(array_column($goodslist,'displayorder'),SORT_DESC,$goodslist);
	}
	foreach($goodslist as $k=>$v){
		$goodslist[$k]['now'] = $k;
	}
	
	$buylistthree = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDER)." WHERE mhdid = {$merchanthd['id']} AND isjl = 0 AND status >= 1 ORDER BY createtime DESC LIMIT 10");
	foreach($buylistthree as $k=>$v){
		$buylistthree[$k]['member'] = pdo_fetch("SELECT avatar,nickname FROM ".tablename(BEST_MEMBER)." WHERE openid = '{$v['from_user']}'");
		$buylistthree[$k]['goods'] = pdo_fetchall("SELECT * FROM ".tablename(BEST_ORDERGOODS)." WHERE orderid = {$v['id']}");
		$buylistthree[$k]['time'] = date("Y-m-d H:i:s",$v['createtime']);
	}
	
	$res['hdres'] = $hdres;
	if($member['shcity'] != ''){
		$member['shcity'] = explode(",",$member['shcity']);
	}
	$res['member'] = $member;
	$res['merchant'] = $merchant;
	$res['buylistthree'] = $buylistthree;
	$res['goodslist'] = $goodslist;
	$res['ztdlist'] = $ztdlist;
	$res['yijiquyu'] = pdo_fetchall("SELECT name,code FROM ".tablename(BEST_CITY)." WHERE type = 1");
	$this->result(0,"活动页", $res);
}elseif($operation == 'sub'){
	$member['openid'] = trim($_GPC['openid']);
	$allnum = 0;
	$allprice = 0;
	$alllirun = 0;
	$hdres = pdo_fetch("SELECT * FROM ".tablename(BEST_HUODONG)." WHERE weid = {$_W['uniacid']} AND id = {$_GPC['hdid']}");
	if($hdres['tqjs'] == 1){
		$this->result(1,"活动已经提前结束！", "");
	}
	if($hdres['starttime'] > TIMESTAMP){
		$this->result(1,"活动还未开始！", "");
	}
	if($hdres['endtime'] < TIMESTAMP){
		$this->result(1,"活动已经结束！", "");
	}
	$pstype = intval($_GPC['pstype']);
	if($pstype != 0 && $pstype != 1){
		$this->result(1,"请选择配送方式！", "");
	}
	
	$goodslist = $this->messistr2array($_GPC['goodslist']);	
	foreach($goodslist as $k=>$v){
		foreach($v['options'] as $kk=>$vv){
			if($vv['nownum'] > 0){
				$optionid = intval($vv['optionid']);
				if($optionid > 0){
					if($vv['nownum'] > $vv['option']['stock']){
						$this->result(1,"商品".$v['goods']['title']."[".$vv['option']['title']."]"."库存不足！", "");
					}
					if($vv['nownum'] > $vv['option']['xiangounum'] && $vv['option']['xiangounum'] > 0){
						$this->result(1,"商品".$v['goods']['title']."[".$vv['option']['title']."]"."限购".$vv['option']['xiangounum']."件！", "");
					}
					$alllirun += ($vv['saleprice']-$vv['option']['dailiprice'])*$vv['nownum'];
				}else{
					if($vv['nownum'] > $v['goods']['total']){
						$this->result(1,"商品".$v['goods']['title']."库存不足！", "");
					}
					if($vv['nownum'] > $v['goods']['xiangounum'] && $v['goods']['xiangounum'] > 0){
						$this->result(1,"商品".$v['goods']['title']."限购".$v['goods']['xiangounum']."件！", "");
					}
					$alllirun += ($vv['saleprice']-$v['goods']['dailiprice'])*$vv['nownum'];
				}
				$allprice += $vv['saleprice']*$vv['nownum'];
			}
			$allnum += $vv['nownum'];
		}
	}
	if($allnum <= 0){		
		$this->result(1,"请选择需要购买的商品！", "");
	}
	if($allprice <= 0){
		$this->result(1,"订单总金额不得少于0元！", "");
	}
	$data['price'] = $allprice;
	$data['alllirun'] = $alllirun;
	if($pstype == 0){
		$shname = trim($_GPC['shname']);
		$shphone = trim($_GPC['shphone']);
		$shcity = trim($_GPC['shcity']);
		$shaddress = trim($_GPC['shaddress']);
		if(empty($shname)){
			$this->result(1,"请填写收货人姓名！", "");
		}
		if(!$this->isMobile($shphone)){
			$this->result(1,"请填写正确的收货人手机号码！", "");
		}
		if(empty($shcity)){
			$this->result(1,"请选择地区！", "");
		}
		if(empty($shaddress)){
			$this->result(1,"请填写详细地址！", "");
		}
		if($hdres['pstype'] == 0){
			if($hdres['yfid'] > 0){
				$diquarr = explode(",",$shcity);
				$sheng = $diquarr[0];
				$shi = $diquarr[1];
				$xian = $diquarr[2];
				$yfsheng1 = pdo_fetch("SELECT * FROM ".tablename(BEST_YUNFEISHENG)." WHERE yfid = {$hdres['yfid']} AND diqutype = 3 AND name = '{$sheng}' AND city = '{$shi}' AND xian = '{$xian}'");
				$yfsheng2 = pdo_fetch("SELECT * FROM ".tablename(BEST_YUNFEISHENG)." WHERE yfid = {$hdres['yfid']} AND diqutype = 2 AND name = '{$sheng}' AND city = '{$shi}' AND xian = ''");
				$yfsheng3 = pdo_fetch("SELECT * FROM ".tablename(BEST_YUNFEISHENG)." WHERE yfid = {$hdres['yfid']} AND diqutype = 1 AND name = '{$sheng}' AND city = '' AND xian = ''");
				if(empty($yfsheng1) && empty($yfsheng2) && empty($yfsheng3)){
					$this->result(1,"不在活动售卖区域不能提交订单！", "");
				}
			}
			if($data['price'] >= $hdres['manjian']){
				$data['yunfei'] = 0;
			}else{
				if(!empty($yfsheng1)){
					$data['yunfei'] = $yfsheng1['money'];
				}
				if(!empty($yfsheng2) && empty($yfsheng1)){
					$data['yunfei'] = $yfsheng2['money'];
				}
				if(!empty($yfsheng3) && empty($yfsheng1) && empty($yfsheng2)){
					$data['yunfei'] = $yfsheng3['money'];
				}
			}
		}else{
			$merchanthd = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANTHD)." WHERE weid = {$_W['uniacid']} AND id = {$_GPC['mhdid']}");
			if($data['price'] >= $merchanthd['manjian']){
				$data['yunfei'] = 0;
			}else{
				$data['yunfei'] = $merchanthd['yunfei'];
			}
			$pstype = 3;
			$data['alllirun'] = $data['alllirun'] + $data['yunfei'];
		}
		$data['remark'] = $_GPC['remark'];
		$datam['shname'] = $shname;
		$datam['shphone'] = $shphone;
		$datam['shcity'] = $shcity;
		$datam['shaddress'] = $shaddress;
		$data['address'] = $shname."|".$shphone."|".$shcity."|".$shaddress;
		$data['price'] = $data['price']+$data['yunfei'];
		$data['isdmfk'] = intval($_GPC['isdmfk']);
		if($data['isdmfk'] == 1){
			$data['status'] = 2;
		}
	}else{
		$shname = $datam['shname'] = trim($_GPC['shname2']);
		if(empty($shname)){
			$this->result(1,"请填写取货人人姓名！", "");
		}
		$shphone = $datam['shphone'] = trim($_GPC['shphone2']);
		if(!$this->isMobile($shphone)){
			$this->result(1,"请填写自提所需的手机号码！", "");
		}
		$ztdid = intval($_GPC['ztdid']);
		$ztdres = pdo_fetch("SELECT * FROM ".tablename(BEST_ZITIDIAN)." WHERE id = {$ztdid}");
		if(empty($ztdres)){
			$this->result(1,"请选择自提点！", "");
		}
		$data['remark'] = $_GPC['remark2'];
		$data['ztdid'] = $ztdid;
		$data['ztdaddress'] = $ztdres['address'];
		$data['ztdjingdu'] = $ztdres['jingdu'];
		$data['ztdweidu'] = $ztdres['weidu'];
		$data['address'] = $shphone."|".$shname;
		$data['yunfei'] = 0;
		if($hdres['pstype'] == 1){
			$pstype = 4;
		}
		$data['isdmfk'] = intval($_GPC['isdmfk']);
		if($data['isdmfk'] == 1){
			$data['status'] = 1;
		}
	}
	$autofield = intval($_GPC['autofield']);
	if($autofield == 1){
		$idcardlen = strlen($_GPC['idcard']);
		//$isidcard = $this->validation_filter_id_card($_GPC['idcard']);
		if($idcardlen != 18){
			$this->result(1,"请填写18位数的身份证号！", "");
		}
		$data['otheraddress'] = $_GPC['idcard']."(身份证)";
	}
	
	$data['weid'] = $_W['uniacid'];
	$data['pstype'] = $pstype;
	$data['from_user'] = $member['openid'];
	$data['ordersn'] = date('Ymd').random(13,1);
	$data['merchant_id'] = intval($_GPC['merchant_id']);
	$data['createtime'] = TIMESTAMP;
	$data['hdid'] = intval($_GPC['hdid']);
	$data['mhdid'] = intval($_GPC['mhdid']);
	if(isset($this->module['config']['dltkhour'])){
		$data['cantktime'] = $hdres['endtime'] - ($this->module['config']['dltkhour'])*3600;
	}
	$data['formid'] = $_GPC['formid'];
	pdo_insert(BEST_ORDER, $data);
	$orderid = pdo_insertid();

	pdo_update(BEST_MEMBER,$datam,array('openid'=>$member['openid']));
	foreach($goodslist as $k=>$v){
		foreach($v['options'] as $kk=>$vv){
			if($vv['nownum'] > 0){
				$optionid = intval($vv['optionid']);
				if($optionid > 0){
					$datao['weid'] = $_W['uniacid'];
					$datao['optionid'] = $optionid;
					$datao['total'] = $vv['nownum'];
					$datao['price'] = $vv['saleprice'];
					$datao['cbprice'] = $vv['option']['chengbenprice'];
					$datao['dlprice'] = $vv['option']['dailiprice'];
					$datao['goodsid'] = $v['goodsid'];
					$datao['createtime'] = TIMESTAMP;
					$datao['orderid'] = $orderid;
					$datao['goodsname'] = $v['goods']['title'];
					$datao['optionname'] = $vv['option']['title'];
					$datao['lirun'] = ($vv['saleprice']-$vv['option']['dailiprice'])*$vv['nownum'];
				}else{
					$datao['weid'] = $_W['uniacid'];
					$datao['optionid'] = 0;
					$datao['total'] = $vv['nownum'];
					$datao['price'] = $vv['saleprice'];
					$datao['cbprice'] = $v['goods']['chengbenprice'];
					$datao['dlprice'] = $v['goods']['dailiprice'];
					$datao['goodsid'] = $v['goodsid'];
					$datao['createtime'] = TIMESTAMP;
					$datao['orderid'] = $orderid;
					$datao['goodsname'] = $v['goods']['title'];
					$datao['optionname'] = "";
					$datao['lirun'] = ($vv['saleprice']-$v['goods']['dailiprice'])*$vv['nownum'];
				}
				$datao['hdid'] = intval($_GPC['hdid']);
				$datao['mhdid'] = intval($_GPC['mhdid']);
				pdo_insert(BEST_ORDERGOODS,$datao);
			}
		}
	}
	if($data['isdmfk'] == 1){
		$this->result(0,"提交订单成功！", '');
	}else{
		$this->result(0,"提交订单成功！", $data['ordersn']);
	}
}elseif($operation == 'tongzhi'){
	$pid = trim($_GPC['pid']);
	$pidarr = explode('=',$pid);
	$ordersn = trim($_GPC['ordersn']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND ordersn = '{$ordersn}' AND isjl = 0");
	//$merchant = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} AND id = {$orderres['merchant_id']}");
	if($this->module['config']['istplon'] == 1 && !empty($this->module['config']['nt_order_new'])){
		$temvalue = array(
			"keyword1"=>array(
				"value"=>$orderres['ordersn'],
				"color"=>"#4a4a4a"
			),
			"keyword2"=>array(
				"value"=>$orderres['price'],
				"color"=>"#9b9b9b"
			),
			"keyword3"=>array(
				"value"=>date("Y-m-d H:i:s",$orderres['createtime']),
				"color"=>"#9b9b9b"
			),
			"keyword4"=>array(
				"value"=>$orderres['address'],
				"color"=>"#9b9b9b"
			)
		);
		$account_api = WeAccount::create();
		$access_token = $account_api->getAccessToken();
		$url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token;
		$dd = array();
		$dd['touser'] = $orderres['from_user'];
		$dd['template_id'] = $this->module['config']['nt_order_new'];
		$dd['page'] = 'cy163_salesjl/pages/myorderdetail/myorderdetail?ordersn='.$orderres['ordersn']; //点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,该字段不填则模板无跳转。
		$dd['form_id'] = $pidarr[1];
		$dd['data'] = $temvalue;                        //模板内容，不填则下发空模板
		$dd['color'] = '';                        //模板内容字体的颜色，不填默认黑色
		$dd['emphasis_keyword'] = '';    //模板需要放大的关键词，不填则默认无放大
		//$dd['emphasis_keyword']='keyword1.DATA';
		//$send = json_encode($dd);   //二维数组转换成json对象
		
		/* curl_post()进行POST方式调用api： api.weixin.qq.com*/
		//load()->func('communication');
		//$result = ihttp_post($url,json_encode($dd));
		$result = $this->https_curl_json($url,$dd,'json');
		/*$path = ROOT_PATH.'messi.txt';
			$myfile = fopen($path, "w") or die("Unable to open file!");
			fwrite($myfile, $pid.$result.$pidarr[1]);
			fclose($myfile);*/
		/*if($result){
			echo json_encode(array('state'=>5,'msg'=>$result));
		}else{
			echo json_encode(array('state'=>5,'msg'=>$result));
		}*/
	}
	$this->result(0,"发送成功！", '');
}elseif($operation == 'tongzhi2'){
	$ordersn = trim($_GPC['ordersn']);
	$orderres = pdo_fetch("SELECT * FROM ".tablename(BEST_ORDER)." WHERE weid = {$_W['uniacid']} AND ordersn = '{$ordersn}' AND isjl = 0");
	$merchant = pdo_fetch("SELECT * FROM ".tablename(BEST_MERCHANT)." WHERE weid = {$_W['uniacid']} AND id = {$orderres['merchant_id']}");
	if($this->module['config']['istplon'] == 1 && !empty($this->module['config']['nt_order_new'])){
		$temvalue = array(
			"keyword1"=>array(
				"value"=>$orderres['ordersn'],
				"color"=>"#4a4a4a"
			),
			"keyword2"=>array(
				"value"=>$orderres['price'],
				"color"=>"#9b9b9b"
			),
			"keyword3"=>array(
				"value"=>date("Y-m-d H:i:s",$orderres['createtime']),
				"color"=>"#9b9b9b"
			),
			"keyword4"=>array(
				"value"=>$orderres['address'],
				"color"=>"#9b9b9b"
			)
		);
		$account_api = WeAccount::create();
		$access_token = $account_api->getAccessToken();
		$url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token;
		$dd = array();
		$dd['touser'] = $merchant['openid'];
		$dd['template_id'] = $this->module['config']['nt_order_new'];
		$dd['page'] = 'cy163_salesjl/pages/merorderdetail/merorderdetail?id='.$orderres['id']; //点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,该字段不填则模板无跳转。
		$dd['form_id'] = $orderres['formid'];
		$dd['data'] = $temvalue;                        //模板内容，不填则下发空模板
		$dd['color'] = '';                        //模板内容字体的颜色，不填默认黑色
		$dd['emphasis_keyword'] = '';    //模板需要放大的关键词，不填则默认无放大

		$result = $this->https_curl_json($url,$dd,'json');
	}
	$this->result(0,"发送成功！", '');
}
?>