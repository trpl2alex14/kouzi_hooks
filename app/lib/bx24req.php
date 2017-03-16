<?php

class bx24req {
    private $auth_type;
    private $oauth2;


    public function __construct($at = 0) {
        $this->auth_type = $at;
        if($at==1){
            $this->oauth2 = new bx24oauth();
        }
    }

    protected function query($method, $url, $data = null, $jsonDecode = false){
        return bx24method::query($method, $url, $data, $jsonDecode);
    }  
    
    public function call($method, $params, $domain=''){
            if($this->auth_type == 0){
                $domain = BX24_DOMEN;
                $method = BX24_USER.'/'.BX24_TOKEN_REST.'/'.$method;                
            }else{                              
                $params['auth'] = $this->oauth2->oauth_access();
                $domain = $this->oauth2->get_token("domain");                                 
            }
            return $this->query("POST", BX24_PROTOCOL.$domain."/rest/".$method, $params, true);
    }
    
    public function route(){
        if($this->auth_type == 1){
            return $this->oauth2->route();
        }
    }
}
