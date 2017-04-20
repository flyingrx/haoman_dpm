<?php


global $_W, $_GPC;
$rid = intval($_GPC['rid']);
load()->model('reply');
$t = time();
$pindex = max(1, intval($_GPC['page']));
$psize = 20;
//确认有没有商家，没有则创建
$sj = pdo_fetch('select id from'.tablename('haoman_dpm_commission').' where id =1');
if(!$sj){
	$insert = array(
		'id'=>1,
		'uniacid'=>$_W['uniacid'],
		'rid'=>$rid,
		'percentage'=>80,
		'status'=>0,
		'role'=>'商家',
		'createtime'=>time()
	);
	pdo_insert('haoman_dpm_commission',$insert);
}
$sql = 'select * from ' . tablename('haoman_dpm_commission') . 'where uniacid = :uniacid and rid = :rid  LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
$prarm = array(':uniacid' => $_W['uniacid'], ':rid' => $rid);
$list = pdo_fetchall($sql, $prarm);
$count = pdo_fetchcolumn('select count(*) from ' . tablename('haoman_dpm_commission') . 'where uniacid = :uniacid and rid = :rid', $prarm);
$pager = pagination($count, $pindex, $psize);
foreach ($list as $k => $v) {
	$keywords = reply_single($v['rid']);
	$list[$k]['rulename'] = $keywords['name'];
}
load()->func('tpl');
include $this->template('showcommission');