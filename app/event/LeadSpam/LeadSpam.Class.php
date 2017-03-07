<?php

define ('BX24_SPAM_ID', '1');
define ('BX24_SPAM_TITLE', 'СПАМ');
define ('LEADSPAM_EVENT','ONCRMLEADUPDATE');

class LeadSpam extends bx24core{
   
    public function route() {                
        if($this->getEvent() == LEADSPAM_EVENT){
            $data = $this->getData();
            $id = (isset($data['FIELDS']) && isset($data['FIELDS']['ID'])) ? $data['FIELDS']['ID'] : 0; 
            $this->log('LEADSPAM: Event ('.$this->getEvent().') ID('.$id.')');                
            $result = $this->call("crm.lead.get", array('id' => $id));
            if(isset($result['result'])){
                $result = $result['result'];
                if($result['STATUS_ID']==BX24_SPAM_ID && !preg_match("/".BX24_SPAM_TITLE."/i",$result['TITLE'])){                
                    $newtitle = mb_substr($result['TITLE'], 0, 40,'UTF-8').' ['.BX24_SPAM_TITLE.']';                                                      
                    $params =array(
                        'id'        => $id,                    
                        'fields'    => array(		
                            "TITLE" => $newtitle,
                            "COMMENTS" => $result['TITLE'].'<br>'.$result['COMMENTS']
                        ),
                        'params'    => array( "REGISTER_SONET_EVENT" => "N" )
                    );
                    $this->call("crm.lead.update", $params);
                    $this->log('LEADSPAM: New Title('.$newtitle.') Old Tile('.$result['TITLE'].')');                
                }            
            }                        
        }
    }
}
