<?php defined('SYSPATH') or die('No direct script access.');

class Task_Problem_AnalyzeLogs extends Minion_Task
{

    /**
     * @var array 
     */
    protected $analyzeResults;
    
    /**
     * @var string 
     */
    protected $serverTimezone;
    
    protected function _execute(array $params)
    {
        $this->analyzeResults = [];

        try {
            $this->serverTimezone = $this->getServerTimezone();
        } catch(Exception $e) {
            $this->serverTimezone = new DateTimeZone('UTC');
        }
        
        try {
            $this->analyzeDmesgLog();
        } catch(Exception $e) {
            Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e), null, ['exception' => $e]);
        }
        try {
            $this->analyzeDfh();
        } catch(Exception $e) {
            Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e), null, ['exception' => $e]);
        }
        try {
            $this->analyzeKohanaLogs();
        } catch(Exception $e) {
            Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e), null, ['exception' => $e]);
        }
        
        $this->reportProblems();
        
        sleep(300);
    }

    protected function getSphinxIndexes()
    {
        $indexes = scandir('/home/new_sphinx');
        $trash = ['bin', 'pid', '.', '..'];
        foreach($trash as $t) {
            unset($indexes[array_search($t, $indexes)]);
        } unset($t);
        
        return $indexes;
    }
    
    protected function getServerTimezone()
    {
        return new DateTimeZone(shell_exec('date +%Z | tr -d "\n"'));
    }
    
    protected function analyzeSearchdQueryLog()
    {
        $logFilename = "/var/log/query.log";
        $logLines = file($logFilename);
        foreach($logLines as $logLine) {
            // double star **
            if (preg_match('/^\/\*\s(?P<timestamp>.{28})[^\*]+\*\/\sSELECT\s.+\sWHERE\s.*MATCH\s*\(\s*\'.*\*\*.*\'\s*\).*$/', $logLine, $matches)) {
                $timestamp = DateTime::createFromFormat('D M j H:i:s.u Y', $matches['timestamp'], $this->serverTimezone);
                $timestamp->setTimezone(new DateTimeZone('UTC'));
                $timestamp = $timestamp->getTimestamp();
                $this->storeError($logFilename, $logLine, 0, $timestamp, 'Query containing <strong>**</strong> in MATCH expression means that you are passing empty string to non-strong match, for example, $match = \' @link *\' . $_POST[\'link\'] . \'* \'; where $_POST[\'link\'] is empty string');
            }
        } unset($logLine);
    }

    protected function analyzeDmesgLog()
    {
        $logFilename = "dmesg -T";
        $logLines = explode("\n", shell_exec('dmesg -T'));
        foreach($logLines as $logLine) {
            // OOM killer
            if (preg_match('/^\[(?P<timestamp>.{24})\].*OOM killed process \d+ \(.+\)/', $logLine, $matches)) {
                $description = 'An message <em>OOM killed</em> means server\'s daemons has improper configuration in term of RAM usage';
            }
            // Segmentation fault
            elseif (preg_match('/^\[(?P<timestamp>.{24})\].*segfault /', $logLine, $matches)) {
                $description = 'Segmentation fault means error in native application/module. You need to debug issue using <em>core dumping</em> and <em>gdb</em>.';
            }
            // Hang (TODO: implode to single $logLine)
            elseif (preg_match('/^\[(?P<timestamp>.{24})\].*(blocked for more than|hung_task_timeout_secs|Call Trace\:)/', $logLine, $matches)) {
                $description = 'no description';
            } else {
                $description = null;
            }
            
            if (isset($description)) {
                $timestamp = new DateTime($matches['timestamp'], $this->serverTimezone);
                $timestamp->setTimezone(new DateTimeZone('UTC'));
                $timestamp = $timestamp->getTimestamp();
                $this->storeError($logFilename, $logLine, 0, $timestamp, $description);
            }
        } unset($logLine); unset($description);
    }

    protected function analyzeDfh()
    {
        $disk_free_space = disk_free_space('/') / disk_total_space('/');
        if ($disk_free_space < 0.1) {
            $logFilename = "df -h";
            $logLine = shell_exec('df -h');
            $this->storeError($logFilename, $logLine, 0 == $disk_free_space ? 0 : 1, time(), 'Most high-level applications does not sustain from disk space shortage');
        } unset($logLine); unset($logFilename);
    }

    protected function analyzeKohanaLogs()
    {
        $time = time();
        $logsDir = APPPATH.'logs';
        $todayLogs = $logsDir . '/' . date('Y/m/d', $time) . EXT;
        $yesterdayLogs = $logsDir . '/' . date('Y/m/d', strtotime('yesterday', $time)) . EXT;
        
        $this->analyzeKohanaLogsSingle($todayLogs);
        $this->analyzeKohanaLogsSingle($yesterdayLogs);
    }
    
    protected function analyzeKohanaLogsSingle($logsPath)
    {
        if ( ! file_exists($logsPath)) {
            return;
        }
        
        $logFilename = $logsPath;
        $logLines = file($logsPath);
        foreach($logLines as $i => $logLine) {
            if (preg_match('/^(?P<timestamp>\d\d\d\d-\d\d-\d\d\s\d\d:\d\d:\d\d)\s---\s(?P<level>[^:]+):.*$/', $logLine, $matches) && in_array($matches['level'], ['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR'])) {
                while(preg_match('/^#\d+\s/', $logLines[++$i])) {
                    $logLine .= $logLines[$i];
                }
                $timestamp = new DateTime($matches['timestamp']);
                $timestamp->setTimezone(new DateTimeZone('UTC'));
                $timestamp = $timestamp->getTimestamp();
                $this->storeError($logFilename, $logLine, 1, $timestamp, 'Error in PHP code or runtime error');
            }
        } unset($logLine);
    }
    
    protected function storeError($logFilename, $logLine, $level, $timestamp, $description = null)
    {
        static $server;
        if (null === $server) {
            $server = Kohana::$config->load('init.name');
        }
        
        $this->analyzeResults[] = [
            'server' => $server,
            'filename' => $logFilename,
            'logline' => nl2br($logLine),
            'level' => $level,
            'timestamp' => $timestamp,
            'description' => $description,
        ];
    }
    
    protected function reportProblems()
    {
        if ( ! count($this->analyzeResults)) {
            return;
        }
        
        $this->removeDuplicates();
        
        $gearman = Task_Helper::getStorageClient();
        foreach(array_chunk($this->analyzeResults, 100) as $analyzeResults) {
            $gearman->addTaskBackground('Problem:Report', json_encode($analyzeResults));
            $gearman->runTasks();
        } unset($analyzeResults);
    }
    
    /**
     * Remove duplicate problems
     * 
     * (Wondering how this function would look on Scala - 30-40 characters of concise code?
     * 
     * @return array
     */
    protected function removeDuplicates()
    {
        $count = count($this->analyzeResults);
        if ($count < 2) {
            return;
        }
        
        $i = 0;
        do {
            $currentProblem = $this->analyzeResults[$i];
            $nextProblem = $this->analyzeResults[$i + 1];
            
            while ($currentProblem['filename'] == $nextProblem['filename'] && levenshtein(substr($currentProblem['logline'], 0, 255), substr($nextProblem['logline'], 0, 255)) <= 0.15 * min(strlen($currentProblem['logline']), strlen($nextProblem['logline']))) {
                $i += 1;
                unset($this->analyzeResults[$i]);
                if ($i == $count - 1) {
                    break;
                }
                $nextProblem = $this->analyzeResults[$i + 1];
            } unset($nextProblem);
        } while(++$i < $count - 1); unset($i); unset($count); unset($currentProblem); unset($nextProblem);
        
        $this->analyzeResults = array_values($this->analyzeResults);
    }
    
}