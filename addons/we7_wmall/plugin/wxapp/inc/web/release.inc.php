<?php

defined('IN_IA') || exit('Access Denied');
global $_W;
global $_GPC;
mload()->model('cloud');
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
$wxapp_type = trim($_GPC['type']) ? trim($_GPC['type']) : 'we7_wmall';

if ($op == 'index') {
    $_W['page']['title'] = '基础设置';

    if ($_W['ispost']) {
        $content = cloud_w_request('http://renren.weapp.cc/index/lala/checkscan', [
            'code_token' => $_GPC['code_token']
        ]);
        $wxapp = @json_decode($content, true);
        $result = 0;
        if (isset($wxapp['data']['errcode']) && $wxapp['data']['errcode'] == '405') {
            $result = 1;
            cache_write($wxapp_type.'code_uuid', $_GPC['code_token'], 3600);
            cache_write($wxapp_type.'code_token', $wxapp['data']['code_token'], 3600);
        }
        imessage($result, '', 'ajax');
    }
    $code_token = cache_read($wxapp_type.'code_token');
    $wxapp = get_plugin_config('wxapp.basic');
    $wxapp1 = get_plugin_config('wxapp.deliveryer');
    $wxapp2 = get_plugin_config('wxapp.manager');
    $wxapp_skip = get_plugin_config('wxapp.miniProgramAppIdList');
    $tominiprogram = [];
    if ($wxapp_skip) {
        foreach ($wxapp_skip as $v) {
            $tominiprogram[] = $v['appid'];
        }
    }
  
    $menu = get_plugin_config('wxapp.menu');
    $params = [
        'host' => $_SERVER['HTTP_HOST'],
        'uniacid' => $_W['uniacid'],
        'appid' => $wxapp_type == 'we7_wmall_deliveryer' ? $wxapp1['key']: ($wxapp_type == 'we7_wmall_manager' ?$wxapp2['key']:$wxapp['key']),
        'version' => '1.0.0',
        'tominiprogram' => $tominiprogram,
        'menu' => $menu
    ];

    $content = cloud_w_request('http://renren.weapp.cc/index/lala/qrcode', $params);

    $wxapp = @json_decode($content, true);

  
//	$wxapp = cloud_w_get_wxapp_authorize_info($wxapp_type);
//
	if ($wxapp['errno']!= 0) {
		imessage($wxapp['message'], '', 'error');
	}

    include itemplate('release/index');
    exit();
}

if ($op == 'release') {
    $code_uuid = cache_read($wxapp_type.'code_uuid');
    $code_token = cache_read($wxapp_type.'code_token');

    $content = cloud_w_request('http://renren.weapp.cc/index/lala/upload', [
        'code_uuid' => $code_uuid,
        'code_token' => $code_token,
        'type' => $wxapp_type
    ]);
    $wxapp = @json_decode($content, true);
    $result = error($wxapp['errno'] == 0 ? 0 : 1, $wxapp['errno'] == 0 ? '发布成功!' : $wxapp['msg']);
    imessage($result, iurl('wxapp/release/index', array('type' => $wxapp_type)), 'error');

}
if ($op == 'preview') {

    $code_uuid = cache_read($wxapp_type.'code_uuid');
    $code_token = cache_read($wxapp_type.'code_token');

    $content = cloud_w_request('http://renren.weapp.cc/index/lala/preview', [
        'code_uuid' => $code_uuid,
        'code_token' => $code_token,
        'type' => $wxapp_type
    ]);
    $wxapp = @json_decode($content, true);
    $result = error($wxapp['errno'] == 0 ? 0 : 1, $wxapp['errno'] == 0 ? '提交成功!' : $wxapp['msg']);
    imessage($result, iurl('wxapp/release/index', array('type' => $wxapp_type)), 'error');

}
if ($op == 'clear') {

    cache_delete($wxapp_type.'code_uuid');
    cache_delete($wxapp_type.'code_token');
    $result = error(0, '清除成功!');
    imessage($result, iurl('wxapp/release/index', array('type' => $wxapp_type)), 'error');

}

if ($op == 'bind_tester') {
    if ($_W['ispost']) {
        $wechatid = trim($_GPC['wechatid']);

        if (empty($wechatid)) {
            imessage('体验者微信号不能为空', iurl('wxapp/release/index', array('type' => $wxapp_type)), 'error');
        }

        $result = cloud_w_wxapp_bind_tester($wechatid, $wxapp_type);
        imessage($result, iurl('wxapp/release/index', array('type' => $wxapp_type)), 'success');
    }

    include itemplate('release/category');
    exit();
}

if ($op == 'undocodeaudit') {
    $result = cloud_w_wxapp_undocodeaudit($wxapp_type);

    if (is_error($result)) {
        imessage($result, iurl('wxapp/release/index'), 'error');
    }

    imessage(error(0, '撤销审核成功'), iurl('wxapp/release/index', array('type' => $wxapp_type)), 'success');
}

?>
