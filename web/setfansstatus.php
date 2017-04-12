<?php


global $_GPC, $_W;
$is_back = intval($_GPC['is_back']);
$fansid = intval($_GPC['fansid']);
if (empty($fansid)) {
	message('抱歉，找不到该粉丝！', '', 'error');
}
$fans = pdo_fetch("select from_user from " . tablename('haoman_dpm_fans') . " where id = :id ", array(':id' => $fansid));
$temps = pdo_update('haoman_dpm_messages', array('is_back' => $is_back), array('from_user' => $fans['from_user']));
if ($temps) {
	$temp = pdo_update('haoman_dpm_fans', array('is_back' => $is_back), array('id' => $fansid));
	pdo_update('haoman_dpm_yyyuser', array('is_back' => $is_back), array('from_user' => $fans['from_user']));
}
if ($temp) {
	if ($is_back == 1) {
		message('拉黑成功！', referer(), 'success');
	} else {
		message('取消拉黑成功！', referer(), 'success');
	}
} else {
	if ($is_back == 1) {
		message('拉黑失败！', referer(), 'success');
	} else {
		message('取消拉黑失败！', referer(), 'success');
	}
}