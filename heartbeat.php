<?php

/**
 * 
 * @author Jimmy 278636108@qq.com
 * 
 *
 */

error_reporting(E_ALL);

mysqli_report(MYSQLI_REPORT_ALL & ~MYSQLI_REPORT_INDEX); 

class HeartbeatOne
{
    private $mysqlDriver = 'mysqli';
    
    private $mysqlDatabase = 'heartbeat-one';
    
    private $mysqlTable = 'monitor';
    
    private $mysqlMasterHost;
    
    private $mysqlMasterUser;
    
    private $mysqlMasterPwd;
    
    private $mysqlSlaveHosts;
    
    private $mysqlSlaveUser;
    
    private $mysqlSlavePwd;
    
    private $logFilePath = '/var/log';
    
    private $logFileName = 'HeartbeatOne-Monitor';
    
    private $masterDbh;
    
    private $slaveDbhs = [];
    
    private $interval = 1;
    
    private $averages = [1, 5, 30];
    
    private $runningTime = 0;
    
    public function __construct()
    {
        if ( PHP_SAPI !== 'cli' ) {
            exit( "Command line running only!" );
        }
        
        $this->_initSetting();
        
        $this->__connectDB();
        
        $this->_initMasterHost();
        
        while ($this->interval >= 1) {
            $this->runningTime += $this->interval;
            $this->updateMonitorTime();
            $this->checkSlaveTime();
            sleep($this->interval);
        }
    }
    
    public function show()
    {
        
    }
    
    private  function _initSetting()
    {
        $settings = include_once('setting.php');
        foreach ($settings as $k => $v) {
            if ( ! $v) throw new Exception("Invalid value of setting option '{$k}'");
            $this->$k = $v;
        }
    }
    
    private function _initMasterHost()
    {
        $initSql = "INSERT INTO `{$this->mysqlTable}` (master_host, updated) values ('{$this->mysqlMasterHost}', now(3))
            ON DUPLICATE KEY UPDATE updated = now(3)";
        $stmt = $this->masterDbh->prepare($initSql);
        $stmt->execute();
    }
    
    public function updateMonitorTime()
    {
        $updateSql = "UPDATE `{$this->mysqlTable}` SET updated = now(3) WHERE 1";
        $stmt = $this->masterDbh->prepare($updateSql);
        $stmt->execute();
    }
    
    public function checkSlaveTime()
    {
        $checkerRunningStart = microtime(true);
        
        // Convert to mins
        $runningTime = ceil($this->runningTime / 60);
        
        $cliOut = PHP_EOL . "Master: {$this->mysqlMasterHost} (uptime: {$runningTime} mins)" . PHP_EOL;
        
        $checkSql = "SELECT master_host, (now(3) - updated) as lagging FROM `{$this->mysqlTable}` WHERE `master_host` = '{$this->mysqlMasterHost}'";
        
        //2006 mysql has gone
        foreach ($this->slaveDbhs as $slaveHost => $slaveDbh) {
            $fetchStart = microtime(true);
            $stmt = $slaveDbh->prepare($checkSql);
            $stmt->execute();
            
            if ($this->mysqlDriver == 'mysqli') {
                $stmt->bind_result($host, $lagging);
            } else {
                $stmt->bindColumn(1, $host);
                $stmt->bindColumn(2, $lagging);
            }
            
            $stmt->fetch();
            $checkerElapsedTime = round ( (microtime(true) - $checkerRunningStart), 3);
            $laggingTime = round($lagging - $checkerElapsedTime, 3);
            
            foreach ($this->averages as $key => $timeSpan) {
                
                $sampleSize = ceil($timeSpan * 60 / $this->interval);
                
                $this->samples[$timeSpan][] = $laggingTime;
                
                if (count($this->samples[$timeSpan]) > $sampleSize) {
                    array_shift($this->samples[$timeSpan]);
                } 
                
                $count = count($this->samples[$timeSpan]);
                $sampleSum = array_sum($this->samples[$timeSpan]);
                $this->samplesAvgs[$timeSpan] = round($sampleSum / $count, 3);
            }
            
            $avgValues = implode(', ', $this->samplesAvgs);
            
            $logOut = "Slave {$slaveHost}; Delay(seconds): {$laggingTime} ($avgValues)";
            
            $this->_outputLogFile($logOut);
            
            $cliOut .= "\t {$logOut}" . PHP_EOL;
        }
        
        echo $cliOut;
    }
    
    private function __connectDB()
    {
        switch ($this->mysqlDriver) {
            case 'mysqli' :
    
                $this->masterDbh = new mysqli($this->mysqlMasterHost, $this->mysqlMasterUser, $this->mysqlMasterPwd, $this->mysqlDatabase);
                
                foreach ($this->mysqlSlaveHosts as $slaveHost) {
                    $this->slaveDbhs[$slaveHost] = new mysqli($slaveHost, $this->mysqlSlaveUser, $this->mysqlSlavePwd, $this->mysqlDatabase);
                }
                
                break;
                
            case 'PDO' :
            
                $dsn = "mysql:dbname={$this->mysqlDatabase};host={$this->mysqlMasterHost}";
                $this->masterDbh = new PDO($dsn, $this->mysqlMasterUser, $this->mysqlMasterPwd);
                
                foreach ($this->mysqlSlaveHosts as $slaveHost) {
                    $dsn = "mysql:dbname={$this->mysqlDatabase};host={$slaveHost}";
                    $this->slaveDbhs[$slaveHost] = new PDO($dsn, $this->mysqlSlaveUser, $this->mysqlSlavePwd);
                }
                
                break;
                
            default :
                throw new Exception('Invalid [mysqlDriver] option, support `mysqli` or `PDO` driver, please check.');
        }
    }
    
    private function _outputLogFile($lineStr)
    {
        $logFile = $this->logFilePath 
            . $this->logFileName
            . '-'
            . $this->mysqlMasterHost
            . '.log';
        
        if ( ! is_writable($this->logFilePath)) {
            throw new Exception("No permission to wirte in this dir ($this->logFilePath)");
        }
        
        if (filectime($logFile) < time() - 60 * 60 * 24) {
            unlink($logFile);
        }
        
        file_put_contents($logFile, $lineStr . "\r\n", FILE_APPEND);
    }
    
}

$hbOne = new HeartbeatOne();
