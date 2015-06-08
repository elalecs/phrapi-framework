<?php defined("PHRAPI") or die("Direct access not allowed!");

$config = [
	'gmt' => '-05:00',
	'locale' => 'es_MX',
	'php_locale' => 'es_MX',
	'timezone' => 'America/Mexico_City',
	'offline' => false,
	'servers' => [
		'YOUR-DOMAIN' => [
			'url' => 'http://YOUR-DOMAIN/YOUR-INSTALL-PATH/',
			'urlssl' => 'https://YOUR-DOMAIN/YOUR-INSTALL-PATH/',
			'db' => [
				[
					'host' => 'localhost',
					'name' => '',
					'user' => '',
					'pass' => ''
				]
			]
		],
	],
	'smtp' => [
		'host' => '',
		'pass' => '',
		'from' => [
			'Contacto' => ''
		]
	],
	'routing' => [

	]
];
