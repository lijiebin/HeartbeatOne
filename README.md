# HeartbeatOne
A MySql master-salve replication lag time checker implementation by PHP more like pt-heartbeat.

## ScreenShot
(command line sample)
![image](https://user-images.githubusercontent.com/11038908/112451930-0050c600-8d91-11eb-8e43-8e13fb217935.png)

(log file sample)
![image](https://user-images.githubusercontent.com/11038908/112454479-b0bfc980-8d93-11eb-88e6-0500a100dd1f.png)

## Istall&Usage
### Install
Download this project zip file and deploy on your any one php machine which can access MySql server.
### Setting
Change the `setting.php` according to your actual situation.
```php
<?php

/**
 * Default setting sample
 * 
 */

return [
    'mysqlMasterHost' => '',

    'mysqlMasterUser' => '',

    'mysqlMasterPwd' => '',

    'mysqlSlaveHosts' => [], // Slaves host of the master

    'mysqlSlaveUser' => '',

    'mysqlSlavePwd' => '',

    'interval' => 1, // Frequency for update monitor time in seconds

    'averages' => [1, 5, 30], // Latest period slave lagging average time in seconds

    'logFilePath' => '/val/logs/',

    'logFileName' => 'HeartbeatOne-Monitor',

    'mysqlDriver' => 'mysqli',
];

```
### Create Monitor Table
Import `heartbeat.sql` to your MySQl master server which you want tracing.

### Running
Just run following command in cli, you can see output data report if everything is ok.
```shell
php heartbeat.php
```
For the production suggest run in background.
```shell
nohup php heartbeat.php > /dev/null &
```








