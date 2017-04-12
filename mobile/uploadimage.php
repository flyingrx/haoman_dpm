<?php


global $_W, $_GPC;
$rid = intval($_GPC['rid']);
load()->func('file');
$reply = pdo_fetch("select * from " . tablename('haoman_dpm_reply') . " where rid = :rid order by `id` desc", array(':rid' => $rid));
if (empty($_FILES['file']['name'])) {
	$result['message'] = '请选择要上传的文件！';
	die(json_encode($result));
}
$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
$ext = strtolower($ext);
if (in_array($ext, array('mp4', 'mp3', 'avi', 'wmv', '3gp', 'rmvb', 'mov'))) {
	if ($file = $this->fileUpload($_FILES['file'], 'audio')) {
		if ($file['error']) {
			die('0');
		}
		$result['url'] = $_W['config']['upload']['attachdir'] . $file['filename'];
		$result['error'] = $file['error'];
		$result['filename'] = $file['filename'];
		$pathname = $result['filename'];
		if (!empty($_W['setting']['remote']['type'])) {
			$remotestatus = file_remote_upload($pathname);
			if (is_error($remotestatus)) {
				$result['msg'] = $remotestatus['message'];
			}
		}
		die(json_encode($result));
	}
} else {
	if ($file = $this->fileUpload($_FILES['file'], 'image')) {
		if ($file['error']) {
			die('0');
		}
		$result['url'] = $_W['config']['upload']['attachdir'] . $file['filename'];
		$result['error'] = $file['error'];
		$result['filename'] = $file['filename'];
		$pathname = $result['filename'];
		if (!empty($_W['setting']['remote']['type'])) {
			$remotestatus = file_remote_upload($pathname);
			if (is_error($remotestatus)) {
				$result['msg'] = $remotestatus['message'];
			}
		}
		die(json_encode($result));
	}
}