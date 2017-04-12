<?php
global $_GPC, $_W;
$rid = intval($_GPC['rid']);
$uniacid = $_W['uniacid'];
$openid = $_GPC['openid'];
$oid = $_GPC['oid'];
$type = $_GPC['type'];


$user_agent = $_SERVER['HTTP_USER_AGENT'];
if (strpos($user_agent, 'MicroMessenger') === false) {

    header("HTTP/1.1 301 Moved Permanently");
    header("Location: {$this->createMobileUrl('other',array('type'=>1,'id'=>$rid))}");
    exit();
}

//网页授权借用开始

load()->model('account');
$_W['account'] = account_fetch($_W['acid']);
$cookieid = '__cookie_haoman_dpm_201606186_' . $rid;
$cookie = json_decode(base64_decode($_COOKIE[$cookieid]),true);
if ($_W['account']['level'] != 4) {
    $from_user = $cookie['openid'];
    $avatar = $cookie['avatar'];
    $nickname = $cookie['nickname'];
}else{
    $from_user = $_W['fans']['from_user'];
    $avatar = $_W['fans']['tag']['avatar'];
    $nickname = $_W['fans']['nickname'];
}

$code = $_GPC['code'];
$urltype = '';
if (empty($from_user) || empty($avatar) || empty($nickname)) {
    if (!is_array($cookie) || !isset($cookie['avatar']) || !isset($cookie['openid']) || !isset($cookie['nickname'])) {
        $userinfo = $this->get_UserInfo($rid, $code, $urltype);
        $nickname = $userinfo['nickname'];
        $avatar = $userinfo['headimgurl'];
        $from_user = $userinfo['openid'];
    } else {
        $avatar = $cookie['avatar'];
        $nickname = $cookie['nickname'];
        $from_user = $cookie['openid'];
    }
}

//网页授权借用结束

if (empty($from_user)) {
    $this->message(array("success" => 0, "msg" => '获取不到您的OpenID,请从新进入活动页面'), "");
}
if($from_user!=$openid){
    $data = array(
        'success' => 5,
        'msg' => '获取资料出错，请重新进入！',
    );
    echo json_encode($data);
    exit();
}

$now = time();
$reply = pdo_fetch("select * from " . tablename('haoman_dpm_reply') . " where rid = :rid order by `id` desc", array(':rid' => $rid));
if($reply==false){
    $data = array(
        'success' => 5,
        'msg' => '活动还没开启',
    );
    echo json_encode($data);
    exit();
}
if($reply['istoupiao']==1){
    $data = array(
        'success' => 5,
        'msg' => '投票还没开启',
    );
    echo json_encode($data);
    exit();
}
if($reply['tp_starttime']>$now){
    $data = array(
        'success' => 5,
        'msg' => '投票还没开启',
    );
    echo json_encode($data);
    exit();
}
if($reply['tp_endtime']<$now){
    $data = array(
        'success' => 5,
        'msg' => '投票已经结束了',
    );
    echo json_encode($data);
    exit();
}

$fans = pdo_fetch("select id,tp_times from " . tablename('haoman_dpm_fans') . " where rid = '" . $rid . "' and from_user='" . $from_user . "'");

if($fans==false){
    $data = array(
        'success' => 5,
        'msg' => '获取不到您的信息，请重新进入',
    );
    echo json_encode($data);
    exit();
}
if($fans['tp_times']>=$reply['tp_times']&&$reply['tp_times']!=0){
    $data = array(
        'success' => 3,
        'msg' => '没有次数了',
    );
    echo json_encode($data);
    exit();
}

$toupiao = pdo_fetch("select id,get_num from " . tablename('haoman_dpm_toupiao') . " where rid = :rid and id=:id and status =0 order by `id` desc", array(':rid' => $rid,':id'=>$oid));
if($toupiao==false){
    $data = array(
        'success' => 5,
        'msg' => '这个投票已取消',
    );
    echo json_encode($data);
    exit();
}
$tp_data = pdo_fetch("select id from " . tablename('haoman_dpm_tp_log') . " where rid = '" . $rid . "' and from_user='" . $from_user . "' and toupiaoip='" . $oid . "' and tp_number='" . $toupiao['number'] . "'");
if($tp_data){
    $data = array(
        'success' => 1,
        'msg' => '已经投票过了',
    );
    echo json_encode($data);
    exit();
}
else{
    $insert = array(
        'rid' => $rid,
        'uniacid' => $_W['uniacid'],
        'from_user' => $from_user,
        'avatar' => $fans['avatar'],
        'nickname' => $nickname,
        'toupiaoip' => $oid,
        'tp_number' => $toupiao['tp_number'],
        'visitorsip' => CLIENT_IP,
        'visitorstime' => TIMESTAMP,
        'viewnum' => 1,
    );

    $temp = pdo_insert('haoman_dpm_tp_log', $insert);
    if($temp){
        pdo_update('haoman_dpm_toupiao',array('get_num'=>$toupiao['get_num']+1),array('id'=>$toupiao['id']));
        pdo_update('haoman_dpm_fans',array('tp_times'=>$fans['tp_times']+1),array('id'=>$fans['id']));

        $data = array(
            'success' => 0,
            'msg' => $toupiao['get_num']+1,
        );
        echo json_encode($data);
        exit();
    }else{
        $data = array(
            'success' => 5,
            'msg' => '投票失败',
        );
        echo json_encode($data);
        exit();
    }
}