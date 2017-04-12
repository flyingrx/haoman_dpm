<?php


global $_GPC, $_W;
$rid = intval($_GPC['rid']);
@header('content-Type: text/html; charset=utf-8');
date_default_timezone_set('PRC');
$pagenum = 200;
$count = pdo_fetchcolumn("SELECT count(id) FROM " . tablename('haoman_dpm_whyerror'));
$page_count = ceil($count / $pagenum);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="红包失败数据.csv"');
header('Cache-Control: max-age=0');
$fp = fopen('php://output', 'a');
$head = array('id', 'openid', '金额', '失败原因', '时间');
foreach ($head as $i => $v) {
	$head[$i] = iconv('utf-8', 'gbk', $v);
}
fputcsv($fp, $head);
for ($i = 0; $i < $page_count; $i++) {
	$data = pdo_fetchall("select * from " . tablename('haoman_dpm_whyerror') . "limit " . $i * $pagenum . ',' . $pagenum);
	foreach ($data as $k => $val) {
		$row = array();
		$row[0] = iconv('utf-8', 'gbk', $val['id']);
		$row[1] = iconv('utf-8', 'gbk', $val['from_user']);
		$row[2] = iconv('utf-8', 'gbk', $val['money']);
		$row[3] = iconv('utf-8', 'gbk', $val['why_error']);
		$row[4] = iconv('utf-8', 'gbk', date('Y-m-d H:i:s', $val['createtime']));
		fputcsv($fp, $row);
	}
	ob_flush();
	flush();
}