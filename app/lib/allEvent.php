<?php

class allEvent {
    private $events = array();
    
    public function __construct() {
        $dh  = opendir(EVENT_DIR);
        while (false !== ($filename = readdir($dh))) {
            if(is_dir(EVENT_DIR.'/'.$filename) && $filename!='.' && $filename !='..'){
                $this->events[] = new $filename();                           
            }            
        }    
    }
    
    public function route(){
        foreach($this->events as $event) {
            $event->route();
        }
    }
}
