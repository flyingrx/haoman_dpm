<?php

/*global $_GPC, $_W;
$rid = intval($_GPC['id']);
$from_user = $_W['openid'];
$uniacid = $_W['uniacid'];*/

//$vip_detail = pdo_fetchall("select money,level from " . tablename('haoman_dpm_fans_vip'));
$lt_time = time()-1000;
$dam_fans = pdo_fetchall("select * from " . tablename('haoman_dpm_fans') . " where money > 0 and last_time > ".$lt_time." and login_status=0");
if($dam_fans){
    pdo_update('haoman_dpm_fans',array('login_status'=>1),array('money >'=>0,'last_time >'=>$lt_time,'login_status'=>0));
}

foreach ($dam_fans as $v){

    $level = pdo_fetch("select level from " . tablename('haoman_dpm_fans_vip') ." where money < ".$v['money']." order by id DESC limit 1");
    if ($level['level']){
        $one['level'] = $level['level'];
        $one['nickname'] = $v['nickname'];
        $one['avatar'] = $v['avatar'];
        $list[]=$one;
    }
}
if ($list) {
    $result = array('isResultTrue' => 1,  'resultMsg' => $list);
    $this->message($result);
} else {
    $result = array('isResultTrue' => 0);
    $this->message($result);
}
