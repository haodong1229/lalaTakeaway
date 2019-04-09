<?php
/*淘宝柠檬鱼科技 https://shop486845690.taobao.com*/
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
wcheckauth();
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'index';
$sid = intval($_GPC['sid']);
$store = store_fetch($sid);
if(is_error($store) || empty($store)) {
	ijson(error(-1, '门店不存在'), '', 'ajax');
}
if(empty($store['location_x']) || empty($store['location_y'])) {
	//ijson(error(-1, '门店未设置经纬度，需要完善经纬度后才能下单'), '', 'ajax');
}

if($ta == 'goods') {
	$cart = order_insert_member_cart($sid);
	if(is_error($cart)) {
		ijson(error(-1, $cart['message']), '', 'ajax');
	}
	ijson(error(0, '保存购物车数据成功'), '', 'ajax');
}

elseif($ta == 'index') {
	$cart = order_fetch_member_cart($sid);
	if(empty($cart)) {
		ijson(error(-1, '购物车为空，请重新选择商品'), '', 'ajax');
	}
	$order_type = 1;
	$delivery_info = array(
		'total_delivery_price' => 0,
		'delivery_free_price' => $store['delivery_free_price'],
	);
	if($store['delivery_fee_mode'] == 1) {
		$delivery_price_basic = $store['delivery_price'];
		$delivery_price = $store['delivery_price'] + 0;
	} elseif($store['delivery_fee_mode'] == 2) {
		$delivery_price = $delivery_price_basic = $store['delivery_price_extra']['start_fee'];
	}
	$delivery_info['total_delivery_price'] = $delivery_price;

	$activityed = order_count_activity($sid, $cart, $coupon_id, $redpacket_id, $delivery_info['total_delivery_price'], $delivery_info['delivery_free_price'], $order_type);
	//平台附加费
	$extra_fee_note = array();
	$extra_fee = 0;
	if(!empty($store['data']['extra_fee'])) {
		foreach($store['data']['extra_fee'] as $item) {
			$item_fee = floatval($item['fee']);
			if($item['status'] == 1 && $item_fee > 0) {
				$extra_fee += $item_fee;
				$extra_fee_note[] = $item;
			}
		}
	}
	$order = array(
		'order_type' => $order_type,
		'pack_fee' => $store['pack_price'],
		'price' => $cart['price'],
		'box_price' => $cart['box_price'],
		'delivery_fee' => $delivery_info['total_delivery_price'],
		'extra_fee' => $extra_fee,
		'extra_fee_detail' => $extra_fee_note,
		'total_fee' => round($cart['price'] + $cart['box_price'] + $store['pack_price'] + $delivery_info['total_delivery_price'] + $extra_fee, 2),
		'discount_fee' => $activityed['total'],
		'activityed' => $activityed,
	);
	$order['final_fee'] = $order['total_fee'] - $activityed['total'];

	$result = array(
		'store' => $store,
		'cart' => $cart,
		'order' => $order,
	);
	ijson(error(0, $result), '', 'ajax');
}

