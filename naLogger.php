<?php
/**
 * A simple, feature-supporting logging class.
 *
 * Originally written for notesafe. (http://www.notesafe.de)
 *
 * Usage:
 * $log = new naLogger('/etc/log.txt', naLogger::INFO);
 * $logger->logCrit('Diskspace under 2MB.', 'Drive'); // => 2013-01-20 19:27:29 - CRITCICAL --> [Drive] Diskspace under 2MB.
 * $logger->logErr('Could not upload file: "test_04.pdf"', 'Uploader'); //Prints to the log file
 * $logger->logDebug('User-Hash: 838hshf82bd01()', 'Usermodule'); //Prints nothing due to current severity threshhold
 *
 * @author  Kevin B. <reflic@notesafe.de>
 * @since   January 20, 2013 — Last update January 20, 2013
 * @link    http://www.fundstücke-im-netz.de
 * @version 1.0.0
 */

/**
 * Class documentation
 */
class naLogger {

    // +++ Class Constants +++
    /**
     * Error severity, from low to high. From BSD syslog RFC, secion 4.1.1
     * @link http://www.faqs.org/rfcs/rfc3164.html
     */
    const EMERG  = 0;  // Emergency: system is unusable
    const ALERT  = 1;  // Alert: action must be taken immediately
    const CRIT   = 2;  // Critical: critical conditions
    const ERR    = 3;  // Error: error conditions
    const WARN   = 4;  // Warning: warning conditions
    const NOTICE = 5;  // Notice: normal but significant condition
    const INFO   = 6;  // Informational: informational messages
    const DEBUG  = 7;  // Debug: debug messages

    /**
    * Custom constant for turning off the logging.
    */
    const OFF = 8;
    // --- Class Constants ---

    // +++ Private Class Variables +++
    /**
    * Valid PHP date() format string for log timestamps
    * @var string
    */
    private static $dateFormat = 'Y-m-d G:i:s';

    /**
    * The logfile path.
    * @var string
    */
    private static $logFilePath = null; 

    /**
    * The limit to which the log should be written.
    * @var const
    */
    private static $severityLimit = null;

    /**
    * The default permissions value for logfiles which are created.
    * @var Octal
    */
    private static $defaultPermissions =  0777;

    /** 
    * The directory seperator
    * @var string
    */
    private static $directorySeperator = '/';

    /**
    * The file resource stream
    * @var File Resource
    */
    private $file = null;
    // --- Private Class Variables ---

    // +++ Constructors and Destructors +++

    /**
    * Class Constructor creates a new naLogger Object which can log messages.
    * 
    * @param string $logfile The logfile, where the logmessages should be stored.
    * @param constant $severity The serverity level which should be stored. 
    */ 
    public function __construct($logfile, $severity)
    {
        if ($severity === self::OFF) {
            return;
        }

        if(!file_exists($logfile) && !is_writeable($logfile)){
            $split = explode(self::$directorySeperator, $logfile);
            if(count($split) > 1){
                array_pop($split);
            
                $path = '';
                foreach ($split as $key => $value) {
                    $path .= $value.self::$directorySeperator;
                }

                if(!mkdir($path, self::$defaultPermissions, true)){
                    throw new Exception("The logfile/logdirectory doesn't exists and could not created.", 1);
                }
                else{
                    self::$logFilePath = $logfile;
                    $this->file = $this->openFile();
                }
            }
            else{
                self::$logFilePath = $logfile;
                $this->file = $this->openFile();
            }
        }
        else{
            self::$logFilePath = $logfile;
            $this->file = $this->openFile();
        }
        self::$severityLimit = $severity;
    }

    /*
    * When the class is destructed the logfile has to be closed.
    */
    public function __destruct()
    {
        if(!self::$severityLimit === self::OFF) {
            $this->closeFile();
        }
    }
    // --- Constructors and Destructors ---

    // +++ Public Methods +++
    /**
    * Core Function which logs a message.
    * @param string $text The message string.
    * @param const $severity The severity level.
    * @param string $feature Optional: The feature name.
    */
    public function log($text, $severity, $feature = '')
    {   
        if(($severity <= self::$severityLimit) && (self::$severityLimit != self::OFF)){
            if(!empty($feature)){$feature = '['.$feature.']';}
            $header = $this->getLineHeader($severity, $feature);

            $this->writeLine($header.' '.$text);    
        }       
    }   

