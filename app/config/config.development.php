<?php

return new Teamlab_Config(array(
    'app' => array(
        'work_dir' => APP_PATH . '/data',
        'sql_dir'  => APP_PATH . '/src/sql/',
    ),
    'db' => array(
        'dsn'  => 'mysql:dbname=mysql;host=127.0.0.1',
        'host' => '127.0.0.1',
        'user' => 'mysql',
        'password' => '',
    ),
    's3' => array(
        'aws_access_key' => '',
        'aws_secret_key' => '',
        'aws_bucket_name' => '',
    ),
    'sftp' => array(
        'remotehost' => '',
        'username'   => '',
        'password'   => '',
    ),

));
