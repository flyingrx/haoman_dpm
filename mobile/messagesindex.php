<?php
global $_GPC, $_W;
$rid = intval($_GPC['id']);
$uniacid = $_W['uniacid'];
//$debug=1;
if(DEBUG){
	$nickname = '测试账号';
	$avatar = '/addons/haoman_dpm/static/wedding_xys/lover/heart1.png';
	$from_user = '123456';
	//print_r($rid);exit;
	/*$insert = array(
        'uniacid' => 'sfefsfse',
        'from_user' => '123456',
        'avatar' => 'images/global/avatars/avatar_6.jpg',
        'nickname' => 'dfs',
        'realname' => 'lzd',
        'mobile' => '18792839237',
        'address' => '',
        'rid' => 12,
        'sex' => 1,
        'isbaoming' => 1,
        'is_back' => 0,
        'createtime' => time(),
    );
    pdo_insert('haoman_dpm_fans',$insert);*/

}else{
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
}

//网页授权借用结束


$page_from_user = base64_encode(authcode($from_user, 'ENCODE'));


$fashb = pdo_fetch( " SELECT * FROM ".tablename('haoman_dpm_hb_setting')." WHERE rid='".$rid."' " );

$reply = pdo_fetch("select * from " . tablename('haoman_dpm_reply') . " where rid = :rid order by `id` desc", array(':rid' => $rid));
$xys = pdo_fetch("select isxys from " . tablename('haoman_dpm_xysreply') . " where rid = :rid order by `id` desc", array(':rid' => $rid));
$bp = pdo_fetch("select isbp,isds,bp_pay,bp_pay2,bp_listword,bp_keyword,ishb,isvo from " . tablename('haoman_dpm_bpreply') . " where rid = :rid order by `id` desc", array(':rid' => $rid));
$yyy = pdo_fetch("select isyyy from " . tablename('haoman_dpm_yyyreply') . " where rid = :rid order by `id` desc", array(':rid' => $rid));
if(ISCUSTOM == 1 && CUSTOM_VERSION == 'DS'){
    $dsreply = pdo_fetch("select * from " . tablename('haoman_dpm_ds_reply') . " where rid = :rid order by `id` desc", array(':rid' => $rid));
}
if(ISCUSTOM == 1 && CUSTOM_VERSION == 'ZNL'){
    $znlreply = pdo_fetch( " SELECT * FROM ".tablename('haoman_dpm_znl_reply')." WHERE rid='".$rid."' " );
}

if (empty($reply)) {
	message('非法访问，请重新发送消息进入活动页面！');
}

$bpmoney = pdo_fetchall("select * from " . tablename('haoman_dpm_bpmoney') . " where rid = :rid and uniacid=:uniacid order by `bp_time` asc", array(':rid' => $rid, ":uniacid" => $uniacid));
if($bpmoney){
    $money  =1;
}

//检测是否关注
if (!empty($reply['share_url'])) {
	//查询是否为关注用户
	$fansID = $_W['member']['uid'];
	$follow = pdo_fetchcolumn("select follow from " . tablename('mc_mapping_fans') . " where uid=:uid and uniacid=:uniacid order by `fanid` desc", array(":uid" => $fansID, ":uniacid" => $uniacid));

	if ($follow == 0) {
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: " . $reply['share_url'] . "");
		exit();
	}

}

if(empty($from_user)){
	$from_user ='oJfLAvhM011uRt7SKeI-p1ad0s0M';
}

//检测是否为空
$fans = pdo_fetch("select * from " . tablename('haoman_dpm_fans') . " where rid = '" . $rid . "' and from_user='" . $from_user . "'");
//为空则插入粉丝
if($fans==false){
	$insert = array(
		'uniacid' => $_W['fans']['uniacid'],
		'from_user' => $_W['fans']['from_user'],
		'avatar' => $_W['fans']['avatar'],
		'nickname' => $_W['fans']['nickname'],
		'realname' => $_W['fans']['from_realname'],
		'mobile' => $_W['fans']['mobile'],
		'address' => $_W['fans']['pay_addr'],
		'rid' => $rid,
		'sex' => $_W['fans']['sex'],
		'isbaoming' => 1,
		'is_back' => 0,
		'createtime' => time(),
	);

	pdo_insert('haoman_dpm_fans',$insert);
}

