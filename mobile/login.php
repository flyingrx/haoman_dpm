<?php


global $_GPC, $_W;
$uniacid = $_W['uniacid'];
$rid = intval($_GPC['id']);
$reply = pdo_fetch("SELECT * FROM " . tablename('haoman_dpm_reply') . " WHERE uniacid=:uniacid AND rid = :rid LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid));
$video = pdo_fetch("SELECT vodio_bg1 FROM " . tablename('haoman_dpm_mp4') . " WHERE uniacid=:uniacid AND rid = :rid LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid));
include $this->template('dpm_login');