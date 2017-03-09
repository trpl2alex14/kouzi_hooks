<?php

define ('GANEWLEAD_EVENT','ONCRMLEADUPDATE');
define ('GANEWDEAL_EVENT','ONCRMDEALADD');
define ('GANL_ROW','UF_CRM_1488890129');
define ('GANL_SOURCE','CALL');

define ('GANL_CATEGORY','offline');
define ('GANL_ACTION','lead');
define ('GANL_LABEL','Звонок');


class GANewLead extends bx24core{
    private $good_status = array("IN_PROCESS","CONVERTED","2","3");   
    
    public function __construct() {
        parent::__construct();
        $this->setToken(GANEWLEAD_TOKEN);
    }

    private function eventLead(){
        $data = $this->getData();
        $id = (isset($data['FIELDS']) && isset($data['FIELDS']['ID'])) ? $data['FIELDS']['ID'] : 0; 
        $this->log('GANewLead: Event ('.$this->getEvent().') ID('.$id.')');                            
        $result = $this->call("crm.lead.get", array('id' => $id));
        if(isset($result['result'])){
            $result = $result['result'];                  
            if( $result['SOURCE_ID']==GANL_SOURCE && in_array($result['STATUS_ID'], $this->good_status) && $result[GANL_ROW]==''){                
                $cid = uniqid("gacid_", true);
                $params =array(
                    'id'        => $id,                    
                    'fields'    => array(		
                        GANL_ROW => $cid
                    ),
                    'params'    => array( "REGISTER_SONET_EVENT" => "N" )
                );
                $this->call("crm.lead.update", $params);
                $ga = new gaApiHit();
                $ga->hit($cid, GANL_CATEGORY, GANL_ACTION, GANL_LABEL);                    
                $this->log('GANewLead: Send Google Analitics Event');                
            } 

        }                                
    }

    private function eventDeal(){
        
    }

    public function route() {   
        switch ($this->getEvent()){
            case GANEWLEAD_EVENT:
                $this->eventLead();
            break;
            case GANEWDEAL_EVENT:
                $this->eventDeal();
            break;
        
        }
    }    
}
