<?php


global $_GPC, $_W;
$rid = intval($_GPC['rid']);
$uid = $_GPC['uid'];
$from_user = $_W['openid'];
$minid = $_GPC['minid'];
$isAdmin = 0;
if(DEBUG){
	$from_user = '123456';
}
if ($uid == $from_user) {
	$admin = pdo_fetch("select id from " . tablename('haoman_dpm_bpadmin') . "  where admin_openid=:admin_openid and status=0 and rid=:rid ", array(':admin_openid' => $from_user, ':rid' => $rid));
	if ($admin) {
		$isAdmin = 1;
	}
}
$list = pdo_fetchall("SELECT * FROM " . tablename('haoman_dpm_messages') . " WHERE rid = :rid and uniacid = :uniacid and status = 1 and is_back !=1 and is_xy !=1  and id < :id ORDER BY id desc limit 20", array(':rid' => $rid, ':uniacid' => $_W['uniacid'], ':id' => $minid));
$list = array_reverse($list);
$aa = '';
foreach ($list as &$v) {
	$times = date("m-d H:i", $v['createtime']);
	$v['wordimg'] = tomedia($v['wordimg']);
	if (empty($v['avatar'])) {
		$v['avatar'] = "../addons/haoman_dpm/img9/ava_default.jpg";
	}
}
unset($v);
if ($list) {
	$result = array('time' => $times, 'uid' => $uid, 'isMaster' => $isAdmin, 'data' => $list);
} else {
	$result = array('time' => 0, 'uid' => 0, 'isMaster' => 0, 'data' => 0);
}
$this->message($result);