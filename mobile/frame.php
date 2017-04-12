<?php


global $_GPC, $_W;
$uniacid = $_W['uniacid'];
$rid = intval($_GPC['rid']);
$reply = pdo_fetch("SELECT * FROM " . tablename('haoman_dpm_reply') . " WHERE uniacid=:uniacid AND rid = :rid LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid));
$yyyreply = pdo_fetch("SELECT isyyy FROM " . tablename('haoman_dpm_yyyreply') . " WHERE uniacid=:uniacid AND rid = :rid LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid));
$bpreply = pdo_fetch("SELECT * FROM " . tablename('haoman_dpm_bpreply') . " WHERE uniacid=:uniacid AND rid = :rid LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid));
$xysreply = pdo_fetch("SELECT * FROM " . tablename('haoman_dpm_xysreply') . " WHERE uniacid=:uniacid AND rid = :rid LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid));
$xyhreply = pdo_fetch("SELECT is_xysjh,is_xyh FROM " . tablename('haoman_dpm_xyhreply') . " WHERE uniacid=:uniacid AND rid = :rid LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid));
$cjxreply = pdo_fetch("SELECT isCjxStart FROM " . tablename('haoman_dpm_cjxreply') . " WHERE uniacid=:uniacid AND rid = :rid LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid));

if ($reply['timenum'] == 0) {
	$url = $this->createMobileUrl('dpm_index', array('rid' => $rid));
} else {
	$url = $this->createMobileUrl('dpm_messages', array('rid' => $rid));
}
load()->model('reply');
$keywords = reply_single($rid);
include $this->template('dpm_frame');