elseif($ta == 'submit') {
	$cart = iorder_check_member_cart($sid);
	if(is_error($cart)) {
		ijson($cart, '', 'ajax');
	}
	if(!$store['is_in_business_hours']) {
		ijson(error(-1, '商户休息中'), '', 'ajax');
	}
	$address = array();
	$address['username'] = trim($_GPC['username']);
	if(empty($address['username'])) {
		ijson(error(-1, '收货人姓名不能为空'), '', 'ajax');
	}
	$address['mobile'] = trim($_GPC['mobile']);
	if(empty($address['mobile'])) {
		ijson(error(-1, '收货人手机号不能为空'), '', 'ajax');
	}
	$address['address'] = trim($_GPC['address']);
	if(empty($address['address'])) {
		ijson(error(-1, '收货人地址不能为空'), '', 'ajax');
	}

	$delivery_day = trim($_GPC['delivery_day']);
	if(empty($delivery_time)) {
		$delivery_day = date('Y-m-d');
	}
	$delivery_time = trim($_GPC['delivery_time']);
	if(empty($delivery_time)) {
		$delivery_time = '尽快送达';
	}
	$order_type = 1;
	$delivery_info = array(
		'total_delivery_price' => 0,
		'delivery_free_price' => $store['delivery_free_price'],
	);
	if($store['delivery_fee_mode'] == 1) {
		$delivery_price_basic = $store['delivery_price'];
		$delivery_price = $store['delivery_price'] + 0;
	} elseif($store['delivery_fee_mode'] == 2) {
		$delivery_price = $delivery_price_basic = $store['delivery_price_extra']['start_fee'];
	}
	$delivery_info['total_delivery_price'] = $delivery_price;

	$activityed = order_count_activity($sid, $cart, $coupon_id, $redpacket_id, $delivery_info['total_delivery_price'], $delivery_info['delivery_free_price'], $order_type);
	//平台附加费
	$extra_fee_note = array();
	$extra_fee = 0;
	if(!empty($store['data']['extra_fee'])) {
		foreach($store['data']['extra_fee'] as $item) {
			$item_fee = floatval($item['fee']);
			if($item['status'] == 1 && $item_fee > 0) {
				$extra_fee += $item_fee;
				$extra_fee_note[] = $item;
			}
		}
	}

	$calculate = array(
		'order_type' => $order_type,
		'pack_fee' => $store['pack_price'],
		'price' => $cart['price'],
		'box_price' => $cart['box_price'],
		'delivery_fee' => $delivery_info['total_delivery_price'],
		'extra_fee' => $extra_fee,
		'extra_fee_detail' => $extra_fee_note,
		'total_fee' => round($cart['price'] + $cart['box_price'] + $store['pack_price'] + $delivery_info['total_delivery_price'] + $extra_fee, 2),
		'discount_fee' => $activityed['total'],
		'activityed' => $activityed,
		'person_num' => trim($condition['person_num']),
	);
	$calculate['final_fee'] = $calculate['total_fee'] - $activityed['total'];

	$order = array(
		'uniacid' => $_W['uniacid'],
		'agentid' => $store['agentid'],
		'acid' => $_W['acid'],
		'sid' => $sid,
		'uid' => $_W['member']['uid'],
		'mall_first_order' => $_W['member']['is_mall_newmember'],
		'ordersn' => date('YmdHis') . random(6, true),
		'serial_sn' => store_order_serial_sn($sid),
		'code' => random(4, true),
		'order_type' => $calculate['order_type'],
		'openid' => $_W['openid'],
		'emp_no' => $_W['member']['uid'],
		'mobile' => $address['mobile'] ? $address['mobile'] : $_W['member']['mobile'],
		'username' => $address['username'] ? $address['username'] : $_W['member']['realname'],
		'sex' => $address['sex'],
		'address' => $address['address'] . $address['number'],
		'location_x' => floatval($address['location_x']),
		'location_y' => floatval($address['location_y']),
		'delivery_day' => $delivery_day,
		'delivery_time' => $delivery_time,
		'delivery_fee' => $calculate['delivery_fee'],
		'pack_fee' => $calculate['pack_fee'],
		'pay_type' => 'credit',
		'is_pay' => 0,
		'paytime' => TIMESTAMP,
		'num' => $cart['num'],
		'distance' => $address['distance'],
		'box_price' => $calculate['box_price'],
		'price' => $calculate['price'],
		'extra_fee' => $calculate['extra_fee'],
		'total_fee' => $calculate['total_fee'],
		'discount_fee' => $calculate['discount_fee'],
		'store_discount_fee' => $calculate['activityed']['store_discount_fee'],
		'plateform_discount_fee' => $calculate['activityed']['plateform_discount_fee'],
		'agent_discount_fee' => $calculate['activityed']['agent_discount_fee'],
		'final_fee' => $calculate['final_fee'] > 0 ? $calculate['final_fee'] : 0,
		'vip_free_delivery_fee' => !empty($calculate['activityed']['list']['vip_delivery']) ? 1 : 0,
		'delivery_type' => $store['delivery_mode'],
		'status' => 1,
		'is_comment' => 0,
		'invoice' => $invoice,
		'addtime' => TIMESTAMP,
		'data' => array(
			'formId' => $condition['formId'],
			'extra_fee' => $calculate['extra_fee_detail'],
			'cart' => iunserializer($cart['original_data']),
			'commission' => array(
				'spread1_rate' => "0%",
				'spread1' => 0,
				'spread2_rate' => "0%",
				'spread2' => 0,
			)
		),
		'spreadbalance' => 1,
		'note' => trim($_GPC['note']),
	);
	$order['data'] = iserializer($order['data']);
	pdo_insert('tiny_wmall_order', $order);
	$order_id = pdo_insertid();
	$order_id = intval($order_id);
	if(empty($order_id)) {
		ijson(error(-1, '订单信息有误，请重新下单'), '', 'ajax');
	}
	order_update_bill($order_id, array('activity' => $calculate['activityed']));
	order_insert_discount($order_id, $sid, $calculate['activityed']['list']);
	order_insert_status_log($order_id, 'place_order');
	order_update_goods_info($order_id, $sid);
	order_del_member_cart($sid);
	$result = array(
		'id' => $order_id
	);
	$params = array(
		'order_channel' => 'wechat',
		'pay_type' => 'credit',
		'card_fee' => $order['final_fee'],
		'paytime' => TIMESTAMP,
	);
	mload()->classs('TyAccount');
	$data = order_system_status_update($order_id, 'pay', $params);
	ijson(error(0, $result), '', 'ajax');
}

