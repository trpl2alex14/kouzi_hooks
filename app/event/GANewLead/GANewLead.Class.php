<?php

define ('GANEWLEAD_EVENT','ONCRMLEADUPDATE');
define ('GANEWDEAL_EVENT','ONCRMDEALADD');
define ('GANEWDEAL_EVENT_UPDATE','ONCRMDEALUPDATE');
define ('GAN_DEF_DEAL',0);

//deal
define ('GANL_DEAL_ID_ROW','UF_CRM_1486632429');
define ('GANL_DEAL_GA_ROW','UF_CRM_1489044715');
define ('GANL_DEAL_SUCCESS','WON');

//lead
define ('GANL_ROW','UF_CRM_1488890129');

//deal
define ('GANL_D_CATEGORY_OFFLINE','offline');
define ('GANL_D_CATEGORY_ONLINE','online');
define ('GANL_D_ACTION','deal');
define ('GANL_D_ACTION_S','success');
define ('GANL_D_LABEL','Сделка создана в ЦРМ');
define ('GANL_D_LABEL_S','Сделка успешно закрыта');

//lead
define ('GANL_CID_PREFIX','gacid_');
define ('GANL_CATEGORY','offline');
define ('GANL_ACTION','lead');
define ('GANL_LABEL','Звонок');

define('GANL_ART_ROW','PROPERTY_272');


class GANewLead extends bx24core{
    private $good_status = array("IN_PROCESS","CONVERTED","2","3");   
    private $source_id = array("CALL","4","5","SELF","EMAIL");   


    public function __construct() {
        parent::__construct();
        $this->setToken(GANEWLEAD_TOKEN);
    }

    private function getGACidDeal($result){
        $gacid = '';        
        if($result[GANL_DEAL_GA_ROW]!=''){
            $gacid = $result[GANL_DEAL_GA_ROW];
        }elseif($lid = $result['LEAD_ID']){                             
                $call = $this->call("crm.lead.get", array('id' => $lid));
                if(isset($call['result'])){
                    $result = $call['result'];
                    $gacid = $result[GANL_ROW];                        
                }                        
        }
        if($gacid == ''){
            $gacid = uniqid(GANL_CID_PREFIX, true);
        } 
        return $gacid;
    }

    private function successDeal(){
        $data = $this->getData();
        $id = (isset($data['FIELDS']) && isset($data['FIELDS']['ID'])) ? $data['FIELDS']['ID'] : GAN_DEF_DEAL; 
        $this->log('GANewLead: Event ('.$this->getEvent().') ID('.$id.')');                            
        $call = $this->call("crm.deal.get", array('id' => $id));
        if(!isset($call['result'])){
            return;        
        }
        $result = $call['result'];   
        if($result['STAGE_ID']==GANL_DEAL_SUCCESS && $result[GANL_DEAL_ID_ROW]!=GANL_D_ACTION_S){
            $gacid = $this->getGACidDeal($result);   
            if(preg_match("/".GANL_CID_PREFIX."/i",$gacid)){
                $category = GANL_D_CATEGORY_OFFLINE;
            }else{
                $category = GANL_D_CATEGORY_ONLINE;
            }        
            $params =array(
                'id'        => $id,                    
                'fields'    => array(		
                    GANL_DEAL_GA_ROW => $gacid,
                    GANL_DEAL_ID_ROW => GANL_D_ACTION_S
                ),
                'params'    => array( "REGISTER_SONET_EVENT" => "N" )
            );
            $this->call("crm.deal.update", $params);
            $ga = new gaApiHit();
            $ga->hit($gacid, $category, GANL_D_ACTION_S, GANL_D_LABEL_S);                    
            $this->log('GANewLead: Send Google Analitics Event:Success CID:'.$gacid.' Category:'.$category);      
        }
    }
    
    
    private function eventLead(){
        $data = $this->getData();
        $id = (isset($data['FIELDS']) && isset($data['FIELDS']['ID'])) ? $data['FIELDS']['ID'] : 0; 
        $this->log('GANewLead: Event ('.$this->getEvent().') ID('.$id.')');                            
        $result = $this->call("crm.lead.get", array('id' => $id));
        if(isset($result['result'])){
            $result = $result['result'];                 
            if(in_array($result['SOURCE_ID'], $this->source_id) && in_array($result['STATUS_ID'], $this->good_status) && $result[GANL_ROW]==''){                
                $cid = uniqid(GANL_CID_PREFIX, true);
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
                $this->log('GANewLead: Send Google Analitics Event:Lead ID('.$id.')');                
            } 

        }                                
    }

