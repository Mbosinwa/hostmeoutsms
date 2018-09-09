<?php
$hook = array(
    'hook' => 'ClientAdd',
    'function' => 'ClientAdd_admin',
    'type' => 'admin',
    'extra' => '',
    'defaultmessage' => 'A new customer  has Register on Hostmeout.',
    'variables' => ''
);
if(!function_exists('ClientAdd_admin')){
    function ClientAdd_admin($args){
        $class = new HostmeoutSms();
        $template = $class->getTemplateDetails(__FUNCTION__);
        if($template['active'] == 0){
            return null;
        }
        $settings = $class->getSettings();
        if(!$settings['api'] || !$settings['apiparams'] || !$settings['gsmnumberfield'] || !$settings['wantsmsfield']){
            return null;
        }
        $admingsm = explode(",",$template['admingsm']);

        foreach($admingsm as $gsm){
            if(!empty($gsm)){
                $class->setGsmnumber(trim($gsm));
                $class->setUserid(0);
                $class->setMessage($template['template']);
                $class->send();
            }
        }
    }
}
return $hook;