function icart_data_init($sid, $goods_id = 0, $option_key = 0, $sign = '', $ignore_bargain= false, $cart = array()) {
	global $_W;
	$option_key = trim($option_key);
	if(empty($option_key)) {
		$option_key = 0;
	}
	mload()->model('goods');
	if(empty($cart)) {
		$cart = order_fetch_member_cart($sid, false);
	}

	$goods_ids = array();
	if(!empty($cart)) {
		$goods_ids = array_keys($cart['data']);
	}
	if(empty($goods_ids)) {
		return error(-1, '购物车信息有误1');
	}
	$goods_ids[] = $goods_id;
	$goods_ids_str = implode(',', $goods_ids);
	$goods_info = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods') ." WHERE uniacid = :uniacid AND sid = :sid AND id IN ($goods_ids_str)", array(':uniacid' => $_W['uniacid'], ':sid' => $sid), 'id');
	$options = pdo_fetchall('select * from ' . tablename('tiny_wmall_goods_options') . " where uniacid = :uniacid and sid = :sid and goods_id in ($goods_ids_str) ", array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	foreach($options as $option) {
		$goods_info[$option['goods_id']]['options'][$option['id']] = $option;
	}
	$bargain_goods_ids = array();
	if(!$ignore_bargain) {
		mload()->model('activity');
		activity_store_cron($sid);
		$bargains = pdo_getall('tiny_wmall_activity_bargain', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'status' => '1'), array(), 'id');
		if(!empty($bargains)) {
			$bargain_ids = implode(',', array_keys($bargains));
			$bargain_goods = pdo_fetchall('select * from ' . tablename('tiny_wmall_activity_bargain_goods') . " where uniacid = :uniacid and sid = :sid and bargain_id in ({$bargain_ids})", array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
			$bargain_goods_group = array();
			if(!empty($bargain_goods)) {
				foreach($bargain_goods as &$row) {
					$bargain_goods_ids[$row['goods_id']] = $row['bargain_id'];
					$row['available_buy_limit'] = $row['max_buy_limit'];
					$bargain_goods_group[$row['bargain_id']][$row['goods_id']] = $row;
				}
			}

			$where = " where uniacid = :uniacid and sid = :sid and uid = :uid and stat_day = :stat_day and bargain_id in ({$bargain_ids}) group by bargain_id";
			$params = array(
				':uniacid' => $_W['uniacid'],
				':sid' => $sid,
				':stat_day' => date('Ymd'),
				':uid' => $_W['member']['uid']
			);
			$bargain_order = pdo_fetchall('select count(distinct(oid)) as num, bargain_id from ' . tablename('tiny_wmall_order_stat') . $where, $params, 'bargain_id');
			foreach($bargains as &$row) {
				$row['available_goods_limit'] = $row['goods_limit'];
				$row['goods'] = $bargain_goods_group[$row['id']];

				$row['avaliable_order_limit'] = $row['order_limit'];
				if(!empty($bargain_order)) {
					$row['avaliable_order_limit'] = $row['order_limit'] - intval($bargain_order[$row['id']]['num']);
				}
				$row['hasgoods'] = array();
			}
		} else {
			$bargains = array();
		}
	}
	$total_num = 0;
	$total_original_price = 0;
	$total_price = 0;
	$total_box_price = 0;
	$cart_bargain = array();
	$bargain_has_goods = array(); //已有效参与特价活动的商品
	if(!empty($cart)) {
		foreach($cart['data'] as $k => $v) {
			$k = intval($k);
			$goods = $goods_info[$k];
			if(empty($goods) || $k == '88888') {
				continue;
			}
			if(!goods_is_available($goods)) {
				unset($cart['data'][$k]);
				unset($cart['original_data'][$k]);
				continue;
			}
			$goods_box_price = $goods['box_price'];
			if(!$goods['is_options']) {
				$discount_num = 0;
				foreach($v as $key => $val) {
					$goods['options_data'] = goods_build_options($goods);
					$key = trim($key);
					$option = $goods['options_data'][$key];
					if(empty($option) || empty($option['total'])) {
						continue;
					}
					$num = intval($val['num']);
					if($option['total'] != -1 && $option['total'] <= $num) {
						$num = $option['total'];
					}
					if($num <= 0) {
						continue;
					}
					if($goods['total'] != -1) {
						$goods['total'] -= $num;
						$goods['total'] = max($goods['total'], 0);
					}
					$title = $goods_info[$k]['title'];
					if(!empty($key)) {
						$title = "{$title}({$option['name']})";
					}
					$cart_item = array(
						'cid' => $goods_info[$k]['cid'],
						'child_id' => $goods_info[$k]['child_id'],
						'goods_id' => $k,
						'thumb' => tomedia($goods_info[$k]['thumb']),
						'title' => $title,
						'option_title' => $option['name'],
						'num' => $num,
						'price' => $goods_info[$k]['price'],
						'discount_price' => $goods_info[$k]['price'],
						'discount_num' => 0,
						'price_num' => $num,
						'total_price' => round($goods_info[$k]['price'] * $num, 2),
						'total_discount_price' => round($goods_info[$k]['price'] * $num, 2),
						'bargain_id' => 0
					);
					if(in_array($k, array_keys($bargain_goods_ids))) {
						$goods_bargain_id = $bargain_goods_ids[$k];
						$bargain = $bargains[$goods_bargain_id];
						$bargain_goods = $bargain['goods'][$k];
						//max_buy_limit：每单限购
						$val['discount_num'] =  min($bargain_goods['max_buy_limit'], $num);
						//available_goods_limit:每单限购几种折扣商品，available_buy_limit：每单限购折扣商品几份，discount_available_total：折扣商品的库存
						if($bargain['avaliable_order_limit'] > 0 && $bargain['available_goods_limit'] > 0 && $bargain_goods['available_buy_limit'] > 0) {
							for($i = 0; $i < $val['discount_num']; $i++) {
								if($bargain_goods['poi_user_type'] == 'new' && empty($_W['member']['is_store_newmember'])) {
									break;
								}
								if(($bargain_goods['discount_available_total'] == -1 || $bargain_goods['discount_available_total'] > 0) && $bargain_goods['available_buy_limit'] > 0) {
									$cart_item['discount_price'] = $bargain_goods['discount_price'];
									$cart_item['discount_num']++;
									$cart_item['bargain_id'] = $bargain['id'];
									$cart_bargain[] = $bargain['use_limit'];
									if($cart_item['price_num'] > 0) {
										$cart_item['price_num']--;
									}
									if($bargain_goods['discount_available_total'] > 0) {
										$bargain_goods['discount_available_total']--;
										$bargains[$goods_bargain_id]['goods'][$k]['discount_available_total']--;
									}
									$bargain_goods['available_buy_limit']--;
									$bargains[$goods_bargain_id]['goods'][$k]['available_buy_limit']--;
									$discount_num++;
									$bargain_has_goods[] = $k;
								} else {
									break;
								}
							}
							$cart_item['total_discount_price'] = $cart_item['discount_num'] * $bargain_goods['discount_price'] + $cart_item['price_num'] * $goods_info[$k]['price'] ;
							$cart_item['total_discount_price'] = round($cart_item['total_discount_price'], 2);
						}
					}

					$total_num += $num;
					$total_price += $cart_item['total_discount_price'];
					$total_original_price += $cart_item['total_price'];
					$total_box_price += ($goods_box_price * $num);
					$cart_goods[$k][$key] = $cart_item;
				}

				if($discount_num > 0) {
					$bargain['available_goods_limit']--;
					$bargains[$goods_bargain_id]['available_goods_limit']--;
					//此处不应该在减available_buy_limit，因为上面的for循环里已经减去了
					//$bargains[$goods_bargain_id]['goods'][$k]['available_buy_limit'] -= $discount_num;
				}
				$totalnum = get_cart_goodsnum($k, -1, 'num', $cart_goods);
				if($goods_info[$k]['total'] != -1) {
					$goods_info[$k]['total'] -= $totalnum;
					$goods_info[$k]['total'] = max($goods_info[$k]['total'], 0);
				}
			} else {
				foreach($v as $key => $val) {
					$goods['options_data'] = goods_build_options($goods);
					$option_id = tranferOptionid($key);
					$key = trim($key);
					$option = $goods['options_data'][$key];
					if(empty($option) || empty($option['total'])) {
						continue;
					}
					$num = intval($val['num']);
					if($option['total'] != -1 && $option['total'] <= $num) {
						$num = $option['total'];
					}
					if($num <= 0) {
						continue;
					}
					if($goods['options'][$option_id]['total'] != -1) {
						$goods['options'][$option_id]['total'] -= $num;
						$goods['options'][$option_id]['total'] = max($goods['options'][$option_id]['total'], 0);
					}
					$title = $goods_info[$k]['title'];
					if(!empty($key)) {
						$title = "{$title}({$option['name']})";
					}
					$cart_goods[$k][$key] = array(
						'cid' => $goods_info[$k]['cid'],
						'goods_id' => $k,
						'thumb' => tomedia($goods_info[$k]['thumb']),
						'title' => $title,
						'option_title' => $option['name'],
						'num' => $num,
						'price' => $option['price'],
						'discount_price' => $option['price'],
						'discount_num' => 0,
						'price_num' => $num,
						'total_price' => round($option['price'] * $num, 2),
						'total_discount_price' => round($option['price'] * $num, 2),
						'bargain_id' => 0
					);
					$total_num += $num;
					$total_price += $option['price'] * $num;
					$total_original_price += $option['price'] * $num;
					$total_box_price += $goods_box_price * $num;
					if($goods_info[$k]['options'][$option_id]['total'] != -1) {
						$goods_info[$k]['options'][$option_id]['total'] -= $num;
						$goods_info[$k]['options'][$option_id]['total'] = max($goods_info[$k]['options'][$option_id]['total'], 0);
					}
				}
			}
		}
	}
	$goods_item = $goods_info[$goods_id];
	$goods_item['options_data'] = goods_build_options($goods_item);
	$cart_item = $cart['data'][$goods_id][$option_key];
	if($sign == '+') {
		if(!goods_is_available($goods_info[$goods_id])) {
			return error(-1, '当前商品不在可售时间范围内');
		}
		$option = $goods_item['options_data'][$option_key];
		if(empty($option['total'])) {
			return error(-1, '库存不足');
		}
		if(empty($cart_item)) {
			$title = $goods_item['title'];
			if(!empty($option_key)) {
				$title = "{$title}({$option['name']})";
			}
			$cart_item = array(
				'cid' => $goods_info[$goods_id]['cid'],
				'child_id' => $goods_info[$goods_id]['child_id'],
				'goods_id' => $goods_id,
				'thumb' => tomedia($goods_info[$goods_id]['thumb']),
				'title' => $title,
				'option_title' => $option['name'],
				'num' => 0,
				'price' => $option['price'],
				'discount_price' => $option['price'],
				'discount_num' => 0,
				'price_num' => 0,
				'total_price' => 0,
				'total_discount_price' => 0,
				'bargain_id' => 0
			);
		}
		$price_change = 0;
		$price = $option['price'];
		if(in_array($goods_id, array_keys($bargain_goods_ids))) {
			//属于特价商品
			$goods_bargain_id = $bargain_goods_ids[$goods_id];
			$bargain = $bargains[$goods_bargain_id];
			$bargain_goods = $bargain['goods'][$goods_id];
			$msg = '';
			$pricenum = get_cart_goodsnum($goods_id, '-1', 'price_num', $cart_goods);
			if($bargain_goods['poi_user_type'] == 'new' && !$_W['member']['is_store_newmember']) {
				if(!$pricenum) {
					$msg = "仅限门店新用户优惠";
				}
				$price_change = 1;
				$price = $option['price'];
			}
			if(!$price_change && $bargain['avaliable_order_limit'] <= 0) {
				if(!$pricenum) {
					$msg = "{$bargain['title']}活动每天限购一单,超出后恢复原价";
				}
				$price_change = 1;
				$price = $option['price'];
			}
			if(!$price_change && count($bargain_has_goods) == $bargain['goods_limit'] && !in_array($goods_id, $bargain_has_goods)) {
				if(!$pricenum) {
					$msg = "{$bargain['title']}每单特价商品限购{$bargain['goods_limit']}种,超出后恢复原价";
				}
				$price_change = 1;
				$price = $option['price'];
			}
			if(!$price_change) {
				if(!$pricenum && get_cart_goodsnum($goods_id, '-1', 'discount_num', $cart_goods) == $bargain_goods['max_buy_limit']) {
					$msg = "{$bargain['title']}每单特价商品限购{$bargain_goods['max_buy_limit']}份,超出后恢复原价";
				}
				if($cart_item['discount_num'] > $bargain_goods['available_buy_limit']) {
					$price_change = 1;
					$price = $option['price'];
				}

				if($bargain_goods['discount_available_total'] != -1 && $bargain_goods['discount_available_total'] == 0) {
					if(!$pricenum) {
						$msg = "活动库存不足,恢复原价购买";
					}
					$price_change = 1;
					$price = $option['price'];
				}
			}
			if(!$price_change) {
				$price_change = 2;
				$price = $bargain_goods['discount_price'];
				$cart_bargain[] = $bargain['use_limit'];
			}
		}
		if($price_change == 2) {
			//折扣价购买
			$cart_item['discount_num']++;
			$cart_item['bargain_id'] = $bargain['id'];
			$cart_item['discount_price'] = $bargain_goods['discount_price'];
		} else {
			$cart_item['price_num']++;
		}
		$cart_item['num']++;
		$cart_item['total_discount_price'] = $cart_item['discount_num'] * $bargain_goods['discount_price'] + $cart_item['price_num'] * $option['price'];
		$cart_item['total_discount_price'] = round($cart_item['total_discount_price'], 2);
		$cart_item['total_price'] = round($cart_item['num'] * $option['price'], 2);
		$total_num++;
		$total_box_price = $total_box_price + $goods_item['box_price'];
		$total_price = $total_price + $price;
	} else {
		if(!empty($cart_item) && $cart_item['num'] > 0) {
			$cart_item['num']--;
			$price = $cart_item['price'];
			if($cart_item['price_num'] > 0) {
				$cart_item['price_num']--;
			} elseif($cart_item['discount_num'] > 0) {
				$price = $cart_item['discount_price'];
				$cart_item['discount_num']--;
				if($cart_item['discount_num'] <= 0) {
					$cart_item['bargain_id'] = 0;
				}
			}
			$cart_item['total_price'] = round($cart_item['num'] * $cart_item['price']);
			$cart_item['total_discount_price'] = $cart_item['discount_num'] * $cart_item['discount_price'] + $cart_item['price_num'] * $cart_item['price'] ;
			$cart_item['total_discount_price'] = round($cart_item['total_discount_price'], 2);
			$total_num--;
			$total_box_price = $total_box_price - $goods_item['box_price'];
			$total_price = $total_price - $price;
		}
	}
	if($total_box_price > 0) {
		$cart_goods['88888'] = array(
			'0' =>  array(
				'num' => 0,
				'title' => '餐盒费',
				'goods_id' => '88888',
				'discount_num' => 0,
				'price_num' => 0,
				'price_total' => $total_box_price,
				'total_discount_price' => $total_box_price,
			)
		);
	}
	if($sign) {
		$cart_goods[$goods_id][$option_key] = $cart_item;
		if($sign == '-') {
			foreach($cart_goods[$goods_id] as $key => &$item) {
				if(!$item['num']) {
					unset($cart_goods[$goods_id][$key]);
				}
			}
			$item_total_num = get_cart_goodsnum($goods_id, -1, 'num', $cart_goods);
			if(!$item_total_num) {
				unset($cart_goods[$goods_id]);
			}
		}
	}
	$isexist = pdo_fetchcolumn('SELECT id FROM ' . tablename('tiny_wmall_order_cart') . " WHERE uniacid = :aid AND sid = :sid AND uid = :uid", array(':aid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $_W['member']['uid']));
	$cart_goods_original = array();
	foreach($cart_goods as $key => $row) {
		$cart_goods_original[$key] = array(
			'title' => $goods_info[$key]['title'],
			'goods_id' => $key,
			'options' => $row
		);
	}
	$data = array(
		'uniacid' => $_W['uniacid'],
		'sid' => $sid,
		'uid' => $_W['member']['uid'],
		'groupid' => $_W['member']['groupid'],
		'num' => $total_num,
		'price' => $total_price,
		'box_price' => round($total_box_price, 2),
		'data' => iserializer($cart_goods),
		'original_data' => iserializer($cart_goods_original),
		'addtime' => TIMESTAMP,
		'bargain_use_limit' => 0,
	);
	if(!empty($cart_bargain)) {
		$cart_bargain = array_unique($cart_bargain);
		if(in_array(1, $cart_bargain)) {
			$data['bargain_use_limit'] = 1;
		}
		if(in_array(2, $cart_bargain)) {
			$data['bargain_use_limit'] = 2;
		}
	}
	if(empty($isexist)) {
		pdo_insert('tiny_wmall_order_cart', $data);
	} else {
		pdo_update('tiny_wmall_order_cart', $data, array('uniacid' => $_W['uniacid'], 'id' => $isexist, 'uid' => $_W['member']['uid']));
	}
	if(empty($bargain_has_goods)) {
		$discount_notice = array();
		$store_discount = pdo_get('tiny_wmall_store_activity', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'type' => 'discount', 'status' => 1), array('title', 'data'));
		if(!empty($store_discount)) {
			$discount_notice['note'] = $store_discount['title'];
			if($data['price'] > 0) {
				$discount_condition = iunserializer($store_discount['data']);
				$apply_price = array_keys($discount_condition);
				sort($apply_price);
				foreach($apply_price as $key => $val) {
					if($data['price'] > $val) {
						if($apply_price[$key+1]) {
							continue;
						}
					}
					$dvalue = $val - $data['price'];
					if($dvalue <= 5 || $key > 0) {
						$discount_notice['leave_price'] = $dvalue;
						$discount_notice['back_price'] = $discount_condition[$val]['back'];
						$discount_notice['note'] = $dvalue > 0 ? "再买 {$dvalue} 元, 可减 {$discount_notice['back_price']} 元" : "下单减 {$discount_notice['back_price']} 元";
						if($key > 0 && $dvalue > 0) {
							$discount_notice['note'] = "下单减 {$discount_condition[$apply_price[$key-1]]['back']} 元 " . $discount_notice['note'];
						}
						if($apply_price[$key+1] > $data['price']) {
							if($dvalue <= 0) {
								$furdiscount = $apply_price[$key+1] - $data['price'];
								$discount_notice['note'] .= ", 再买 {$furdiscount} 元可减 {$discount_condition[$apply_price[$key+1]]['back']}";
							}
							break;
						}
					}
					if($dvalue > 5) {
						break;
					}
				}
			}
		}
	}
	$data['discount_notice'] = $discount_notice;
	$data['data'] = $cart_goods;
	$data['original_data'] = array_values($cart_goods_original);
	$result = array(
		'cart' => $data,
		'msg' => $msg
	);
	return error(0, $result);
}

function iorder_check_member_cart($sid, $cart = array()) {
	global $_W;
	if(empty($cart)) {
		$cart = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_order_cart') . " WHERE uniacid = :aid AND sid = :sid AND uid = :uid", array(':aid' => $_W['uniacid'], ':sid' => $sid, ':uid' => $_W['member']['uid']));
		if(empty($cart)) {
			return error(-1, '购物车为空1');
		}
	}
	$cart['data'] = iunserializer($cart['data']);
	if(empty($cart['data'])) {
		return error(-1, '购物车为空3');
	}
	$errno = 0;
	$errmessage = '';
	$goods_ids = implode(',', array_keys($cart['data']));
	$goods_info = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods') ." WHERE uniacid = :uniacid AND sid = :sid AND id IN ($goods_ids)", array(':uniacid' => $_W['uniacid'], ':sid' => $sid), 'id');
	foreach($cart['data'] as $goods_id => $cart_item) {
		if(!empty($errno)) {
			break;
		}
		$goods = $goods_info[$goods_id];
		if(!$goods_info[$goods_id]['is_options']) {
			$option_item = $cart_item[0];
			if($option_item['discount_num'] > 0 && 0) {
				$bargain = pdo_get('tiny_wmall_activity_bargain', array('uniacid' => $_W['uniacid'], 'id' => $option_item['bargain_id'], 'sid' => $sid, 'status' => '1'));
				if(empty($bargain)) {
					$errno = -3; //特价活动已结束
					$errmessage = "特价活动{$bargain['title']}已结束！";
					break;
				}
				$bargain_goods = pdo_get('tiny_wmall_activity_bargain_goods', array('uniacid' => $_W['uniacid'], 'bargain_id' => $option_item['bargain_id'], 'goods_id' => $goods_id));
				if($bargain_goods['discount_available_total'] != -1 && $option_item['discount_num'] > $bargain_goods['discount_available_total']) {
					$errno = -4; //特价商品库存不足
					$errmessage = "参与特价活动{$bargain['title']}的{$goods['title']}库存不足！";
					break;
				}
			} else {
				if($goods['total'] != -1 && $option_item['num'] > $goods['total']) {
					$errno = -2; //商品库存不足
					$errmessage = "{$option_item['title']}库存不足！";
					break;
				}
			}
		} else {
			foreach($cart_item as $option_id => $option_item) {
				$option = pdo_get('tiny_wmall_goods_options', array('uniacid' => $_W['uniacid'], 'id' => $option_id));
				if(empty($option)) {
					continue;
				}
				if($option['total'] != -1 && $cart_item['num'] > $option['total']) {
					$errno = -2; //商品库存不足
					$errmessage = "{$option_item['title']}库存不足！";
					break;
				}
			}
		}
	}
	if(!empty($errno)) {
		return error($errno, $errmessage);
	}
	return $cart;
}
