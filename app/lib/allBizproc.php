<?php

class allBizproc {
    private $bizprocs = array();
    
    public function __construct() {
        $dh  = opendir(BIZPROC_DIR);
        while (false !== ($filename = readdir($dh))) {
            if(is_dir(BIZPROC_DIR.'/'.$filename) && $filename!='.' && $filename !='..'){
                $this->bizprocs[] = new $filename();                           
            }            
        }    
    }
    
    public function install(){        
        foreach($this->bizprocs as $biz) {
            if($_REQUEST['code']==get_class($biz) && method_exists($biz,'install')){
                $biz->install();
            }
        }
    }
    
    public function delete(){
        foreach($this->bizprocs as $biz) {
            if($_REQUEST['code']==get_class($biz) && method_exists($biz,'delete')){
                $biz->delete();
            }
        }
    }    
    
    public function inputReq(){
        foreach($this->bizprocs as $biz) {
            if($_REQUEST['code']==get_class($biz) && method_exists($biz,'inputReq')){
                $biz->inputReq();
            }
        }
    }      
}
