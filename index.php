<?php
/**
 * PHP Raccoon API Framework
 *
 * @author Alejandro Galindo, twitter.com/elalecs
 * @copyright Tecnologías Web de México S.A. de C.V.
 * @version 1.0 (Diciembre 2013)
 */

include_once 'framework/globals.php';

// Sin config no hay servicio
if(!file_exists(PHRAPI_PATH . DS . 'config.php')) {
	D("500: no existe el archivo de configuración" . PHRAPI_PATH . DS . 'config.php');
	status_code();
	die;
}

include_once PHRAPI_PATH . DS . 'config.php';

// Sin config no hay servicio
if (!isset($config)) {
	D("500: configuración no definida");
	status_code();
	die;
}

// Servicio apagado?
if (isset($config['offline']) && $config['offline']) {
	status_code(503);
	die;
}

// Se pone en raís de $config las variables específicas por nombre de servidor
if(isset($config['servers'][$_SERVER['SERVER_NAME']])) {
	foreach($config['servers'][$_SERVER['SERVER_NAME']] as $param_key => $param_value) {
		$config[$param_key] = $param_value;
	}
	unset($config['servers']);
}

if (!isset($config['url'])) {
	$config['url'] = $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . "/";
}

$config['uid'] = md5($config['url']);

$config['base_path'] = PHRAPI_PATH;
$config['controllers_path'] = $config['base_path'] .  "controllers" . DS;

// Hay hospedajes donde difiere el LOCALE de MySQL al de PHP
if (isset($config['php_locale']) && !empty($config['php_locale'])) {
	setlocale(LC_MONETARY | LC_NUMERIC, $config['php_locale']);
}

if (isset($config['timezone']) && !empty($config['timezone'])) {
	date_default_timezone_set($config['timezone']);
}

$session_id = getValueFrom($_GET, 'SSID', '');

if (!empty($session_id)) {
	session_write_close();
	session_id($session_id);
}

$factory = new Factory();
