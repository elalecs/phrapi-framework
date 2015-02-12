<?php

# http://www.php.net/manual/en/errorfunc.constants.php
# 32767 in PHP 5.4.x
# 30719 in PHP 5.3.x
#  6143 in PHP 5.2.x
#  2047 previously
error_reporting(6143);
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
ini_set("track_errors", 1);
ini_set('html_errors', 1);

function query($sql) {
	global $db;

	D("SQL: {$sql}");

	return $db->query($sql);
}

require_once 'framework/globals.php';
require_once 'config.php';

header("Content-type: text/html; charset=UTF-8");

if(isset($config['servers'][$_SERVER['SERVER_NAME']])) {
	foreach($config['servers'][$_SERVER['SERVER_NAME']] as $param_key => $param_value) {
		$config[$param_key] = $param_value;
	}
	unset($config['servers']);
} else {
	D("NO SERVER CONFIG!");
}

try {
	$cnx_config = array(
		PDO::ATTR_PERSISTENT => true,
		PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
	);

	if (defined('PDO::MYSQL_ATTR_COMPRESS')) {
		$cnx_config[PDO::MYSQL_ATTR_COMPRESS] = true;
	}

	$db = new PDO(
		"mysql:host={$config['db'][0]['host']};dbname={$config['db'][0]['name']}",
		$config['db'][0]['user'],
		$config['db'][0]['pass'],
		$cnx_config
	);
} catch(PDOException $e) {
	D('Connection failed: ' . $e->getMessage());
}


query("SET CHARACTER SET utf8");
query("SET NAMES utf8");

if (isset($config->app['gmt']) && !empty($config->app['gmt']))
	query("SET time_zone = '{$config->app['gmt']}'");

if (isset($config->app['locale']) && !empty($config->app['locale']))
	query("SET lc_time_names = '{$config->app['locale']}'");

// Hay hospedajes donde difiere el LOCALE de MySQL al de PHP
if (isset($config['php_locale']) && !empty($config['php_locale'])) {
	setlocale(LC_MONETARY | LC_NUMERIC, $config['php_locale']);
}

if (isset($config['timezone']) && !empty($config['timezone'])) {
	date_default_timezone_set($config['timezone']);
}

D(query("SELECT NOW()")->fetchColumn());

D(compact('config'));

phpinfo();