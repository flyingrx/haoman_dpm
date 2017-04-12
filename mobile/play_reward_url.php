<?php


global $_GPC, $_W;
$rid = intval($_GPC['rid']);
$uniacid = $_W['uniacid'];
$from_user = $_W['openid'];
$item_list = pdo_fetchall("SELECT * FROM " . tablename('haoman_dpm_guest') . " WHERE rid = :rid and uniacid = :uniacid and turntable =2  ORDER BY id desc", array(':rid' => $rid, ':uniacid' => $uniacid));
$guest_list = pdo_fetchall("SELECT * FROM " . tablename('haoman_dpm_guest') . " WHERE rid = :rid and uniacid = :uniacid and turntable =1 ORDER BY id desc", array(':rid' => $rid, ':uniacid' => $uniacid));
if ($guest_list && $item_list) {
	$result = array('error' => 0, 'guest_list' => $guest_list, 'item_list' => $item_list);
} else {
	$result = array('error' => 1);
}
$this->message($result);