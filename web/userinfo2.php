<?php


global $_GPC, $_W;
$op = empty($_GPC['op']) ? 'display' : $_GPC['op'];
$starttime = time() - 604800;
$endtime = time() + 604800;
if ($_W['isajax']) {
	$id = intval($_GPC['fansid']);
	$uniacid = $_W['uniacid'];
	$params = array();
	$guest = pdo_fetch("SELECT * FROM " . tablename('haoman_dpm_guest') . " WHERE uniacid = :uniacid AND id = :id", array(':uniacid' => $uniacid, ':id' => $id));
	$condition = 'uniacid=:uniacid and message = :message and pay_type=:pay_type';
	$params = array(':uniacid' => $_W['uniacid'], ':message' => $guest['id'], ':pay_type' => 3);
	if (!empty($_GPC['datestart'])) {
		$starttime = strtotime($_GPC['datestart']);
		$endtime = strtotime($_GPC['dateend']);
		$condition .= " and createtime >:starttime and createtime <:endtime";
		$params[':starttime'] = $starttime;
		$params[':endtime'] = $endtime;
	}
	$dsdata = pdo_fetchall("select * from " . tablename('haoman_dpm_pay_order') . " where " . $condition . " order by id LIMIT 50 ", $params);
	$dsmoney = pdo_fetch("select SUM(pay_total*num) as most_money FROM " . tablename('haoman_dpm_pay_order') . " WHERE uniacid=:uniacid and message = :message and pay_type=:pay_type", array(':uniacid' => $_W['uniacid'], ':message' => $guest['id'], ':pay_type' => 3));
	$dsmoney = $dsmoney['most_money']?$dsmoney['most_money']:0;
	foreach ($dsdata as &$k) {
		$k['bptime'] = pdo_fetchcolumn('SELECT `ds_time` FROM ' . tablename('haoman_dpm_guest') . ' WHERE `id` = :id ', array(":id" => $k['pay_addr']));
	}
	unset($k);
	include $this->template('userinfo2');
	die;
}