<?php

class bx24core {
    private $log;
    
    public function __construct() {
        $this->log = new webLog();
    }
    
    public function log($msg) {
        $this->log->info($msg);
    }
    
    protected function checkAuth() {
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
    
    private function query($method, $url, $data = null, $jsonDecode = false){
            $curlOptions = array(
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false
            );

            if($method == "POST"){
                    $curlOptions[CURLOPT_POST] = true;
                    $curlOptions[CURLOPT_POSTFIELDS] = http_build_query($data);
            }
            elseif(!empty($data)){
                    $url .= strpos($url, "?") > 0 ? "&" : "?";
                    $url .= http_build_query($data);
            }
            $curl = curl_init($url);
            curl_setopt_array($curl, $curlOptions);
            $result = curl_exec($curl);
            return ($jsonDecode ? json_decode($result, 1) : $result);
    }  
    
    protected function call($method, $params){
            if(BX24_AUTH == 0 ){
                $domain = BX24_DOMEN;
                $method = BX24_USER.'/'.BX24_TOKEN_REST.'/'.$method;
            }
            return $this->query("POST", BX24_PROTOCOL.$domain."/rest/".$method, $params, true);
    }
}
