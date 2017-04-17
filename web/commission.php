<?php

global $_W ,$_GPC;checklogin();$uniacid =$_W['uniacid'];$rid =$_GPC['rid'];$_GPC['do']='commission';load()->model('reply');load()->func('tpl');$sql ="uniacid = :uniacid and `module` = :module";$params =array();$params[':uniacid'] =$_W['uniacid'];$params[':module'] ='haoman_dpm';$rowlist =reply_search($sql, $params);
$t = 'commission';
$c='分佣规则';
$identity['url']= $this->createWebUrl('commission');
include $this->template('general');