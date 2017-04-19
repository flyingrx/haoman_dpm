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
	if ($_GPC['percentage'] < 0) {
		message('分佣比例最小值为0的整数，请留意', '', 'error');
	}
	if (!$_GPC['role']) {
		message('请填入角色名称', '', 'error');
	}
	$keywords = reply_single($_GPC['rulename']);

	$other_p = pdo_fetchcolumn("select SUM(percentage) from".tablename('haoman_dpm_commission')."where id <>".$id);
	if($_GPC['percentage']+$other_p>100){
		message('请保证所有角色分佣比例相加不大于100', '', 'error');
	}
	$updata = array('rid' => $_GPC['rulename'], 'uniacid' => $_W['uniacid'], 'percentage' => intval($_GPC['percentage']), 'role' => $_GPC['role'], 'createtime' => time(), 'status' => $_GPC['status']);
	$temp = pdo_update('haoman_dpm_commission', $updata, array('id' => $id));
	message('修改分佣规则成功', $this->createWebUrl('showcommission', array('rid' => $rid)), "success");
} elseif ($operation == 'addad') {
	if ($_GPC['percentage'] <= 0) {
		message('分佣比例最小值为0的整数，请留意', '', 'error');
	}
	if (!$_GPC['role']) {
		message('请填入角色名称', '', 'error');
	}
	$keywords = reply_single($_GPC['rulename']);
	$other_p = pdo_fetchcolumn("select SUM(percentage) from".tablename('haoman_dpm_commission'));
	if($_GPC['percentage']+$other_p>100){
		message('请保证所有角色分佣比例相加不大于100', '', 'error');
	}
	$updata = array('rid' => $_GPC['rulename'], 'uniacid' => $_W['uniacid'], 'percentage' =>  intval($_GPC['percentage']), 'role' =>$_GPC['role'], 'createtime' => time(), 'status' => $_GPC['status']);
	$temp = pdo_insert('haoman_dpm_commission', $updata);
	message('添加分佣规则成功', $this->createWebUrl('showcommission', array('rid' => $rid)), "success");
} elseif ($operation == 'up') {
	$uid = intval($_GPC['uid']);
	if (empty($uid)) {
		message('获取分佣规则ID出错，请刷新后重试', '', 'error');
	}
	$item = pdo_fetch("select * from " . tablename('haoman_dpm_commission') . "  where id=:uid ", array(':uid' => $uid));
	$keywords = reply_single($item['rid']);
	include $this->template('newcommission');
} elseif ($operation == 'del') {
	$uid = intval($_GPC['uid']);
	if (empty($uid)) {
		message('获取分佣规则ID出错，请刷新后重试', '', 'error');
	}
	pdo_delete('haoman_dpm_commission', array('id' => $uid));
	message('删除分佣规则成功', $this->createWebUrl('showcommission', array('rid' => $rid)), "success");
} else {
	include $this->template('newcommission');
}