<?php


global $_GPC, $_W;
$id = intval($_GPC['id']);
$rid = intval($_GPC['rid']);
$status = intval($_GPC['status']);
$credit = $_GPC['awardname'];
$from_user = $_GPC['from_user'];
$reply = pdo_fetch("select * from " . tablename('haoman_dpm_reply') . " where rid = :rid order by `id` desc", array(':rid' => $rid));
$cash = pdo_fetch("select * from " . tablename('haoman_dpm_cash') . " where rid = :rid and id = :id and from_user = :from_user ", array(':rid' => $rid, ':id' => $id, ':from_user' => $from_user));
$fans = pdo_fetch("select * from " . tablename('haoman_dpm_fans') . " where rid = :rid and from_user = :from_user ", array(':rid' => $rid, ':from_user' => $from_user));
if (empty($id)) {
	message('抱歉，传递的参数错误！', '', 'error');
}
$p = array('status' => $status);
if ($status == 1 && $cash['awardname'] == $credit) {
	$record['fee'] = $cash['awardname'] / 100;
	$record['openid'] = $from_user;
	$user['nickname'] = $cash['nickname'];
	$user['fansid'] = $rid . $fans['id'];
	$sendhongbao = $this->sendhb($record, $user);
	if ($sendhongbao['isok']) {
		$temp = pdo_update('haoman_dpm_cash', $p, array('id' => $id));
		if ($temp == false) {
			message('抱歉，刚才操作数据失败！', '', 'error');
		} else {
			message('操作成功！', $this->createWebUrl('cashprize', array('rid' => $_GPC['rid'])), 'success');
		}
		$hbstatus = 2;
	} else {
		message($sendhongbao['error_msg'], '', 'error');
		$hbstatus = 21;
	}
} elseif ($status == 2) {
	$temp = pdo_update('haoman_dpm_cash', $p, array('id' => $id));
	message('成功拒绝！', $this->createWebUrl('cashprize', array('rid' => $_GPC['rid'])), 'success');
} else {
	message('非法操作', '', 'error');
}