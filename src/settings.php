<?php
	return [
		'settings' => [
			'displayErrorDetails' => true,

			'logger' => [
				'name' => 'slim-app',
				'path' => __DIR__.'/../logs/app.log',
				'timezone' => 'America/Asuncion',
			],

			'renderer' => [
				'template_path' => __DIR__.'/../templates/',
			],
		],
	];
