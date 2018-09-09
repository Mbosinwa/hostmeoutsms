<?php
class hmosms extends HostmeoutSms {

    function __construct($message,$gsmnumber,$senderid){
        $this->message = $this->utilmessage($message);
        $this->gsmnumber = $this->utilgsmnumber($gsmnumber);
        $this->senderid = $this->utilsender($senderid);
    }

    function send(){
        if($this->gsmnumber == "numbererror"){
            $log[] = ("Number format error.".$this->gsmnumber);
            $error[] = ("Number format error.".$this->gsmnumber);
            return null;
        }

        $params = $this->getParams();

        
		 $api_key = $params->apiid;
         $gsmnumber=$this->gsmnumber;
	     $messages=$this->message;
		    if (empty($params->senderid)){
		      $senderid   =  $this->getSenderid();
			
			}
			else{
				$senderid = $params->senderid;
			}
		 
$action = "bulksms";
$send = "";
$type = "gsm";
$service_tag = array();
$service_tag["tag"] = $action;
$service_tag["userid"] = $params->user;
$service_tag["sender"] = $sender_id;
$service_tag["send"] = $send;
$service_tag["type"] = $type;
$service_tag["phone"] = $gsmnumber;
$service_tag["message"] = $messages;
$service_tag["apikey"] = $api_key;
$url = "https://hostmeout.com/modules/addons/hostmeout_sms/smsapi.php"; 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $service_tag);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
$api_call = curl_exec($ch); 
$json_api = json_decode($api_call, true);
//Responses
$returned_code = $json_api['success'];
$return_alert =  $json_api['alert']; //Current SMS Response
//echo $result; 
if($returned_code == '8080') {
            $log[] = ("Sucess: $return_alert.");
        } else {
            $log[] = ("Error Occured: $return_alert");
            $error[] = ("Check status, looks like problem with a connection or credentials. [Error: $return_alert] ");
        }
        return array(
            'log' => $log,
            'error' => $error,
            'msgid' => $returned_code,
        );
    }

    function balance(){
		$params = $this->getParams();
        if($params->apiid){
			
			$api_key = $params->apiid;
$action = "checkbalance";
$service_tag = array();
$service_tag["tag"] = $action;
$service_tag["userid"] = $params->user;
$service_tag["apikey"] = $api_key;
$url = "https://hostmeout.com/modules/addons/hostmeout_sms/smsapi.php"; 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $service_tag);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,true);
curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
$api_call = curl_exec($ch);
curl_close ($ch);
$json_api = json_decode($api_call, true);
//Responses
$returned_code = $json_api['success'];
$return_alert =  $json_api['alert'];
$returned_balance = $json_api['user']['balance'];
$return_client =  $json_api["user"]["name"]; //Current SMS Response
//echo $result; 
			if ($returned_code != "8080"){
				return $return_alert;
			}else{
				return $returned_balance;
			}
			
			
			
        }else{
            return null;
        }
	}
	

     function report($msgid){
        return "success";
    }

    //You can spesifically convert your gsm number. See netgsm for example
    function utilgsmnumber($number){
        return $number;
    }
    //You can spesifically convert your message
    function utilmessage($message){
        return $message;
    }
        function utilsender($senderid){
        return $senderid;
    }
}

return array(
    'value' => 'hmosms',
    'label' => 'HmoSMS',
    'fields' => array(
        'user','apiid'
    )
);