<?php

class bx24oauth {
    protected $json_data;         
    
    public function __construct(){ 
        $this->json_data='';            
    }
    
    protected function query($method, $url, $data = null, $jsonDecode = false){
        return bx24method::query($method, $url, $data, $jsonDecode);
    }
    
    protected function redirect($url){
            Header("HTTP 302 Found");
            Header("Location: ".$url);
            die();
    }        

    protected function save_token($query_data){
        $query_data["ts"] = time();
        $json_text = json_encode($query_data);
        file_put_contents(TOKEN_FILE,$json_text);        
    }

    protected function load_token($var=NULL){        
        if(is_file(TOKEN_FILE)){
            $json = file_get_contents(TOKEN_FILE);
            $this->json_data = json_decode($json, TRUE);
            if($var){
                return $this->json_data[$var];
            }else{
                return NULL;
            }
        }else {
            trigger_error('Ошибка:'.TOKEN_FILE. ' - отсутствует ключ CRM');
            die();
        }
        return NULL;
    }

    public function get_token($var){        
        if($this->json_data == ''){ 
          return $this->load_token($var);
        }else{    
            return $this->json_data[$var];        
        }
    }    
    
    protected function oauth_init(){
        $domain = BX24_DOMEN;
        $params = array(
           "response_type" => "code",
           "client_id" => CLIENT_ID,
           "redirect_uri" => REDIRECT_URI,
        );
        $path = "/oauth/authorize/";
        $this->redirect(BX24_PROTOCOL.$domain.$path."?".http_build_query($params));  
        die();
    } 
    
    protected function oauth_code (){
        $code      = htmlspecialchars($_REQUEST["code"]);
        $domain    = htmlspecialchars($_REQUEST["domain"]);
        $params = array(
           "grant_type"    => "authorization_code",
           "client_id"     => CLIENT_ID,
           "client_secret" => CLIENT_SECRET,
           "redirect_uri"  => REDIRECT_URI,
           "scope"         => SCOPE,
           "code"          => $code
        ); 
        $path = "/oauth/token/";        
        $query_data = $this->query("GET", BX24_PROTOCOL.$domain.$path, $params, true);
        var_dump($query_data);        
        if(isset($query_data["access_token"]))   { 
             $this->save_token($query_data); 
             return $query_data["access_token"];
        }
        return false;
    }

    protected function oauth_refresh() {
        $this->load_token();
        $params = array(
                "grant_type"    => "refresh_token",
                "client_id"     => CLIENT_ID,
                "client_secret" => CLIENT_SECRET,
                "redirect_uri"  => REDIRECT_URI,
                "scope"         => SCOPE,
                "refresh_token" => $this->get_token("refresh_token")
        );       
        $domain = $this->get_token("domain");
        $path = "/oauth/token/";
        $query_data = $this->query("GET", BX24_PROTOCOL.$domain.$path, $params, true);

        if(isset($query_data["access_token"])){		
                $this->save_token($query_data);
                return $query_data["access_token"];                
        }else{
            return false;
        }
    }    
    
    public function oauth_access(){
        $this->load_token();
        if(time() > $this->get_token("ts") + $this->get_token("expires_in") + 30){
            return $this->oauth_refresh();
        }else{
            return $this->get_token("access_token");
        }
    }

    public function route(){
        if(isset($_REQUEST["code"])){
           if($this->oauth_code()){
                $this->redirect(REDIRECT_URI);      
                die();       
           }
        }elseif(isset($_REQUEST["refresh"])){
            if($this->oauth_refresh()){
                $this->redirect(REDIRECT_URI);
                die();        
            }	
        }        
        $data = '';
        $action = isset($_REQUEST["action"]) ? htmlspecialchars($_REQUEST["action"]) : "";                           
        switch($action){
            case 'init':
                $this->oauth_init();
            break;                    
            case 'status':
                $data = $this->getStatus();
            break;        
        }       
        return $data;
    }    
    
    public function getStatus(){        
        return array($this->oauth_access(),date('c',time()),date('c',($this->load_token("ts")+$this->load_token("expires_in")+30)));
    }     
}
