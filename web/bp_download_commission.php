<?php
global $_GPC,$_W;
$rid = intval($_GPC['rid']);

checklogin();



$cis = pdo_fetchall("select * from " . tablename('haoman_dpm_commission')." order by id desc");
$cis_sj = pdo_fetchcolumn("select percentage from " . tablename('haoman_dpm_commission')." where id =3");

$params = array(':rid' => $rid, ':uniacid' => $_W['uniacid']);
$where .= ' and isadmin<>:isadmin and status=:status';
$params[':isadmin']=1;
$params[':status']=2;
if(!empty($_GPC['name'])){
    $guest_id = pdo_fetchcolumn("select id from ".tablename('haoman_dpm_guest')."where name=:name",array(':name'=>$_GPC['name']));
    if($guest_id){
        $where.=' and message='.$guest_id;
    }
}

if (!empty($_GPC['time'])) {
    $starttime = strtotime($_GPC['time']['start']);
    $endtime = strtotime($_GPC['time']['end']);
    $where .= " AND createtime >= :starttime AND createtime <= :endtime";
    $params[':starttime'] = $starttime;
    $params[':endtime'] = $endtime;
}

$list = pdo_fetchall("select * from " . tablename('haoman_dpm_pay_order') . " where rid = :rid and uniacid=:uniacid and pay_type=3" . $where , $params);


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
/*

$list = pdo_fetchall('select * from ' . tablename('haoman_dpm_pay_order') . ' where uniacid = :uniacid and rid = :rid  ORDER BY id ', array(':uniacid' => $_W['uniacid'],':rid'=>$rid));*/

$tableheader = array('序号','工作人员/节目','订单号','金额','打赏人微信昵称','工作人员提成(元)');

foreach ($cis as $v){
    array_push($tableheader,$v['role'].'提成');
}
array_push($tableheader,'创建时间');
//var_dump($all);exit;

$html = "\xEF\xBB\xBF";

foreach ($list as &$row) {

    $row['mobile'] = pdo_fetchcolumn("select mobile from " . tablename('haoman_dpm_fans') . " where from_user = :from_user", array(':from_user' => $row['from_user']));
    $row['realname'] = pdo_fetchcolumn("select realname from " . tablename('haoman_dpm_fans') . " where from_user = :from_user", array(':from_user' => $row['from_user']));
//            $lists['address'] = pdo_fetchcolumn("select address from " . tablename('haoman_dpm_fans') . " where from_user = :from_user", array(':from_user' => $lists['from_user']));
    if($row['pay_type']==3){
        $row['bptime']= pdo_fetchcolumn("select ds_time from " . tablename('haoman_dpm_guest') . " where id = :id", array(':id' => $row['pay_addr']));

        $row['message']= pdo_fetchcolumn("select says from " . tablename('haoman_dpm_guest') . " where id = :id", array(':id' => $row['pay_addr']));

    }

}
unset($row);

foreach ($tableheader as $value) {
    $html .= $value . "\t ,";
}
unset($value);
$html .= "\n";
foreach ($all as $value) {
    $html .= $value['id'] . "\t ,";
    $html .= str_replace('"','',$value['guest_name']) . "\t ,";

    $html .=  $value['transid'] . "\t ,";
    $html .=  $value['money'] . "\t ,";

    $html .=  $value['nickname'] . "\t ,";
    $html .=  sprintf("%.2f",$value['money']*$cis_sj*$value['commission']/10000) . "\t ,";
    foreach ($cis as $rrow){
        $html .=sprintf("%.2f",$value['money']*$rrow['percentage']/100). "\t ,";
    }
    $html .=  date('Y-m-d H:i:s', $value['createtime']) . "\n ";

}
//var_dump($html);exit;

header("Content-type:text/csv");

header("Content-Disposition:attachment;filename=打赏分佣.csv");

//$html = mb_convert_encoding($html, 'gb2312', 'UTF-8');

echo $html;
exit();