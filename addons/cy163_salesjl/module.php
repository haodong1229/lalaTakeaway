<?php
/**
 * 模块定义
 *
 * @author 梅小西
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Cy163_salesjlModule extends WeModule {

    public function settingsDisplay($settings) {
        global $_GPC,$_FILES, $_W;
        
        if (checksubmit()) {
            $cfg = array(
				'zfon'=>intval($_GPC['zfon']),
				'iszfjlsh'=>intval($_GPC['iszfjlsh']),
				'agentreg'=>intval($_GPC['agentreg']),
				'agentyaoqing'=>intval($_GPC['agentyaoqing']),
				
				'msmoney'=>$_GPC['msmoney'],
				
				
				'tzmoney'=>$_GPC['tzmoney'],
				'tzyongjin'=>$_GPC['tzyongjin'],
				
				'agentthumb'=>$_GPC['agentthumb'],
				
				'temstyle'=>intval($_GPC['temstyle']),
				
				'mapkey'=>$_GPC['mapkey'],
				
				'dltxhour' => $_GPC['dltxhour'],
				'dltkhour' => $_GPC['dltkhour'],

				'zftxhour' => $_GPC['zftxhour'],
				
				'goodsthumb' => $_GPC['goodsthumb'],
				'indexthumb' => $_GPC['indexthumb'],
				'indexhbstyle'=>intval($_GPC['indexhbstyle']),
				
				'certify_api' => $_GPC['certify_api'],
				'certify_key' => $_GPC['certify_key'],
                'mchid' => $_GPC['mchid'],
                'appid' => $_GPC['appid'],
                'key' => $_GPC['key'],
                'ip' => $_GPC['ip'],
				
				'indextitle' => $_GPC['indextitle'],
				'sharetitle' => $_GPC['sharetitle'],
				'sharethumb' => $_GPC['sharethumb'],
				'sharedes' => $_GPC['sharedes'],
				
				'istplon'=>intval($_GPC['istplon']),
				'nt_order_new' => $_GPC['nt_order_new'],// 订单消息通知
				'tpl_or_fahuo' => $_GPC['tpl_or_fahuo'],// 发货通知
				'agent_tz' => $_GPC['agent_tz'],// 
				'huodong_tz' => $_GPC['huodong_tz'],// 
				'rutuan_tz' => $_GPC['rutuan_tz'],
				'daohuo_tz' => $_GPC['daohuo_tz'],
				
				'txtype'=>intval($_GPC['txtype']),
				
				'present_money' => $_GPC['present_money'],
                'present_money_end' => $_GPC['present_money_end'],
                'txdisaccount' => $_GPC['txdisaccount'],
				'usertxdisaccount' => $_GPC['usertxdisaccount'],
				'refundstock' => $_GPC['refundstock'],
				
				'kdnid' => $_GPC['kdnid'],
				'kdnkey' => $_GPC['kdnkey'],
				
				'gerenfee' => $_GPC['gerenfee'],
				'qiyefee' => $_GPC['qiyefee'],
				'renzhengsm' => $_GPC['renzhengsm'],
				'rztixian'=>intval($_GPC['rztixian']),
				
				'tdtitle' => $_GPC['tdtitle'],
				
				'advon1'=>intval($_GPC['advon1']),
				'advon2'=>intval($_GPC['advon2']),
				'advon3'=>intval($_GPC['advon3']),
				'adv1'=>trim($_GPC['adv1']),
				'adv2'=>trim($_GPC['adv2']),
				'adv3'=>trim($_GPC['adv3']),
				'adv1url'=>trim($_GPC['adv1url']),
				'adv2url'=>trim($_GPC['adv2url']),
				'adv3url'=>trim($_GPC['adv3url']),
				'adv1appid'=>trim($_GPC['adv1appid']),
				'adv2appid'=>trim($_GPC['adv2appid']),
				'adv3appid'=>trim($_GPC['adv3appid']),
				
				'qrshhour1'=>intval($_GPC['qrshhour1']),
				'qrshhour2'=>intval($_GPC['qrshhour2']),
				
				
				'tdthumb' => $_GPC['tdthumb'],
				'tdleft' => $_GPC['tdleft'],
				'tdbottom' => $_GPC['tdbottom'],
				
				'contactthumb' => $_GPC['contactthumb'],
				'contactname' => $_GPC['contactname'],
				'contactaddress' => $_GPC['contactaddress'],
				'contactphone' => $_GPC['contactphone'],
				'jianjie' => $_GPC['jianjie'],
            );
			
			
			if($_W["account"]["type_name"] == "公众号"){
				$cfg['dodatahf'] = $_GPC['dodatahf'];
				$cfg['loadthumb'] = $_GPC['loadthumb'];
			}
			
			if($_W["account"]["type_name"] == "微信小程序"){
				$cfg['xcxxqid'] = $_GPC['xcxxqid'];
				$cfg['moshi'] = intval($_GPC['moshi']);
			}
			
            if ($this->saveSettings($cfg)) {
                message('保存成功', 'refresh');
            }
        }
        load()->func('tpl');
		include $this->template('setting');
    }
}