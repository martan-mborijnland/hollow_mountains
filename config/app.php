<?php

return [
    'db' => [
        'host' => 'localhost',
        'dbname' => '',
        'username' => 'root',
        'password' => '',
        'driver' => 'mysql',
        'port' => 3306,
    ],

	'Security' => [
		'password' => [
			'salt' => "PASSWORD_SALT",
			'pepper' => "PASSWORD_PEPPER",
			'algorithm'=> CRYPT_SHA256
		]
	]
];