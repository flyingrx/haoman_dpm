<?php


global $_GPC, $_W;
$rid = intval($_GPC['rid']);
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
load()->model('reply');
load()->func('tpl');
$sql = "uniacid = :uniacid and `module` = :module";
$params = array();
$params[':uniacid'] = $_W['uniacid'];
$params[':module'] = 'haoman_dpm';
$rowlist = reply_search($sql, $params);
if ($operation == 'updataad') {
	$id = $_GPC['listid'];
	if ($_GPC['bp_money'] <= 0) {
		message('霸屏金额最小值为0.01元，请留意', '', 'error');
	}
	if ($_GPC['bp_time'] <= 0) {
		message('霸屏时间最小值为1秒，请留意', '', 'error');
	}
	$keywords = reply_single($_GPC['rulename']);
	$updata = array('rid' => $_GPC['rulename'], 'uniacid' => $_W['uniacid'], 'bp_money' => $_GPC['bp_money'], 'bp_time' => intval($_GPC['bp_time']), 'createtime' => time(), 'status' => $_GPC['status']);
	$temp = pdo_update('haoman_dpm_bpmoney', $updata, array('id' => $id));
	message('修改霸屏金额成功', $this->createWebUrl('bapinshow', array('rid' => $rid)), "success");
} elseif ($operation == 'addad') {
	if ($_GPC['bp_money'] <= 0) {
		message('霸屏金额最小值为0.01元，请留意', '', 'error');
	}
	if ($_GPC['bp_time'] <= 0) {
		message('霸屏时间最小值为1秒，请留意', '', 'error');
	}
	$keywords = reply_single($_GPC['rulename']);
	$updata = array('rid' => $_GPC['rulename'], 'uniacid' => $_W['uniacid'], 'bp_money' => $_GPC['bp_money'], 'bp_time' => intval($_GPC['bp_time']), 'createtime' => time(), 'status' => $_GPC['status']);
	$temp = pdo_insert('haoman_dpm_bpmoney', $updata);
	message('添加霸屏金额成功', $this->createWebUrl('bapinshow', array('rid' => $rid)), "success");
} elseif ($operation == 'up') {
	$uid = intval($_GPC['uid']);
	if (empty($uid)) {
		message('获取霸屏金额ID出错，请刷新后重试', '', 'error');
	}
	$item = pdo_fetch("select * from " . tablename('haoman_dpm_bpmoney') . "  where id=:uid ", array(':uid' => $uid));
	$keywords = reply_single($item['rid']);
	include $this->template('updatabapin');
} elseif ($operation == 'del') {
	$uid = intval($_GPC['uid']);
	if (empty($uid)) {
		message('获取奖品ID出错，请刷新后重试', '', 'error');
	}
	pdo_delete('haoman_dpm_bpmoney', array('id' => $uid));
	message('删除霸屏金额成功', $this->createWebUrl('bapinshow', array('rid' => $rid)), "success");
} else {
	include $this->template('newbapin');
}