    /**
     * Writes a $line to the log with a severity level of EMERG.
     *
     * @param string $line Information to log
     * @return void
     */
    public function logEmerg($line, $feature)
    {
        $this->log($line, self::EMERG, $feature);
    }


    /**
     * Writes a $line to the log with a severity level of ALERT.
     *
     * @param string $line Information to log
     * @return void
     */
    public function logAlert($line, $feature)
    {
        $this->log($line, self::ALERT, $feature);
    }


    /**
     * Writes a $line to the log with a severity level of CRIT.
     *
     * @param string $line Information to log
     * @return void
     */
    public function logCrit($line, $feature)
    {
        $this->log($line, self::CRIT, $feature);
    }


    /**
     * Writes a $line to the log with a severity level of ERR. Most likely used
     * with E_RECOVERABLE_ERROR
     *
     * @param string $line Information to log
     * @return void
     */
    public function logErr($line, $feature)
    {
        $this->log($line, self::ERR, $feature);
    }


    /**
     * Writes a $line to the log with a severity level of WARN. Generally
     * corresponds to E_WARNING, E_USER_WARNING, E_CORE_WARNING, or 
     * E_COMPILE_WARNING
     *
     * @param string $line Information to log
     * @return void
     */
    public function logWarn($line, $feature)
    {
        $this->log($line, self::WARN, $feature);
    }


    /**
     * Writes a $line to the log with a severity level of NOTICE. Generally
     * corresponds to E_STRICT, E_NOTICE, or E_USER_NOTICE errors
     *
     * @param string $line Information to log
     * @return void
     */
    public function logNotice($line, $feature)
    {
        $this->log($line, self::NOTICE, $feature);
    }


    /**
     * Writes a $line to the log with a severity level of INFO. Any information
     * can be used here, or it could be used with E_STRICT errors
     *
     * @param string $line Information to log
     * @return void
     */
    public function logInfo($line, $feature)
    {
        $this->log($line, self::INFO, $feature);
    }


    /**
     * Writes a $line to the log with a severity level of DEBUG.
     *
     * @param string $line Information to log
     * @return void
     */
    public function logDebug($line, $feature)
    {
        $this->log($line, self::DEBUG, $feature);
    }
    // --- Public Methods ---
    
    // +++ Private Methods +++
    /**
    * Sets the date format string
    * @param string $formatstring A valid PHP date() format string.
    */
    private function setDateFormat($formatstring)
    {
        self::$DateFormat = $formatstring;
    }

    /**
    * Opens the log file and creates it if doesn't exist.
    */
    private function openFile()
    {   
        $resource = fopen(self::$logFilePath, 'a+');
        if(!$resource){
            throw new Exception("The logfile could not opened.", 1);
        }
        else{
            return $resource;
        }
    }

    /**
    * Close the log file.
    */
    private function closeFile()
    {
        if(!fclose($this->file)){
            throw new Exception("The logfile could not closed.", 1);
            
        }
    }


    /**
    * Writes a line to the logfile
    * @param string $line
    */
    private function writeLine($line)
    {
        if(!fwrite($this->file, $line . PHP_EOL)){
            throw new Exception("Can not write to the log file.", 1);
            
        }
    } 


    /**
    * Creates the line header for the diffrent severity levels
    * @param const $severity The severity level.
    * @param string $category The category which should be added.
    * @return string $header The full header
    */
    private function getLineHeader($severity, $category)
    {   
        $time = date(self::$dateFormat);

        switch ($severity) {
            case self::EMERG:
                $header = $time." - EMERGENCY --> ".$category;
            break;

            case self::ALERT:
                $header = $time." - ALERT --> ".$category;
            break;

            case self::CRIT:
                $header = $time." - CRITCICAL --> ".$category;
            break;

            case self::ERR:
                $header = $time." - ERROR --> ".$category;
            break;
            
            case self::WARN:
                $header = $time." - WARNING --> ".$category;
            break;

            case self::NOTICE:
                $header = $time." - NOTICE --> ".$category;
            break;

            case self::INFO:
                $header = $time." - INFORMATION --> ".$category;
            break;

            case self::DEBUG:
                $header = $time." - DEBUG --> ".$category;
            break;
            
            default:
                $header = $time." - LOG --> ".$category;
            break;
        }
        return $header;
    }
    // --- Private Methods ---
}
?>