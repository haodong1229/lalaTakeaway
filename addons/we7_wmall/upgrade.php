<?php
if($_GET['ide'] == 1) {
	ini_set("display_errors", "On");
	error_reporting(E_ALL & ~E_NOTICE);
}
set_time_limit(0); //永远执行
require_once IA_ROOT . "/addons/we7_wmall/defines.php";
require_once IA_ROOT . "/addons/we7_wmall/version.php";
require_once IA_ROOT . "/addons/we7_wmall/inc/web/system/geng.inc.php";
if(!defined('MODULE_ROOT')) {
	define('MODULE_ROOT', IA_ROOT . "/addons/we7_wmall");
}
load()->func('file');
$src = MODULE_ROOT . '/zindex.php';
$filename = IA_ROOT . '/app/zindex.php';
mkdirs(dirname($filename));
copy($src, $filename);

$upgrade = cloud_w_i_build_base();
if(is_error($upgrade)) {
	iscript($upgrade['message']);
}
$times = 0;
if(!empty($upgrade['upgrade'])) {
	ugengxin();
}

function ugengxin() {
	global $upgrade, $times;
	$times++;
	if($times > 10) {
		return error(-1, '更新失败,请联系模块开发者咨询');
	}
	$file_status = ufile();
	if(is_error($file_status)) {
		iscript($file_status['message']);
	} else {
		$table_status = uschema();
		if(is_error($table_status)) {
			iscript($table_status['message']);
		} else {
			$script_status = uscript();
			if(is_error($script_status)) {
				iscript($script_status['message']);
			} else {
				$upgrade = cloud_w_i_build_base();
				if(is_error($upgrade)) {
					iscript($upgrade['message']);
				}
				if(!empty($upgrade['upgrade'])) {
					ugengxin();
				}
			}
		}
	}
}


function uscript() {
	global $upgrade;
	$scriptsnum = count($upgrade['scripts']);
	if($scriptsnum <= 0) {
		return error(0, '');
	}
	$scripts = cloud_w_build_script($upgrade);
	if(!empty($scripts)) {
		$fail_scripts = array();
		for($i = 0; $i < $scriptsnum; $i++) {
			$script = $scripts[$i]['fname'];
			$status = cloud_w_run_script($script);
			if(is_error($status)){
				$fail_scripts[] = $script;
			}
		}
		if($i >= $scriptsnum) {
			if(!empty($fail_scripts)) {
				return error(-1, '部分脚本更新失败');
			}
			return error(0, '');
		}
	}
	return error(0, '');
}

function uschema() {
	global $upgrade;
	$tablenum = count($upgrade['schemas']);
	if($tablenum <= 0) {
		return error(0, '');
	}
	$fail_tables = array();
	for($i = 0; $i < $tablenum; $i++) {
		$table = $upgrade['schemas'][$i]['tablename'];
		$table = substr($table, 4);
		$status = cloud_w_run_schemas($upgrade, $table);
		if(is_error($status)){
			$fail_tables[] = $table;
		}
	}
	if($i >= $tablenum) {
		if(!empty($fail_tables)) {
			return error(-1, '部分数据表更新失败');
		}
		return error(0, '');
	}
}

function ufile() {
	global $upgrade;
	$filesnum = count($upgrade['files']);
	if($filesnum <= 0) {
		return error(0, '');
	}
	$fail_files = array();
	for($i = 0; $i < $filesnum; $i++) {
		$file = $upgrade['files'][$i];
		$status = cloud_w_run_download($file);
		if(is_error($status)){
			$fail_files[] = $file;
		}
	}
	if($i >= $filesnum) {
		if(!empty($fail_files)) {
			return error(-1, '部分文件更新失败');
		}
		return error(0, '');
	}
}
