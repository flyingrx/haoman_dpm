<?php


global $_GPC, $_W;
$rid = intval($_GPC['id']);
$from_user = $_W['openid'];
$uniacid = $_W['uniacid'];
$last_id = $_GPC['bp_last_id'];
$bpreply = pdo_fetch("SELECT status,isbd,bd_endtime,bd_starttime FROM " . tablename('haoman_dpm_bpreply') . " WHERE uniacid=:uniacid AND rid = :rid LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid));
if ($bpreply['status'] == 1) {
	$list = pdo_fetchall("SELECT * FROM " . tablename('haoman_dpm_messages') . " WHERE rid = :rid and uniacid = :uniacid and status = 1 and is_back !=1 and is_xy !=1 and is_bp =1  and is_bpshow = 1 and bptime >0 and id > :id ORDER BY id DESC limit 20", array(':rid' => $rid, ':uniacid' => $uniacid,':id' => $last_id));
} else {
	$list = pdo_fetchall("SELECT * FROM " . tablename('haoman_dpm_messages') . " WHERE rid = :rid and uniacid = :uniacid and is_back !=1 and is_xy !=1 and is_bp = 1 and is_bpshow = 1 and bptime >0 and id > :id  ORDER BY id DESC limit 20", array(':rid' => $rid, ':uniacid' => $uniacid,':id' => $last_id));
}
if ($bpreply['isbd']) {
	$params = array(':rid' => $rid, ':uniacid' => $_W['uniacid']);
	$where .= ' and createtime>=:createtime1 and createtime<=:createtime2 ';
	$params[':createtime1'] = $bpreply['bd_starttime'];
	$params[':createtime2'] = $bpreply['bd_endtime'];
	$topfans = pdo_fetchall("SELECT id,avatar,pay_total,nickname,sum(pay_total)as pt FROM " . tablename('haoman_dpm_pay_order') . " WHERE uniacid = :uniacid and rid =:rid AND status= 2 " . $where . " GROUP BY from_user  ORDER BY pt DESC limit 5", $params);
}
foreach ($list as &$v) {
	if ($v['wordimg']) {
		$v['wordimg'] = tomedia($v['wordimg']);
	}
}
if ($list) {
	$result = array('isResultTrue' => 1, 'resultMsg' => $list, 'ranking' => $topfans);
	$this->message($result);
} else {
	$result = array('isResultTrue' => 0);
	$this->message($result);
}