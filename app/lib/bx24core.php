<?php

class bx24core {
    private $log;
    
    public function __construct() {
        $this->log = new webLog();
    }
    
    public function log($msg) {
        $this->log->info($msg);
    }
    
    public function checkAuth() {
        if(isset($_REQUEST['auth'])){
            $auth = $_REQUEST['auth'];
            $domain = isset($auth['domain']) ? $auth['domain']: '';
            $token  = isset($auth['application_token']) ? $auth['application_token']: '';
            if($domain == BX24_DOMEN && $token == BX24_TOKEN){
                return true;
            }
        }
        return false;
    }
    
    public function getData(){
        if(self::checkAuth() && isset($_REQUEST['data'])){
            return $_REQUEST['data'];
        }
        return null;
    }
    
    public function getEvent(){
        if(self::checkAuth() && isset($_REQUEST['event'])){
            return $_REQUEST['event'];
        }
        return 'ERROR';
    }    
}
