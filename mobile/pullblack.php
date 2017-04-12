<?php


global $_GPC, $_W;
$fromuser = $_GPC['userid'];
$rid = intval($_GPC['rid']);
$type = intval($_GPC['type']);
$uid = $_GPC['uid'];
$from_user = $_W['openid'];
$uniacid = $_W['uniacid'];
if ($uid == $from_user) {
	$admin = pdo_fetch("select id from " . tablename('haoman_dpm_bpadmin') . "  where admin_openid=:admin_openid and status=0 and rid=:rid", array(':admin_openid' => $from_user, ':rid' => $rid));
	if ($admin) {
		$rule = pdo_fetch("select id,from_user from " . tablename('haoman_dpm_fans') . " where from_user = :from_user ", array(':from_user' => $fromuser));
		if (empty($rule)) {
			$result = array('isResultTrue' => 0, 'msg' => 1);
			echo json_encode($result);
			die;
		}
		pdo_update('haoman_dpm_messages', array('is_back' => 1), array('from_user' => $fromuser));
		pdo_update('haoman_dpm_fans', array('is_back' => 1), array('from_user' => $fromuser));
		$result = array('isResultTrue' => 1, 'msg' => 2);
		echo json_encode($result);
		die;
	}
} else {
	$result = array('isResultTrue' => 0, 'msg' => $type);
	echo json_encode($result);
	die;
}