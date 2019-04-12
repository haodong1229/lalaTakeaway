<?php
/**
 * 啦啦外卖 - 做好用的外卖系统!
 * =========================================================
 * Copy right 2015-2038 太原多讯网络科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.duoxunwl.com/
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : 啦啦外卖团队
 * @客服QQ : 2622178042
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$ta = trim($_GPC['ta']) ? trim($_GPC['ta']) : 'list';

if($ta == 'list') {
	$_W['page']['title'] = '分类列表';
	$condition = ' uniacid = :uniacid AND sid = :sid';
	$params[':uniacid'] = $_W['uniacid'];
	$params[':sid'] = $sid;
	$status = isset($_GPC['status']) ? intval($_GPC['status']) : -1;
	if($status > -1) {
		$condition .= ' and status = :status';
		$params[':status'] = $status;
	}
	$lists = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_goods_category') . ' WHERE ' . $condition . ' ORDER BY displayorder DESC,id ASC', $params, 'id');
	if(!empty($lists)) {
		$ids = implode(',', array_keys($lists));
		$nums = pdo_fetchall('SELECT count(*) AS num,cid FROM ' . tablename('tiny_wmall_goods') . " WHERE uniacid = :uniacid AND cid IN ({$ids}) GROUP BY cid", array(':uniacid' => $_W['uniacid']), 'cid');
	}
	include itemplate('goods/category');
}

if($ta == 'post') {
	$_W['page']['title'] = '编辑分类';
	$id = intval($_GPC['id']);
	$title = trim($_GPC['title']) ? trim($_GPC['title']) : imessage(error(-1, '分组名称不能为空'), '', 'ajax');
	$displayorder = intval($_GPC['displayorder']);
	if($id > 0) {
		$category = pdo_get('tiny_wmall_goods_category', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
		if(empty($category)) {
			imessage(error(-1, '商品分组不存在'), '', 'ajax');
		}
		pdo_update('tiny_wmall_goods_category', array('title' => $title, 'displayorder' => $displayorder), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	} else {
		$insert = array(
			'uniacid' => $_W['uniacid'],
			'sid' => $sid,
			'title' => $title,
			'displayorder' => $displayorder,
			'status' => 1,
		);
		pdo_insert('tiny_wmall_goods_category', $insert);
	}
	imessage(error(0, ''), '', 'ajax');
}

if($ta == 'status') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']);
	pdo_update('tiny_wmall_goods_category', array('status' => $status), array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	$message = ($status == 0 ? '下架成功' : '上架成功');
	imessage(error(0, $message), referer(), 'ajax');
}

if($ta == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_goods_category', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
	pdo_delete('tiny_wmall_goods_category', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'parentid' => $id));
	pdo_delete('tiny_wmall_goods', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'cid' => $id));
	imessage(error(0, '删除分组成功'), referer(), 'ajax');
}