/*if ($fans == false) {
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: " . $this->createMobileUrl('information', array('id' => $rid,'from_user'=>$page_from_user)) . "");
	exit();
} else {


}*/
//增加浏览次数
	pdo_update('haoman_dpm_reply', array('viewnum' => $reply['viewnum'] + 1), array('id' => $reply['id']));
	pdo_update('haoman_dpm_fans',array('last_time'=>time()),array('from_user'=>$from_user));
	//距上次登录时间大于4小时才更新登录状态，以便大屏出现VIP登场效果
	if(time()-$fans['last_time']>14400){
		pdo_update('haoman_dpm_fans',array('login_status'=>0),array('from_user'=>$from_user));
	}

$admin = pdo_fetch("select id,createtime,uses_times from " . tablename('haoman_dpm_bpadmin') . "  where admin_openid=:admin_openid and status=0 and rid=:rid", array(':admin_openid' => $from_user,':rid'=>$rid));

if($admin){
	$nowtime = mktime(0, 0, 0);
	if ($admin['createtime'] < $nowtime) {

		$admin['uses_times'] = 0;

		$temps = pdo_update('haoman_dpm_bpadmin', array('uses_times'=>$admin['uses_times'],'createtime' => time()), array('id' => $admin['id']));

	}
}

$dsDatas = pdo_fetchall("SELECT `name`,`id`,`says`,`ds_time`as`time`,`ds_pic`as`img`,`ds_vodiobg`as`src`,`sort`as`iconName` FROM " . tablename('haoman_dpm_guest') . " WHERE rid = :rid and uniacid = :uniacid and turntable =2 ORDER BY id DESC ",array(':rid'=>$rid,':uniacid'=>$uniacid));


if($dsDatas){
	foreach($dsDatas as $v){
		$res[$v['id']]=$v;
		$arr[]=$v['id'];
	}

	$dsData = json_encode($res);
	$arr = json_encode($arr);
}
else{
	$dsData='123';
	$arr='123';
}

if(!empty($fans['realname'])){
	$nickname = $fans['realname'];
}
//if($bp['isbp']==1||$bp['isds']==1){
//   $isopend =1;
//}else{
//	$isopend =0;
//}

if($bp['bp_keyword']){
	$keywords= explode(',',$bp['bp_keyword']);
	$i=1;

	foreach($keywords as $k=>$v){
		$keyword[$i]=$v;
		$i++;
	}

	$keyword = json_encode($keyword);
}else{
	$keyword ="0";
}

if($bp['bp_listword']){
	$bp_listwords = explode(',',$bp['bp_listword']);
	$i=1;

	foreach($bp_listwords as $k=>$v){
		$bp_listword[$i]=$v;
		$i++;
	}
	$bp_listword = json_encode($bp_listword);
}else{
	$bp_listword = "0";
}




//分享信息
$sharelink = $_W['siteroot'] . 'app/' . $this->createMobileUrl('share', array('rid' => $rid, 'from_user' => $page_from_user));
$sharetitle = empty($reply['share_title']) ? '一起来咻一咻抽奖吧!' : $reply['share_title'];
$sharedesc = empty($reply['share_desc']) ? '亲，一起来咻一咻吧，赢大奖哦！！' : str_replace("\r\n", " ", $reply['share_desc']);
if (!empty($reply['share_imgurl'])) {
	$shareimg = toimage($reply['share_imgurl']);
} else {
	$shareimg = toimage($reply['picture']);
}

if(empty($reply['mobpicurl'])){
	$bg = "../addons/haoman_dpm/mobimg/bg.jpg";
}else{
	$bg = tomedia($reply['mobpicurl']);
}

$jssdk = new JSSDK();
$package = $jssdk->GetSignPackage();
//print_r($reply['ismessage']);exit;
//$reply['ismessage']=0;
if($reply['ismessage']==1){
    include $this->template('mob_newindex');
}else{

    include $this->template('mob_index');
}