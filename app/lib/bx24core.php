<?php

class bx24core{
    private $log;
    private $req;
    private $token;
    
    public function __construct() {
        $this->log = new webLog();
        $this->req = new bx24req(BX24_AUTH);
    }
    
    public function log($msg) {
        $this->log->info($msg);
    }
    
    protected function setToken($token) {
        $this->token = $token;
    }
    
    protected function checkAuth() {
        if(isset($_REQUEST['auth'])){
            $auth = $_REQUEST['auth'];
            $domain = isset($auth['domain']) ? $auth['domain']: '';
            $token  = isset($auth['application_token']) ? $auth['application_token']: '';
            if($domain == BX24_DOMEN && preg_match("/".$token."/i",$this->token)){
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
    
    public function call($method, $params){
        return $this->req->call($method, $params);
    }    
}
