<?php


global $_W, $_GPC;
$rid = intval($_GPC['rid']);
load()->model('reply');
$t = time();
$pindex = max(1, intval($_GPC['page']));
$psize = 20;
$sql = 'select id,rid,pici, COUNT( DISTINCT from_user ) as `c` from ' . tablename('haoman_dpm_yyyuser') . 'where uniacid = :uniacid and rid = :rid group by pici order by `id` desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$prarm = array(':uniacid' => $_W['uniacid'], ':rid' => $rid);
$list = pdo_fetchall($sql, $prarm);
$count = pdo_fetchcolumn('select count(*) from ' . tablename('haoman_dpm_yyyuser') . 'where uniacid = :uniacid and rid = :rid group by pici', $prarm);
$pager = pagination($count, $pindex, $psize);
foreach ($list as $k => $v) {
	$keywords = reply_single($v['rid']);
	$list[$k]['rulename'] = $keywords['name'];
}
load()->func('tpl');
include $this->template('yyyshow');