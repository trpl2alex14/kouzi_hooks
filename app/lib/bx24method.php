<?php

class bx24method {
    public function query($method, $url, $data = null, $jsonDecode = false){        
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
}
