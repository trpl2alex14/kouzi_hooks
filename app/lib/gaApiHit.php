<?php
define('GA_HOST', 'https://www.google-analytics.com');
define('GA_METHOD', 'collect');

class gaApiHit {

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
    
    protected function call($params){
            return $this->query("POST", GA_HOST."/".GA_METHOD, $params, true);
    }    
    
    public function hit($cid,$category,$action,$label, $index=NULL) {
        $data = array(
            'v'  => '1',
            'tid'=> GA_TRACER,
            'cid'=>$cid,
            't'=>'event',
            'ec'=>$category,
            'ea'=>$action,
            'el'=>$label
        );
        if($index){
            $data['ev']=$index;
        }
        $this->call($data);
    }
}