    private function eventDeal(){
        $data = $this->getData();
        $id = (isset($data['FIELDS']) && isset($data['FIELDS']['ID'])) ? $data['FIELDS']['ID'] : GAN_DEF_DEAL; 
        $this->log('GANewLead: Event ('.$this->getEvent().') ID('.$id.')');                            
        $call = $this->call("crm.deal.get", array('id' => $id));
        if(!isset($call['result'])){
            return;        
        }
        $result = $call['result'];        
        if($result[GANL_DEAL_ID_ROW]!=''){
            return;        
        }
        $gacid = $this->getGACidDeal($result);                
        if(preg_match("/".GANL_CID_PREFIX."/i",$gacid)){
            $category = GANL_D_CATEGORY_OFFLINE;
        }else{
            $category = GANL_D_CATEGORY_ONLINE;
        }        
        $params =array(
            'id'        => $id,                    
            'fields'    => array(		
                GANL_DEAL_GA_ROW => $gacid,
                GANL_DEAL_ID_ROW => $category
            ),
            'params'    => array( "REGISTER_SONET_EVENT" => "N" )
        );
        $this->call("crm.deal.update", $params);
        $ga = new gaApiHit();
        $profit = $this->getProfit();
        $ga->hit($gacid, $category, GANL_D_ACTION, GANL_D_LABEL, $profit);                    
        $this->log('GANewLead: Send Google Analitics Event:Deal CID:'.$gacid.' Category:'.$category.' Profit:'.$profit);                                        
    }
    
    private function getProductsDeal($id){        
        $buff = array();
        $products = array(); 
        $call = $this->call("crm.deal.productrows.get", array('id' => $id));
        if(isset($call['result'])){
            $index = 0;
            foreach($call['result'] as $item){
                $products[$item['PRODUCT_ID']]["price"] = $item['PRICE'];
                $products[$item['PRODUCT_ID']]["count"] = $item['QUANTITY'];
                $buff[$index] = 'crm.product.get?'.http_build_query(array("id" => $item['PRODUCT_ID']));
                $index++;
            }
            $req = array(
                "half" => 0,
                "cmd"  => $buff
            );            
            $batch = $this->call("batch", $req);
            if(isset($batch['result']) && isset($batch['result']['result'])){
                foreach($batch['result']['result'] as $item){
                    $products[$item['ID']]["articul"] = $item[GANL_ART_ROW]['value'];
                }
            }                        
        } 
        return $products;
    }

    private function getProfit(){                
        $total_price = 0;
        $total_cost = 0;
        $pd_list = $this->getProductsDeal(2040);
        if(count($pd_list)>0){
            $prices = array();
            $articul_list = array();
            foreach ($pd_list as $item) {
                $prices[$item['articul']] = array('price' => $item['price'], 'count' => $item['count']);
                $articul_list[] = $item['articul'];
            }
            $shop = shopApi::getInstance();
            $cost_list = $shop->getProducts($articul_list);
            foreach ($prices as $articul => $value) {
                $total_cost += ((isset($cost_list[$articul]) && $cost_list[$articul]['cost']>0)? $cost_list[$articul]['cost'] : $value['price'])*$value['count'];
                $total_price += $value['price']*$value['count'];
            }            
        }               
        return $total_price-$total_cost;
    }

    public function route() {   
        switch ($this->getEvent()){
            case GANEWLEAD_EVENT:
                $this->eventLead();
            break;
            case GANEWDEAL_EVENT:
                $this->eventDeal();
            break;                
            case GANEWDEAL_EVENT_UPDATE:
                $this->successDeal();
            break;            
            case 'ERROR':
                var_dump($this->getProfit());
            break;           
        }
    }    
}
