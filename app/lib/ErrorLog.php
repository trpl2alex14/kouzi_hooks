<?php
define('HOST_DEV', $_SERVER['REMOTE_ADDR'] == '127.0.0.1');
define('IN_DEV', (HOST_DEV) ? 'On':'Off');
error_reporting(-1);
ini_set('display_errors', IN_DEV);


$errorClass   = new ErrorLog(ERROR_LOG, 1, (HOST_DEV?1:0), (HOST_DEV?0:1), ERROR_MAIL);
$errorMethod  = 'handler';
set_error_handler(array($errorClass, $errorMethod));

class ErrorLog {

    private $errortype = array (
                1            => 'Ошибка',
                2            => 'Опасность',
                4            => 'Синтаксическая ошибка',
                8            => 'Предупреждение',
                16           => 'Ошибка ядра',
                32           => 'Опасность ядра',
                64           => 'Ошибка компилирования',
                128          => 'Опасность компилирования',
                256          => 'Ошибка пользователя',
                512          => 'Опасность пользователя',
                1024         => 'Предупреждение пользователя',
                2048         => 'Предупреждение времени выполнения',
                4096         => 'Фатальная ошибка '
                );

    private $debug;

    private $log;

    private $userMessage;
  
    private $debugMessage;

    private $logMessage;

    private $logFile = '';

    private $mail;

    private $adminMail;

    public function  __construct($file, $log, $debug, $mail = 0, $adminMail = ''){
        if(!file_exists($file)){
            die("Файл $file для логов не найден");
        }
        $this->logFile    = $file;
        $this->log        = $log;
        $this->debug      = $debug;
        $this->mail       = $mail;
        $this->adminMail  = $adminMail;
    }

    public function handler($errno, $errstr, $errfile, $errline){
        $this->userMessage  = USER_ERROR_MSG;
        $this->debugMessage = $this->getDebugMessage($errno, $errstr, $errfile, $errline);
        $this->logMessage   = '['.date('d.m.Y H:i:s', time()).']' . "[".$this->errortype[$errno].
                              "] $errstr in file $errfile on in line $errline \r\n";
        $this->display();
    }

    private function display(){
        echo ($this->debug)? $this->debugMessage : $this->userMessage;
        if ($this->log)
            error_log($this->logMessage, 3, $this->logFile);
 
        if($this->mail){            
            if(!filter_var($this->adminMail, FILTER_VALIDATE_EMAIL))
                die("Нeправильный e-mail");
   
            $this->logMessage .= " \r\n Хост: " . $_SERVER[HTTP_HOST].$_SERVER[PHP_SELF] ;
            mail($this->adminMail, 'Сообщение об ошибке', $this->logMessage);
        }
    }

    private function getDebugMessage($errno, $errstr, $errfile, $errline ){
        $message  = $this->errortype[$errno]." : $errstr в файле $errfile на линии $errline <hr>";
        $message .= $this->highlight_num($errfile);
        $message .= '<hr><small>$_REQUEST:</small><br/>';
        $message .= '<pre>' . print_r($_REQUEST, true) .'</pre><hr>';
        $message .= '<small>$_COOKIE:</small><br/>';
        $message .= '<pre>' . print_r($_COOKIE, true) .'</pre><hr>';
        $message .= '<small>$_SESSION:</small><br/>';
        $message .= isset($_SESSION)? '<pre>' . print_r($_SESSION, true) .'</pre><hr>' : 'Не задан<hr>';
        $message .= '<small>$_FILES:</small><br/>';
        $message .= '<pre>' . print_r($_FILES, true) .'</pre><hr>';
        $message .= '<small>$_SERVER:</small><br/>';
        $message .= '<pre>' . print_r($_SERVER, true) .'</pre><hr>';
        return $message;
    }

    private function highlight_num($file) {
        $lines = implode(range(1, count(file($file))), '<br />');
        $content  = highlight_file($file, true);
        $content .=' <style type="text/css">
                .num {
                float: left;
                color: #444;
                font-size: 11px;    
                font-family: monospace;
                text-align: right;
                margin-right: 3pt;
                padding-right: 3pt;
                border-right: 1px solid #333;}
 
                body {margin: 0px; margin-left: 3px;}
                td {vertical-align: top;}
                code {white-space: nowrap;}
                span { font-size: 11px; }
                </style>';
        return "<table><tr><td class=\"num\">\n$lines\n</td><td>\n$content\n</td></tr></table>";
    }  
}
