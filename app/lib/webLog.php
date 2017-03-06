<?php


class webLog {

    private $logfile;    
    private $adminMail;

    function __construct($file = NULL, $adminMail='') {
        if(!$file){
            $file = defined("ALL_LOG")? ALL_LOG : 'log.log';
        }
        if(!$adminMail){
            $this->adminMail = defined("INFO_MAIL")? INFO_MAIL : 'av@itentaro.ru';
        }        
        $this->logfile = $file;
    }

    public function info($str) {
        if(is_array($str) || is_object($str)) {
            $str = print_r($str,true);
        }
        $str = $str . "\n";
        file_put_contents($this->logfile, '[' . date("Y-m-d H:i:s") . '] ' . $str, FILE_APPEND);
    }
    
    public function mail($title,$str) {
        if(is_array($str) || is_object($str)) {
            $str = print_r($str,true);
        }
        $str = $str . "\n";        
        $str = str_replace('<br>', " \r\n ", $str);                  
        $str = str_replace('<hr>', " \r\n  \r\n ", $str); 
        $this->info("Send mail: ".$this->adminMail." Str: ".$str);
        if(filter_var($this->adminMail, FILTER_VALIDATE_EMAIL)){        
            mail($this->adminMail, $title, $str);
        }        
    }
    
}