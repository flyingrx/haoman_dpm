<?php


global $_GPC, $_W;
$rid = intval($_GPC['rid']);
$uniacid = $_W['uniacid'];
$from_user = $_W['openid'];

$l_time = time()-600;
$guest_list = pdo_fetchall("SELECT * FROM " . tablename('haoman_dpm_fans') . " WHERE last_time > :l_time and uniacid = :uniacid", array(':l_time' => $l_time, ':uniacid' => $uniacid));
$item_list = pdo_fetchall("SELECT * FROM " . tablename('haoman_dpm_guest') . " WHERE rid = :rid and uniacid = :uniacid and turntable =2  ORDER BY id desc", array(':rid' => $rid, ':uniacid' => $uniacid));
if ($guest_list && $item_list) {
	foreach ($guest_list as $v){
		$one['id'] = $v['id'];
		$one['nickname'] = $v['nickname'];
		$one['avatar'] = $v['avatar'];
		$one['ds_times'] = $v['ds_times'];
		if($v['ds_times']){
			$msg[$v['seat']]['ds_times'] += $v['ds_times'];
		}
		$msg[$v['seat']]['customer'][] = $one;

	}
	foreach ($msg as $index=>$i){
		$two['id'] = $index;
		$two['customer'] = $i['customer'];
		$two['ds_times'] = $i['ds_times']?$i['ds_times']:0;
		$gst_list[]=$two;
	}
	$result = array('error' => 0, 'guest_list' => $gst_list, 'item_list' => $item_list);
} else {
	$result = array('error' => 1);
}

$this->message($result);