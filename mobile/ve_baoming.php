<?php
global $_GPC, $_W;
$uniacid = $_W['uniacid'];
$display = empty($_GPC['act']) ? 'display' : $_GPC['act'];
$rid = intval($_GPC['id']);



$user_agent = $_SERVER['HTTP_USER_AGENT'];
if (strpos($user_agent, 'MicroMessenger') === false) {

    header("HTTP/1.1 301 Moved Permanently");
    header("Location: {$this->createMobileUrl('other',array('type'=>1,'id'=>$rid))}");
    exit();
}
// 			//网页授权借用开始
//
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
    if (!is_array($cookie) || !isset($cookie['avatar']) || !isset($cookie['openid'])) {
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
//
// 			//网页授权借用结束


$nows =time();
$replys = pdo_fetch("select isbaoming,bm_endtime from " . tablename('haoman_dpm_reply') . " where uniacid = :uniacid AND rid = :rid", array(':uniacid' => $uniacid,':rid' => $rid));

if($replys == false){
    message('抱歉，活动已经结束，下次再来吧！', '', 'error');
}

if($display == "display"){


//            if($replys['isbaoming']!=1){
//                message('该活动没有开启报名', '', 'error');
//            }

		$fans = pdo_fetch("select id,isbaoming from " . tablename('haoman_dpm_fans') . " where uniacid = :uniacid and rid = :rid and from_user = :from_user",array(':uniacid'=>$uniacid,':rid'=>$rid,':from_user'=>$from_user));

		if($fans == false){
        if($replys['isbaoming']!=1){

            message('您还没签到！',$this->createMobileUrl('information', array('id' => $rid,'from_user'=>$from_user)),'success');
        }
        else{

            message('您还没报名！',$this->createMobileUrl('go_baoming',array('id' => $rid,'from_user'=>$from_user)),'error');
        }

		}elseif ($fans['isbaoming']!=1){
        message('您已经签到过了！',$this->createMobileUrl('index',array('id'=>$rid)),'success');
    }
    if($nows<$replys['bm_endtime']&&$fans['isbaoming']==1){
        message('您好，还没到签到时间，请留意！', '', 'error');
    }

    include $this->template('hexiao');
}else{

    $fans = pdo_fetch("select * from " . tablename('haoman_dpm_fans') . " where uniacid = :uniacid and rid = :rid and from_user = :from_user",array(':uniacid'=>$uniacid,':rid'=>$rid,':from_user'=>$from_user));
    if($fans == false){
        if($replys['isbaoming']!=1){
            message('您还没签到！',$this->createMobileUrl('information', array('id' => $rid,'from_user'=>$from_user)),'success');
        }
        else{

            message('您还没报名！',$this->createMobileUrl('go_baoming',array('id' => $rid,'from_user'=>$from_user)),'error');
        }


//                message('您还没报名！',$this->createMobileUrl('go_baoming'),'error');

    }elseif ($fans['isbaoming']!=1){
        message('您已经签到成功！',$this->createMobileUrl('index',array('id'=>$rid)),'error');
    }

    if(intval($_GPC['id']) == intval($fans['rid'])){
        $temp = pdo_update('haoman_dpm_fans', array('isbaoming' => 0), array('id' => $fans['id']));
        $url ="../addons/haoman_dpm/";

        $filename=$url."sign.txt";

        $handle=fopen($filename,"a+");
        $fans['avatar'] =empty($fans['avatar'])?$_W['siteroot']."addons/haoman_dpm/common/item2.jpg":$fans['avatar'];
        $str=fwrite($handle,$fans['from_user']."|".$fans['nickname']."|".$fans['avatar']."|".date('Y/m/d H:i',time())."\n");

        fclose($handle);

    }else{
        message('签到失败，请重新签到！',$this->createMobileUrl('hexiao'),'error');
    }

    if ($temp === false) {
        message('签到失败，请联系主办方',$this->createMobileUrl('hexiao',array('id'=>$rid)),'error');
    } else {
        message('恭喜，已经成功签到！',$this->createMobileUrl('index',array('id'=>$rid)),'success');
    }
}
