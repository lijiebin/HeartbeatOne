# HeartbeatOne
PHP 实现的 MySQL 主从（写/读）复制延迟时间监控工具，实现原理与 pt-heartbeat 类似。

## 截图演示
(命令行执行界面)  
![image](https://user-images.githubusercontent.com/11038908/112451930-0050c600-8d91-11eb-8e43-8e13fb217935.png)

(日志文件记录行)  
![image](https://user-images.githubusercontent.com/11038908/112454479-b0bfc980-8d93-11eb-88e6-0500a100dd1f.png)

## 安装&使用

### 安装
下载源码并部署到指定的可与MySQL数据库连接的PHP服务器上。

### 配置
根据设计环境信息修改 `setting.php` 文件
```php
<?php

/**
 * Default setting sample
 * 
 */

return [
    'mysqlMasterHost' => '', // Write server host

    'mysqlMasterUser' => '',

    'mysqlMasterPwd' => '',

    'mysqlSlaveHosts' => [], // 从库 host 数组

    'mysqlSlaveUser' => '', // 从库账户名

    'mysqlSlavePwd' => '',

    'interval' => 1, // 监控频率（单位：秒）

    'averages' => [1, 5, 30], // 最近指定时段的延时平均值(单位：分钟) ，支持多个形式如 [1, 5, 30, 60 ...]

    'logFilePath' => '/val/logs/',

    'logFileName' => 'HeartbeatOne-Monitor',

    'mysqlDriver' => 'mysqli', // 支持 `mysqlli` or `PDO` 驱动
];

```
### 创建监控数据表
导入 `heartbeat.sql` 至待监控的 MySQl 主（写）库。

### 执行

在命令行中运行以下命令即可之看到监控输出并保存日志
```shell
php heartbeat.php
```
对于生产环境可配置为后台运行，不直接输出监控内容。
```shell
nohup php heartbeat.php > /dev/null &
```
## 一些内部细节
- 注意! Log文件记录内容将每24小时被清空。








