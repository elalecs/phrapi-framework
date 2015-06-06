<?php defined("PHRAPI") or die("Direct access not allowed!");

$config = array(
	'gmt' => '-05:00',
	'locale' => 'es_MX',
	'php_locale' => 'es_MX',
	'timezone' => 'America/Mexico_City',
	'offline' => false,
	'servers' => array(
		'YOUR-DOMAIN' => array(
			'url' => 'http://YOUR-DOMAIN/YOUR-INSTALL-PATH/',
			'urlssl' => 'https://YOUR-DOMAIN/YOUR-INSTALL-PATH/',
			'db' => array(
				array(
					'host' => 'localhost',
					'name' => '',
					'user' => '',
					'pass' => ''
				)
			)
		),
	),
	'smtp' => array(
		'host' => '',
		'pass' => '',
		'from' => array(
			'Contacto' => ''
		)
	),
	'routing' => array(

	)
);
