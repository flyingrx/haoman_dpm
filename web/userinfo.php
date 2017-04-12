<?php


global $_GPC, $_W;
$op = empty($_GPC['op']) ? 'display' : $_GPC['op'];
$starttime = time() - 604800;
$endtime = time() + 604800;
if ($_W['isajax']) {
	$fansid = intval($_GPC['fansid']);
	$uniacid = $_W['uniacid'];
	$params = array();
	$condition = 'uniacid=:uniacid';
	$params[':uniacid'] = $_W['uniacid'];
	if (!empty($_GPC['datestart'])) {
		$starttime = strtotime($_GPC['datestart']);
		$endtime = strtotime($_GPC['dateend']);
		$condition .= " AND visitorstime >= :starttime AND visitorstime <= :endtime";
		$params[':starttime'] = $starttime;
		$params[':endtime'] = $endtime;
	}
	$fans = pdo_fetch("SELECT * FROM " . tablename('haoman_dpm_toupiao') . " WHERE uniacid = :uniacid AND id = :id", array(':uniacid' => $uniacid, ':id' => $fansid));
	$dsdata = pdo_fetchall("SELECT * FROM " . tablename('haoman_dpm_tp_log') . " WHERE " . $condition . " and toupiaoip='" . $fans['id'] . "' ORDER BY `id` LIMIT 50", $params);
	include $this->template('userinfo');
	die;
}