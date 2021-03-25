<?php

/**
 * Default setting sample
 * 
 */

return [
    'mysqlMasterHost' => '127.0.0.1',

    'mysqlMasterUser' => 'root',

    'mysqlMasterPwd' => '1',

    'mysqlSlaveHosts' => ['127.0.0.1', 'localhost'],

    'mysqlSlaveUser' => 'root',

    'mysqlSlavePwd' => '1',

    'interval' => 1, // Frequency for update monitor time in seconds

    'averages' => [1, 5, 30], // Latest period slave lagging average time in seconds

    'logFilePath' => 'd:/logs/',

    'logFileName' => 'HeartbeatOne-Monitor.log',

    'mysqlDriver' => 'mysqli',
];

