<?php
/*ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);*/

global $_GPC, $_W;
$uniacid = $_W['uniacid'];
load()->model('reply');
load()->func('tpl');
$_GPC['do'] = 'bporderlist';
$rid = $_GPC['rid'];
$pay_type = $_GPC['pay_type'];
$delete = $_GPC['delete'];
$where = '';
$starttime = mktime(0, 0, 0, date('m'), 1, date('Y'));
$endtime = time();
$params = array(':rid' => $rid, ':uniacid' => $_W['uniacid']);
if (!empty($_GPC['status'])) {
	$where .= ' and status=:status';
	$params[':status'] = $_GPC['status'];
}
if (!empty($_GPC['nickname'])) {
	$where .= ' and nickname=:nickname';
	$params[':nickname'] = $_GPC['nickname'];
}
if (!empty($_GPC['time'])) {
	$starttime = strtotime($_GPC['time']['start']);
	$endtime = strtotime($_GPC['time']['end']);
	$where .= " AND createtime >= :starttime AND createtime <= :endtime";
	$params[':starttime'] = $starttime;
	$params[':endtime'] = $endtime;
}

if($_GPC['commission']){

	$cis = pdo_fetchall("select * from " . tablename('haoman_dpm_commission')." order by id desc");
	$cis_sj = pdo_fetchcolumn("select percentage from " . tablename('haoman_dpm_commission')." where id =3");

//	var_dump($cis_sj);exit;
	$where .= ' and isadmin<>:isadmin and status=:status';
	$params[':isadmin']=1;
	$params[':status']=2;


	$guest_id = pdo_fetchcolumn("select id from ".tablename('haoman_dpm_guest')."where name=:name",array(':name'=>$_GPC['name']));
	if($guest_id){
		$where.=' and message='.$guest_id;
	}
	
	$total = pdo_fetchcolumn("select count(id) from " . tablename('haoman_dpm_pay_order') . "  where rid = :rid and uniacid=:uniacid  and pay_type=3" . $where, $params);
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$pager = pagination($total, $pindex, $psize);
	$start = ($pindex - 1) * $psize;
	$limit .= " LIMIT {$start},{$psize}";
	$list = pdo_fetchall("select * from " . tablename('haoman_dpm_pay_order') . " where rid = :rid and uniacid=:uniacid and pay_type=3" . $where . " order by id desc " . $limit, $params);
//var_dump($list);exit;
	/*$v['real_money'] = sprintf("%.2f",$v['pay_total']*$v['num']*0.8);
	$v['bar_fee'] = sprintf("%.2f",$v['money']-$v['real_money']);*/
	$total_money =0;
	$staff_money =0;
	foreach ($list as $v) {
		$v['money'] = sprintf("%.2f",$v['pay_total']*$v['num']);
		
		$one = pdo_fetch('SELECT name,commission FROM ' . tablename('haoman_dpm_guest') . ' WHERE `id` = :id ', array(":id" => $v['message']));
		$v['guest_name']=$one['name'];
		$v['commission']=$one['commission'];
		
		$total_money+=($v['pay_total']*$v['num']);
		$staff_money+=($v['pay_total']*$v['num']*$cis_sj*$v['commission']);
		$all[]=$v;
	}
	$staff_money=$staff_money/10000;
	//第一行的统计
	unset($v);
	foreach ($cis as $idx=>$v){
		$statistics[$idx]['name'] = $v['role'];
		$statistics[$idx]['money'] = $v['percentage']*$total_money/100;
	}

	include $this->template('bpcommission');
	exit;
}

$total = pdo_fetchcolumn("select count(id) from " . tablename('haoman_dpm_pay_order') . "  where rid = :rid and uniacid=:uniacid  and pay_type=:pay_type" . $where . "", $params);
$pindex = max(1, intval($_GPC['page']));
$psize = 20;
$pager = pagination($total, $pindex, $psize);
$start = ($pindex - 1) * $psize;
$limit .= " LIMIT {$start},{$psize}";
$list = pdo_fetchall("select * from " . tablename('haoman_dpm_pay_order') . " where rid = :rid and uniacid=:uniacid and pay_type=:pay_type" . $where . " order by id desc " . $limit, $params);
if ($pay_type == 3) {
	foreach ($list as &$v) {
		$v['bptime'] = pdo_fetchcolumn("select ds_time from " . tablename('haoman_dpm_guest') . " where id=:id ", array(':id' => $v['pay_addr']));
	}
	unset($v);
}
$lists = pdo_fetchall("select * from " . tablename('haoman_dpm_pay_order') . " where rid = :rid and uniacid=:uniacid and pay_type=:pay_type order by id desc ", $params);
$num1 = '';
$num2 = '';
foreach ($lists as $v) {
	if ($v['pay_type'] == $pay_type && $v['status'] == 2) {
		$num1 += $v['pay_total']*$v['num'];
		if ($v['isadmin'] != 1) {
			$num2 += $v['pay_total']*$v['num'];
		}
	}
}

include $this->template('bporderlist');