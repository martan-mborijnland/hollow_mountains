<?php

return [
    'db' => [
        'host' => 'localhost',
        'dbname' => 'DBNAME',
        'username' => 'root',
        'password' => '',
        'driver' => 'mysql',
        'port' => 3306,
    ],

	'Security' => [
		'password' => [
			'salt' => "xxxxxxxxxxxxxxxx",
			'pepper' => "xxxxxxxxxxxxxxxx",
			'algorithm'=> CRYPT_SHA256
		]
	]
];