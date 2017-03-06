<?php

class LeadSpam extends bx24core{
   
    public function route() {        
        $this->log('Event:'.$this->getEvent());
        $this->log('Data:'.print_r($this->getData(),true));
        var_dump('route');
    }
}
