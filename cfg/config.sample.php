<?php

$aConfig = array(
    'services' => array(
        'cpd.node1'         => array(
            'url'    => 'http://domain/service',

            'code' => array(
                '$caller = new Foo;' .
                '$caller->open($serviceConfig["url"]);',
            ),

            // $caller->execute(':method:', array('data' => 123));
            'method' => 'execute',
            'args'   => array(
                ':method:',
                array(
                    'data' => 123,
                ),
            ),

            'checkCode' => array(
                '$success = ' .
                    'is_array($response) && ' .
                    'isset($response["key"]) && ' .
                    '"value" === $response["key"];'
            ),

            // other service...
        ),
    ),

    'dataAccess' => array(
        'layer'    => 'SQLite',
        'dsn'      => 'sqlite:' . dirname(__FILE__) . '/../db/ping_reports.db',
        'user'     => '',
        'password' => '',
        'options'  => array(
            PDO::ATTR_EMULATE_PREPARES => FALSE,
            PDO::ATTR_ERRMODE          => PDO::ERRMODE_EXCEPTION
        ),
    ),

);
